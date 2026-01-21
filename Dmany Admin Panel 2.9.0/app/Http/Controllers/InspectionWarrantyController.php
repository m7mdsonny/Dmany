<?php

namespace App\Http\Controllers;

use App\Models\InspectionConfiguration;
use App\Models\InspectionOrder;
use App\Models\InspectionReport;
use App\Models\InspectionReportImage;
use App\Models\WarrantyClaim;
use App\Models\WarrantyClaimImage;
use App\Models\InspectionAuditLog;
use App\Models\Item;
use App\Models\User;
use App\Services\FileService;
use App\Services\HelperService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * InspectionWarrantyController
 * 
 * Core controller for managing Inspection & Warranty service
 * This is a critical business module with full admin control
 */
class InspectionWarrantyController extends Controller
{
    /**
     * 2️⃣ Global Settings Page
     * Inspection & Warranty Settings
     */
    public function settingsIndex()
    {
        ResponseService::noPermissionThenRedirect('settings-update');
        
        $config = InspectionConfiguration::getConfiguration();
        
        return view('inspection-warranty.settings', compact('config'));
    }

    /**
     * Update global settings
     */
    public function settingsUpdate(Request $request)
    {
        ResponseService::noPermissionThenSendJson('settings-update');

        $validator = Validator::make($request->all(), [
            'fee_percentage' => 'required|numeric|min:0|max:100',
            'warranty_duration' => 'required|integer|min:1|max:365',
            'service_description' => 'nullable|string',
            'workflow_steps' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'covered_items' => 'nullable|array',
            'excluded_items' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return ResponseService::validationErrorResponse($validator->errors());
        }

        try {
            $config = InspectionConfiguration::getConfiguration();
            $config->update($validator->validated());

            // Log admin action
            InspectionAuditLog::logAction(
                0,
                Auth::id(),
                'settings_update',
                'Updated inspection & warranty global settings',
                $config->getOriginal(),
                $config->getAttributes()
            );

            return ResponseService::successResponse('Settings updated successfully');
        } catch (\Exception $e) {
            return ResponseService::errorResponse($e->getMessage());
        }
    }

    /**
     * 3️⃣ Inspection Orders Management
     * List all inspection orders
     */
    public function ordersIndex(Request $request)
    {
        ResponseService::noPermissionThenRedirect('inspection-order-list');

        return view('inspection-warranty.orders');
    }

    /**
     * Get orders data (AJAX)
     */
    public function ordersData(Request $request)
    {
        ResponseService::noPermissionThenSendJson('inspection-order-list');

        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');
        $search = $request->input('search', '');
        $status = $request->input('status', '');

        $query = InspectionOrder::with([
            'item:id,name,image',
            'buyer:id,name,profile',
            'seller:id,name,profile',
            'assignedTechnician:id,name',
            'inspectionReport:id,inspection_order_id,final_decision,condition_score,grade'
        ]);

        // Apply filters
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('item', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('buyer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $total = $query->count();
        $orders = $query->orderBy($sort, $order)
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json([
            'total' => $total,
            'rows' => $orders->map(function($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'item_name' => $order->item->name ?? 'N/A',
                    'item_image' => $order->item->image ?? '',
                    'buyer_name' => $order->buyer->name ?? 'N/A',
                    'seller_name' => $order->seller->name ?? 'N/A',
                    'status' => $order->status,
                    'device_price' => number_format($order->device_price, 2),
                    'inspection_fee' => number_format($order->inspection_fee, 2),
                    'total_amount' => number_format($order->total_amount, 2),
                    'assigned_technician' => $order->assignedTechnician->name ?? 'Unassigned',
                    'final_decision' => $order->inspectionReport->final_decision ?? null,
                    'condition_score' => $order->inspectionReport->condition_score ?? null,
                    'grade' => $order->inspectionReport->grade ?? null,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }

    /**
     * 4️⃣ Inspection Details Page (Per Order)
     * Show detailed inspection order view
     */
    public function orderDetail($id)
    {
        ResponseService::noPermissionThenRedirect('inspection-order-view');

        $order = InspectionOrder::with([
            'item',
            'buyer',
            'seller',
            'assignedTechnician',
            'inspectionReport.images',
            'inspectionReport.decisionBy',
            'warrantyClaims.images',
            'auditLogs.user'
        ])->findOrFail($id);

        // Get all available technicians (staff/admins)
        $technicians = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Admin', 'Staff', 'Technician']);
        })->get(['id', 'name', 'email']);

        return view('inspection-warranty.order-detail', compact('order', 'technicians'));
    }

    /**
     * Assign technician to order
     */
    public function assignTechnician(Request $request, $id)
    {
        ResponseService::noPermissionThenSendJson('inspection-order-update');

        $validator = Validator::make($request->all(), [
            'technician_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return ResponseService::validationErrorResponse($validator->errors());
        }

        try {
            $order = InspectionOrder::findOrFail($id);
            $oldTechnicianId = $order->assigned_technician_id;

            $order->update([
                'assigned_technician_id' => $request->technician_id,
                'status' => $order->status === 'pending' ? 'under_inspection' : $order->status,
            ]);

            // Log action
            InspectionAuditLog::logAction(
                $id,
                Auth::id(),
                'technician_assigned',
                'Assigned technician to inspection order',
                ['assigned_technician_id' => $oldTechnicianId],
                ['assigned_technician_id' => $request->technician_id]
            );

            return ResponseService::successResponse('Technician assigned successfully');
        } catch (\Exception $e) {
            return ResponseService::errorResponse($e->getMessage());
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        ResponseService::noPermissionThenSendJson('inspection-order-update');

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,device_received,under_inspection,passed,failed,delivered,warranty_active,warranty_expired,cancelled',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return ResponseService::validationErrorResponse($validator->errors());
        }

        try {
            $order = InspectionOrder::findOrFail($id);
            $oldStatus = $order->status;

            // Auto-set dates based on status
            $updates = ['status' => $request->status];
            
            if ($request->status === 'device_received' && !$order->device_received_at) {
                $updates['device_received_at'] = now();
            }
            if ($request->status === 'under_inspection' && !$order->inspection_date) {
                $updates['inspection_date'] = now();
            }
            if ($request->status === 'delivered' && !$order->delivery_date) {
                $updates['delivery_date'] = now();
            }
            if ($request->status === 'warranty_active' && !$order->warranty_start_date) {
                $updates['warranty_start_date'] = now();
                $updates['warranty_end_date'] = $order->calculateWarrantyEndDate()->format('Y-m-d');
            }

            if ($request->notes) {
                $updates['admin_notes'] = $request->notes;
            }

            $order->update($updates);

            // Log action
            InspectionAuditLog::logAction(
                $id,
                Auth::id(),
                'status_changed',
                "Changed order status from {$oldStatus} to {$request->status}",
                ['status' => $oldStatus],
                ['status' => $request->status],
                $request->notes
            );

            return ResponseService::successResponse('Status updated successfully');
        } catch (\Exception $e) {
            return ResponseService::errorResponse($e->getMessage());
        }
    }

    /**
     * Submit/Update inspection report
     */
    public function submitReport(Request $request, $id)
    {
        ResponseService::noPermissionThenSendJson('inspection-report-update');

        $validator = Validator::make($request->all(), [
            'battery_health' => 'nullable|integer|min:0|max:100',
            'screen_condition' => 'nullable|in:excellent,good,fair,poor',
            'camera_condition' => 'nullable|in:excellent,good,fair,poor',
            'speaker_status' => 'nullable|in:working,partial,not_working',
            'network_status' => 'nullable|in:working,partial,not_working',
            'condition_score' => 'nullable|integer|min:1|max:10',
            'grade' => 'nullable|in:A,B,C,D,Fail',
            'technician_notes' => 'nullable|string',
            'checklist_results' => 'nullable|array',
            'final_decision' => 'required|in:pass,fail',
            'decision_notes' => 'nullable|string',
            'images.*' => 'nullable|image|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return ResponseService::validationErrorResponse($validator->errors());
        }

        try {
            DB::beginTransaction();

            $order = InspectionOrder::findOrFail($id);

            // Create or update inspection report
            $report = InspectionReport::updateOrCreate(
                ['inspection_order_id' => $id],
                [
                    'battery_health' => $request->battery_health,
                    'screen_condition' => $request->screen_condition,
                    'camera_condition' => $request->camera_condition,
                    'speaker_status' => $request->speaker_status,
                    'network_status' => $request->network_status,
                    'condition_score' => $request->condition_score,
                    'grade' => $request->grade,
                    'technician_notes' => $request->technician_notes,
                    'checklist_results' => $request->checklist_results ?? [],
                    'final_decision' => $request->final_decision,
                    'decision_date' => now(),
                    'decision_by' => Auth::id(),
                    'decision_notes' => $request->decision_notes,
                ]
            );

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store("inspection-reports/{$report->id}", 'public');
                    
                    InspectionReportImage::create([
                        'inspection_report_id' => $report->id,
                        'image_url' => Storage::url($path),
                        'image_type' => $request->input("image_types.{$index}", 'physical'),
                        'sort_order' => $index,
                    ]);
                }
            }

            // Update order status based on decision
            $newStatus = $request->final_decision === 'pass' ? 'passed' : 'failed';
            $order->update([
                'status' => $newStatus,
                'inspection_date' => now(),
            ]);

            // Log action
            InspectionAuditLog::logAction(
                $id,
                Auth::id(),
                'inspection_report_submitted',
                "Submitted inspection report with decision: {$request->final_decision}",
                null,
                [
                    'final_decision' => $request->final_decision,
                    'condition_score' => $request->condition_score,
                    'grade' => $request->grade,
                ],
                $request->decision_notes
            );

            DB::commit();
            return ResponseService::successResponse('Inspection report submitted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseService::errorResponse($e->getMessage());
        }
    }

    /**
     * 5️⃣ Warranty Management Page
     * View active and expired warranties
     */
    public function warrantiesIndex(Request $request)
    {
        ResponseService::noPermissionThenRedirect('warranty-list');

        $status = $request->input('status', 'active'); // active, expired, all

        return view('inspection-warranty.warranties', compact('status'));
    }

    /**
     * Get warranties data (AJAX)
     */
    public function warrantiesData(Request $request)
    {
        ResponseService::noPermissionThenSendJson('warranty-list');

        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $status = $request->input('status', 'active');

        $query = InspectionOrder::with([
            'item:id,name,image',
            'buyer:id,name',
            'inspectionReport:id,inspection_order_id,condition_score,grade'
        ])->whereIn('status', ['warranty_active', 'warranty_expired']);

        if ($status === 'active') {
            $query->where('status', 'warranty_active');
        } elseif ($status === 'expired') {
            $query->where('status', 'warranty_expired');
        }

        $total = $query->count();
        $orders = $query->orderBy('warranty_end_date', 'ASC')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json([
            'total' => $total,
            'rows' => $orders->map(function($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'item_name' => $order->item->name ?? 'N/A',
                    'buyer_name' => $order->buyer->name ?? 'N/A',
                    'warranty_start_date' => $order->warranty_start_date?->format('Y-m-d'),
                    'warranty_end_date' => $order->warranty_end_date?->format('Y-m-d'),
                    'remaining_days' => $order->getRemainingWarrantyDays(),
                    'status' => $order->status,
                    'condition_score' => $order->inspectionReport->condition_score ?? null,
                    'grade' => $order->inspectionReport->grade ?? null,
                ];
            })
        ]);
    }

    /**
     * 6️⃣ Warranty Claims & Disputes
     * View warranty claims
     */
    public function claimsIndex(Request $request)
    {
        ResponseService::noPermissionThenRedirect('warranty-claim-list');

        return view('inspection-warranty.claims');
    }

    /**
     * Get claims data (AJAX)
     */
    public function claimsData(Request $request)
    {
        ResponseService::noPermissionThenSendJson('warranty-claim-list');

        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $status = $request->input('status', '');

        $query = WarrantyClaim::with([
            'inspectionOrder.item:id,name',
            'user:id,name',
            'resolvedBy:id,name'
        ]);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $total = $query->count();
        $claims = $query->orderBy('created_at', 'DESC')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json([
            'total' => $total,
            'rows' => $claims->map(function($claim) {
                return [
                    'id' => $claim->id,
                    'claim_number' => $claim->claim_number,
                    'order_number' => $claim->inspectionOrder->order_number ?? 'N/A',
                    'item_name' => $claim->inspectionOrder->item->name ?? 'N/A',
                    'user_name' => $claim->user->name ?? 'N/A',
                    'description' => $claim->description,
                    'status' => $claim->status,
                    'admin_response' => $claim->admin_response,
                    'decision_outcome' => $claim->decision_outcome,
                    'refund_amount' => $claim->refund_amount ? number_format($claim->refund_amount, 2) : null,
                    'resolved_by' => $claim->resolvedBy->name ?? null,
                    'resolved_at' => $claim->resolved_at?->format('Y-m-d H:i:s'),
                    'created_at' => $claim->created_at->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }

    /**
     * View claim detail
     */
    public function claimDetail($id)
    {
        ResponseService::noPermissionThenRedirect('warranty-claim-view');

        $claim = WarrantyClaim::with([
            'inspectionOrder.item',
            'inspectionOrder.inspectionReport',
            'user',
            'resolvedBy',
            'images'
        ])->findOrFail($id);

        return view('inspection-warranty.claim-detail', compact('claim'));
    }

    /**
     * Resolve warranty claim
     */
    public function resolveClaim(Request $request, $id)
    {
        ResponseService::noPermissionThenSendJson('warranty-claim-update');

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected,resolved',
            'admin_response' => 'required|string',
            'decision_outcome' => 'required|in:full_refund,partial_refund,repair,replacement,rejected,no_action',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ResponseService::validationErrorResponse($validator->errors());
        }

        try {
            DB::beginTransaction();

            $claim = WarrantyClaim::findOrFail($id);
            $oldStatus = $claim->status;

            $claim->update([
                'status' => $request->status,
                'admin_response' => $request->admin_response,
                'decision_outcome' => $request->decision_outcome,
                'refund_amount' => $request->refund_amount,
                'resolved_by' => Auth::id(),
                'resolved_at' => now(),
            ]);

            // Log action
            InspectionAuditLog::logAction(
                $claim->inspection_order_id,
                Auth::id(),
                'warranty_claim_resolved',
                "Resolved warranty claim: {$request->decision_outcome}",
                ['status' => $oldStatus],
                [
                    'status' => $request->status,
                    'decision_outcome' => $request->decision_outcome,
                    'refund_amount' => $request->refund_amount,
                ],
                $request->admin_response
            );

            DB::commit();
            return ResponseService::successResponse('Claim resolved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseService::errorResponse($e->getMessage());
        }
    }

    /**
     * Delete inspection order image
     */
    public function deleteReportImage($reportId, $imageId)
    {
        ResponseService::noPermissionThenSendJson('inspection-report-update');

        try {
            $image = InspectionReportImage::where('inspection_report_id', $reportId)
                ->findOrFail($imageId);

            // Delete file
            if (Storage::disk('public')->exists(str_replace('/storage/', '', $image->image_url))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $image->image_url));
            }

            $image->delete();

            return ResponseService::successResponse('Image deleted successfully');
        } catch (\Exception $e) {
            return ResponseService::errorResponse($e->getMessage());
        }
    }
}

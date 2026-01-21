# Inspection & Warranty - Admin Panel Documentation

This document outlines the backend implementation requirements for the Inspection & Warranty feature in the Laravel admin panel.

## 📋 Table of Contents
1. [Database Schema](#database-schema)
2. [API Endpoints](#api-endpoints)
3. [Admin Routes](#admin-routes)
4. [Controller Structure](#controller-structure)
5. [Admin Views Structure](#admin-views-structure)
6. [Data Models](#data-models)

---

## 🗄️ Database Schema

### Migration: `create_inspection_orders_table`

```php
Schema::create('inspection_orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_number')->unique();
    $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
    $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
    $table->decimal('device_price', 10, 2);
    $table->decimal('inspection_fee', 10, 2);
    $table->decimal('total_amount', 10, 2);
    $table->enum('status', ['pending', 'under_inspection', 'approved', 'rejected', 'delivered', 'warranty_active', 'warranty_expired'])->default('pending');
    $table->foreignId('assigned_technician_id')->nullable()->constrained('users')->onDelete('set null');
    $table->dateTime('inspection_date')->nullable();
    $table->dateTime('delivery_date')->nullable();
    $table->date('warranty_start_date')->nullable();
    $table->date('warranty_end_date')->nullable();
    $table->integer('warranty_duration')->default(5); // days
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->index(['status', 'created_at']);
    $table->index(['buyer_id', 'status']);
    $table->index(['assigned_technician_id']);
});
```

### Migration: `create_inspection_reports_table`

```php
Schema::create('inspection_reports', function (Blueprint $table) {
    $table->id();
    $table->foreignId('inspection_order_id')->constrained('inspection_orders')->onDelete('cascade');
    $table->integer('condition_score')->nullable(); // 1-10
    $table->enum('grade', ['A', 'B', 'C', 'D'])->nullable();
    $table->integer('battery_health')->nullable(); // percentage
    $table->json('checklist_results')->nullable(); // JSON array of checklist items
    $table->text('technician_notes')->nullable();
    $table->enum('final_decision', ['pass', 'fail'])->nullable();
    $table->string('report_url')->nullable(); // PDF report URL
    $table->timestamps();
    
    $table->index('inspection_order_id');
});
```

### Migration: `create_inspection_report_images_table`

```php
Schema::create('inspection_report_images', function (Blueprint $table) {
    $table->id();
    $table->foreignId('inspection_report_id')->constrained('inspection_reports')->onDelete('cascade');
    $table->string('image_url');
    $table->string('image_type')->nullable(); // 'diagnostic', 'physical', 'screen', etc.
    $table->text('caption')->nullable();
    $table->integer('sort_order')->default(0);
    $table->timestamps();
    
    $table->index('inspection_report_id');
});
```

### Migration: `create_warranty_claims_table`

```php
Schema::create('warranty_claims', function (Blueprint $table) {
    $table->id();
    $table->foreignId('inspection_order_id')->constrained('inspection_orders')->onDelete('cascade');
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // buyer
    $table->string('claim_number')->unique();
    $table->text('description');
    $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'resolved'])->default('pending');
    $table->text('admin_response')->nullable();
    $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
    $table->dateTime('resolved_at')->nullable();
    $table->timestamps();
    
    $table->index(['status', 'created_at']);
    $table->index('user_id');
});
```

### Migration: `create_warranty_claim_images_table`

```php
Schema::create('warranty_claim_images', function (Blueprint $table) {
    $table->id();
    $table->foreignId('warranty_claim_id')->constrained('warranty_claims')->onDelete('cascade');
    $table->string('image_url');
    $table->timestamps();
    
    $table->index('warranty_claim_id');
});
```

### Migration: `create_inspection_configurations_table`

```php
Schema::create('inspection_configurations', function (Blueprint $table) {
    $table->id();
    $table->decimal('fee_percentage', 5, 2)->default(4.00); // 4%
    $table->integer('warranty_duration')->default(5); // days
    $table->text('terms_conditions')->nullable(); // Rich text/HTML
    $table->json('covered_items')->nullable(); // Array of covered issues
    $table->json('excluded_items')->nullable(); // Array of excluded issues
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

---

## 🔌 API Endpoints

### Public/Frontend Endpoints

#### `GET /api/get-inspection-config`
Get inspection service configuration
- **Response**: Fee percentage, warranty duration, terms, covered/excluded items

#### `GET /api/get-inspection-order`
Get inspection order details
- **Params**: `item_id`, `order_id` (optional)
- **Response**: Order details with status, report if available

#### `POST /api/create-inspection-order`
Create new inspection order
- **Body**: `item_id`
- **Response**: Created order with payment details

#### `GET /api/get-inspection-report`
Get inspection report
- **Params**: `order_id`
- **Response**: Full inspection report with images

#### `GET /api/get-warranty-claims`
Get user's warranty claims
- **Params**: `page`
- **Response**: List of warranty claims

#### `POST /api/create-warranty-claim`
Create warranty claim
- **Body**: `order_id`, `description`, `images[]`
- **Response**: Created claim details

---

## 🛣️ Admin Routes

Add to `routes/web.php` (admin routes):

```php
// Inspection & Warranty Management
Route::prefix('admin/inspection-warranty')->name('admin.inspection-warranty.')->group(function () {
    // Configuration
    Route::get('configuration', [InspectionWarrantyController::class, 'configuration'])->name('configuration');
    Route::post('configuration/update', [InspectionWarrantyController::class, 'updateConfiguration'])->name('configuration.update');
    
    // Orders Management
    Route::get('orders', [InspectionWarrantyController::class, 'orders'])->name('orders');
    Route::get('orders/{id}', [InspectionWarrantyController::class, 'orderDetail'])->name('order.detail');
    Route::post('orders/{id}/assign-technician', [InspectionWarrantyController::class, 'assignTechnician'])->name('order.assign-technician');
    Route::post('orders/{id}/update-status', [InspectionWarrantyController::class, 'updateOrderStatus'])->name('order.update-status');
    
    // Inspection Reports
    Route::get('orders/{orderId}/report', [InspectionWarrantyController::class, 'reportForm'])->name('report.form');
    Route::post('orders/{orderId}/report', [InspectionWarrantyController::class, 'submitReport'])->name('report.submit');
    Route::get('orders/{orderId}/report/edit', [InspectionWarrantyController::class, 'editReport'])->name('report.edit');
    
    // Warranty Claims
    Route::get('claims', [InspectionWarrantyController::class, 'claims'])->name('claims');
    Route::get('claims/{id}', [InspectionWarrantyController::class, 'claimDetail'])->name('claim.detail');
    Route::post('claims/{id}/resolve', [InspectionWarrantyController::class, 'resolveClaim'])->name('claim.resolve');
});
```

---

## 🎮 Controller Structure

### Controller: `InspectionWarrantyController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InspectionOrder;
use App\Models\InspectionReport;
use App\Models\InspectionConfiguration;
use App\Models\WarrantyClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InspectionWarrantyController extends Controller
{
    /**
     * A) Inspection Service Configuration
     */
    public function configuration()
    {
        $config = InspectionConfiguration::firstOrCreate([]);
        return view('admin.inspection-warranty.configuration', compact('config'));
    }

    public function updateConfiguration(Request $request)
    {
        $config = InspectionConfiguration::firstOrCreate([]);
        
        $validated = $request->validate([
            'fee_percentage' => 'required|numeric|min:0|max:100',
            'warranty_duration' => 'required|integer|min:1|max:365',
            'terms_conditions' => 'nullable|string',
            'covered_items' => 'nullable|array',
            'excluded_items' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $config->update($validated);
        
        return redirect()->back()->with('success', 'Configuration updated successfully');
    }

    /**
     * B) Inspection Orders Management
     */
    public function orders(Request $request)
    {
        $query = InspectionOrder::with(['item', 'buyer', 'seller', 'assignedTechnician', 'inspectionReport'])
            ->latest();

        // Filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('item', function($q) use ($request) {
                      $q->where('name', 'like', "%{$request->search}%");
                  });
            });
        }

        $orders = $query->paginate(20);

        return view('admin.inspection-warranty.orders', compact('orders'));
    }

    public function orderDetail($id)
    {
        $order = InspectionOrder::with([
            'item', 'buyer', 'seller', 'assignedTechnician',
            'inspectionReport.images', 'warrantyClaims'
        ])->findOrFail($id);

        return view('admin.inspection-warranty.order-detail', compact('order'));
    }

    public function assignTechnician(Request $request, $id)
    {
        $order = InspectionOrder::findOrFail($id);
        
        $validated = $request->validate([
            'technician_id' => 'required|exists:users,id',
        ]);

        $order->update([
            'assigned_technician_id' => $validated['technician_id'],
            'status' => 'under_inspection',
        ]);

        return redirect()->back()->with('success', 'Technician assigned successfully');
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = InspectionOrder::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,under_inspection,approved,rejected,delivered,warranty_active',
            'notes' => 'nullable|string',
        ]);

        $order->update($validated);

        return redirect()->back()->with('success', 'Order status updated successfully');
    }

    /**
     * C) Inspection Report Management
     */
    public function reportForm($orderId)
    {
        $order = InspectionOrder::with(['item', 'inspectionReport'])->findOrFail($orderId);
        return view('admin.inspection-warranty.report-form', compact('order'));
    }

    public function submitReport(Request $request, $orderId)
    {
        $order = InspectionOrder::findOrFail($orderId);

        $validated = $request->validate([
            'condition_score' => 'required|integer|min:1|max:10',
            'grade' => 'required|in:A,B,C,D',
            'battery_health' => 'nullable|integer|min:0|max:100',
            'checklist_results' => 'nullable|array',
            'technician_notes' => 'nullable|string',
            'final_decision' => 'required|in:pass,fail',
            'images.*' => 'nullable|image|max:5120', // 5MB max
        ]);

        // Create or update inspection report
        $report = InspectionReport::updateOrCreate(
            ['inspection_order_id' => $orderId],
            [
                'condition_score' => $validated['condition_score'],
                'grade' => $validated['grade'],
                'battery_health' => $validated['battery_health'],
                'checklist_results' => json_encode($validated['checklist_results'] ?? []),
                'technician_notes' => $validated['technician_notes'],
                'final_decision' => $validated['final_decision'],
            ]
        );

        // Handle images upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store("inspection-reports/{$report->id}", 'public');
                $report->images()->create([
                    'image_url' => Storage::url($path),
                    'image_type' => $request->input("image_types.{$index}", 'physical'),
                    'sort_order' => $index,
                });
            }
        }

        // Generate PDF report (implement as needed)
        // $reportUrl = $this->generatePdfReport($report);
        // $report->update(['report_url' => $reportUrl]);

        // Update order status based on decision
        $order->update([
            'status' => $validated['final_decision'] === 'pass' ? 'approved' : 'rejected',
            'inspection_date' => now(),
        ]);

        return redirect()->route('admin.inspection-warranty.order.detail', $orderId)
            ->with('success', 'Inspection report submitted successfully');
    }

    /**
     * D) Warranty Tracking & Claims
     */
    public function claims(Request $request)
    {
        $query = WarrantyClaim::with(['inspectionOrder.item', 'user', 'resolvedBy'])
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $claims = $query->paginate(20);

        return view('admin.inspection-warranty.claims', compact('claims'));
    }

    public function claimDetail($id)
    {
        $claim = WarrantyClaim::with([
            'inspectionOrder.item', 'user', 'resolvedBy', 'images'
        ])->findOrFail($id);

        return view('admin.inspection-warranty.claim-detail', compact('claim'));
    }

    public function resolveClaim(Request $request, $id)
    {
        $claim = WarrantyClaim::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,resolved',
            'admin_response' => 'required|string',
        ]);

        $claim->update([
            'status' => $validated['status'],
            'admin_response' => $validated['admin_response'],
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Claim resolved successfully');
    }
}
```

---

## 🎨 Admin Views Structure

### Directory Structure
```
resources/views/admin/inspection-warranty/
├── configuration.blade.php      # Service configuration page
├── orders/
│   ├── index.blade.php          # Orders list
│   └── detail.blade.php         # Order detail page
├── reports/
│   ├── form.blade.php           # Report submission form
│   └── edit.blade.php           # Edit report
└── claims/
    ├── index.blade.php          # Claims list
    └── detail.blade.php         # Claim detail & resolution
```

---

## 📊 Data Models

### Model: `InspectionOrder.php`
```php
// Relationships:
- belongsTo Item
- belongsTo Buyer (User)
- belongsTo Seller (User)
- belongsTo AssignedTechnician (User)
- hasOne InspectionReport
- hasMany WarrantyClaims
```

### Model: `InspectionReport.php`
```php
// Relationships:
- belongsTo InspectionOrder
- hasMany Images (InspectionReportImage)
```

### Model: `WarrantyClaim.php`
```php
// Relationships:
- belongsTo InspectionOrder
- belongsTo User (buyer)
- belongsTo ResolvedBy (User, admin)
- hasMany Images (WarrantyClaimImage)
```

---

## 📝 Implementation Notes

1. **Payment Integration**: Integrate with existing payment gateway to handle secured payments
2. **PDF Report Generation**: Use libraries like `barryvdh/laravel-dompdf` for generating inspection reports
3. **File Storage**: Store inspection images and PDFs in `storage/app/public/inspection-reports/`
4. **Notifications**: Send email/SMS notifications on status changes
5. **Permissions**: Ensure proper admin permissions for each section
6. **Audit Trail**: Log all admin actions for audit purposes

---

## 🔐 Permissions Required

- `inspection-warranty.view` - View orders/reports/claims
- `inspection-warranty.manage` - Manage orders (assign technician, update status)
- `inspection-warranty.report` - Submit/edit inspection reports
- `inspection-warranty.config` - Manage service configuration
- `inspection-warranty.resolve` - Resolve warranty claims

---

## 📱 Frontend Integration

The frontend (Next.js) expects the following API response structures:

### Inspection Config Response:
```json
{
  "error": false,
  "data": {
    "fee_percentage": 4.00,
    "warranty_duration": 5,
    "terms_conditions": "<p>...</p>",
    "covered_items": ["...", "..."],
    "excluded_items": ["...", "..."]
  }
}
```

### Inspection Order Response:
```json
{
  "error": false,
  "data": {
    "id": 1,
    "order_number": "IW-2025-001",
    "status": "under_inspection",
    "device_price": 500.00,
    "inspection_fee": 20.00,
    "total_amount": 520.00,
    "warranty_duration": 5,
    "inspection_report": {
      "condition_score": 8,
      "grade": "A",
      "battery_health": 95,
      "technician_notes": "...",
      "images": [...]
    }
  }
}
```

---

This completes the backend implementation structure for the Inspection & Warranty feature.
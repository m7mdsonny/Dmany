<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Inspection & Warranty System Migration
 * Creates all necessary tables for the inspection and warranty feature
 * This is a core business module, not a secondary feature
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Inspection Configuration Table (Global Settings)
        Schema::create('inspection_configurations', function (Blueprint $table) {
            $table->id();
            $table->decimal('fee_percentage', 5, 2)->default(4.00)->comment('Inspection & Warranty fee percentage (default 4%)');
            $table->integer('warranty_duration')->default(5)->comment('Warranty duration in days (default 5)');
            $table->text('service_description')->nullable()->comment('Service description (Rich Text)');
            $table->text('workflow_steps')->nullable()->comment('Inspection workflow steps (editable text)');
            $table->text('terms_conditions')->nullable()->comment('Warranty terms & conditions (Rich Text)');
            $table->json('covered_items')->nullable()->comment('List of covered warranty issues');
            $table->json('excluded_items')->nullable()->comment('List of excluded warranty issues');
            $table->boolean('is_active')->default(true)->comment('Enable/Disable service globally');
            $table->timestamps();
            
            $table->index('is_active');
        });

        // 2. Inspection Orders Table (Core Transaction Table)
        Schema::create('inspection_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique()->comment('Unique order number (e.g., IW-2025-001)');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade')->comment('Product/Item being inspected');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade')->comment('Buyer user ID');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade')->comment('Seller user ID');
            
            // Pricing breakdown (very clear for audit)
            $table->decimal('device_price', 10, 2)->comment('Original device price');
            $table->decimal('inspection_fee', 10, 2)->comment('Calculated inspection fee (device_price * fee_percentage)');
            $table->decimal('total_amount', 10, 2)->comment('Total amount paid (device_price + inspection_fee)');
            
            // Status tracking
            $table->enum('status', [
                'pending',
                'device_received',
                'under_inspection',
                'passed',
                'failed',
                'delivered',
                'warranty_active',
                'warranty_expired',
                'cancelled'
            ])->default('pending')->comment('Current order status');
            
            // Technician assignment
            $table->foreignId('assigned_technician_id')->nullable()->constrained('users')->onDelete('set null')->comment('Assigned technician/admin');
            
            // Important dates
            $table->dateTime('device_received_at')->nullable()->comment('When device was received at office');
            $table->dateTime('inspection_date')->nullable()->comment('When inspection was performed');
            $table->dateTime('delivery_date')->nullable()->comment('When device was delivered to buyer');
            $table->date('warranty_start_date')->nullable()->comment('Warranty start date');
            $table->date('warranty_end_date')->nullable()->comment('Warranty end date');
            $table->integer('warranty_duration')->default(5)->comment('Warranty duration in days');
            
            // Internal notes (admin only)
            $table->text('internal_notes')->nullable()->comment('Internal admin notes (not visible to users)');
            $table->text('admin_notes')->nullable()->comment('Additional admin notes');
            
            $table->timestamps();
            $table->softDeletes(); // Soft delete for audit trail
            
            // Indexes for performance
            $table->index(['status', 'created_at']);
            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index(['assigned_technician_id']);
            $table->index('order_number');
        });

        // 3. Inspection Reports Table (Detailed Inspection Data)
        Schema::create('inspection_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_order_id')->constrained('inspection_orders')->onDelete('cascade')->unique()->comment('Linked inspection order');
            
            // Inspection checklist results (all editable by admin/technician)
            $table->integer('battery_health')->nullable()->comment('Battery health percentage (0-100)');
            $table->enum('screen_condition', ['excellent', 'good', 'fair', 'poor'])->nullable()->comment('Screen condition assessment');
            $table->enum('camera_condition', ['excellent', 'good', 'fair', 'poor'])->nullable()->comment('Camera condition assessment');
            $table->enum('speaker_status', ['working', 'partial', 'not_working'])->nullable()->comment('Speaker/Mic status');
            $table->enum('network_status', ['working', 'partial', 'not_working'])->nullable()->comment('Network/WiFi/Bluetooth status');
            
            // Overall assessment
            $table->integer('condition_score')->nullable()->comment('Overall condition score (1-10)');
            $table->enum('grade', ['A', 'B', 'C', 'D', 'Fail'])->nullable()->comment('Final grade assessment');
            
            // Technician details
            $table->text('technician_notes')->nullable()->comment('Detailed technician notes');
            $table->json('checklist_results')->nullable()->comment('Additional checklist results (JSON)');
            
            // Final decision
            $table->enum('final_decision', ['pass', 'fail'])->nullable()->comment('Final inspection decision (controls deal flow)');
            $table->dateTime('decision_date')->nullable()->comment('When decision was made');
            $table->foreignId('decision_by')->nullable()->constrained('users')->onDelete('set null')->comment('Admin who made the decision');
            
            // Report generation
            $table->string('report_url')->nullable()->comment('PDF inspection report URL');
            $table->text('decision_notes')->nullable()->comment('Notes for the decision');
            
            $table->timestamps();
            
            $table->index('inspection_order_id');
            $table->index(['final_decision', 'decision_date']);
        });

        // 4. Inspection Report Images Table
        Schema::create('inspection_report_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_report_id')->constrained('inspection_reports')->onDelete('cascade')->comment('Linked inspection report');
            $table->string('image_url')->comment('Image file URL/path');
            $table->string('image_type')->nullable()->comment('Type: diagnostic, physical, screen, battery, etc.');
            $table->text('caption')->nullable()->comment('Image caption/description');
            $table->integer('sort_order')->default(0)->comment('Display order');
            $table->timestamps();
            
            $table->index('inspection_report_id');
            $table->index('sort_order');
        });

        // 5. Warranty Claims Table
        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_order_id')->constrained('inspection_orders')->onDelete('cascade')->comment('Linked inspection order');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Buyer making the claim');
            $table->string('claim_number')->unique()->comment('Unique claim number (e.g., WC-2025-001)');
            
            // Claim details
            $table->text('description')->comment('Detailed claim description');
            $table->enum('status', [
                'pending',
                'under_review',
                'approved',
                'rejected',
                'resolved',
                'cancelled'
            ])->default('pending')->comment('Claim status');
            
            // Admin resolution
            $table->text('admin_response')->nullable()->comment('Admin response/decision notes');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null')->comment('Admin who resolved the claim');
            $table->dateTime('resolved_at')->nullable()->comment('When claim was resolved');
            
            // Decision outcome
            $table->enum('decision_outcome', [
                'full_refund',
                'partial_refund',
                'repair',
                'replacement',
                'rejected',
                'no_action'
            ])->nullable()->comment('Decision outcome type');
            $table->decimal('refund_amount', 10, 2)->nullable()->comment('Refund amount if applicable');
            
            // Audit trail
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('user_id');
            $table->index('inspection_order_id');
            $table->index('claim_number');
        });

        // 6. Warranty Claim Images Table
        Schema::create('warranty_claim_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warranty_claim_id')->constrained('warranty_claims')->onDelete('cascade')->comment('Linked warranty claim');
            $table->string('image_url')->comment('Image file URL/path');
            $table->text('description')->nullable()->comment('Image description');
            $table->integer('sort_order')->default(0)->comment('Display order');
            $table->timestamps();
            
            $table->index('warranty_claim_id');
        });

        // 7. Inspection Audit Log Table (Track all admin actions)
        Schema::create('inspection_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_order_id')->nullable()->constrained('inspection_orders')->onDelete('set null')->comment('Related order');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Admin who performed action');
            $table->string('action_type')->comment('Action type: status_change, decision_made, assignment, etc.');
            $table->string('action_description')->comment('Human-readable action description');
            $table->json('old_values')->nullable()->comment('Previous values (JSON)');
            $table->json('new_values')->nullable()->comment('New values (JSON)');
            $table->text('notes')->nullable()->comment('Additional notes');
            $table->timestamps();
            
            $table->index(['inspection_order_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('action_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order (respecting foreign key constraints)
        Schema::dropIfExists('inspection_audit_logs');
        Schema::dropIfExists('warranty_claim_images');
        Schema::dropIfExists('warranty_claims');
        Schema::dropIfExists('inspection_report_images');
        Schema::dropIfExists('inspection_reports');
        Schema::dropIfExists('inspection_orders');
        Schema::dropIfExists('inspection_configurations');
    }
};

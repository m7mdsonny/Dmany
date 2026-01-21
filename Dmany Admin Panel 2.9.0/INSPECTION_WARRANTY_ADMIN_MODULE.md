# Inspection & Warranty Admin Panel Module - Complete Documentation

## ✅ Implementation Complete

This document outlines the complete Admin Panel implementation for the Inspection & Warranty service - a core business module for the marketplace.

## 📋 Module Overview

The Inspection & Warranty Admin Panel provides comprehensive management capabilities for:
- Global service configuration
- Inspection order management
- Inspection report creation and review
- Warranty lifecycle management
- Warranty claims resolution

---

## 🗄️ Database Structure

### Tables Created:

1. **`inspection_configurations`** - Global settings (singleton pattern)
2. **`inspection_orders`** - Core transaction table
3. **`inspection_reports`** - Detailed inspection data
4. **`inspection_report_images`** - Images/logs attached to reports
5. **`warranty_claims`** - Warranty claims by buyers
6. **`warranty_claim_images`** - Images attached to claims
7. **`inspection_audit_logs`** - Complete audit trail

### Key Relationships:
- `inspection_orders.item_id` → `items.id`
- `inspection_orders.buyer_id` → `users.id`
- `inspection_orders.seller_id` → `users.id`
- `inspection_orders.assigned_technician_id` → `users.id`
- `inspection_reports.inspection_order_id` → `inspection_orders.id` (1:1)
- `warranty_claims.inspection_order_id` → `inspection_orders.id` (1:many)

---

## 🎯 Admin Pages

### 1️⃣ Global Settings Page
**Route**: `/inspection-warranty/settings`

**Features**:
- ✅ Inspection fee percentage (default: 4%, editable)
- ✅ Warranty duration in days (default: 5, editable)
- ✅ Service description (Rich Text)
- ✅ Inspection workflow steps (editable text)
- ✅ Warranty terms & conditions (Rich Text)
- ✅ Covered issues list (editable)
- ✅ Excluded issues list (editable)
- ✅ Enable/Disable service globally

**Changes reflect immediately** in:
- Mobile app
- Web product pages
- Deal breakdowns

### 2️⃣ Inspection Orders Management
**Route**: `/inspection-warranty/orders`

**Features**:
- ✅ View all inspection orders in table
- ✅ Filter by status (pending, under_inspection, passed, failed, etc.)
- ✅ Search by order number, product name, buyer name
- ✅ View order details (button per row)
- ✅ Sort by any column
- ✅ Pagination support

**Table Columns**:
- Order Number
- Product
- Buyer
- Seller
- Status
- Device Price
- Inspection Fee
- Total Amount
- Assigned Technician
- Final Decision
- Created Date

### 3️⃣ Inspection Details Page (Per Order)
**Route**: `/inspection-warranty/orders/{id}`

**Features**:

#### A) Device & Deal Info:
- Order number
- Product details with image
- Buyer & seller information
- Payment breakdown (device price, inspection fee, total)
- Payment status

#### B) Inspection Checklist (Fully Editable):
- Battery health % (0-100)
- Screen condition (excellent/good/fair/poor)
- Camera condition (excellent/good/fair/poor)
- Speaker/Mic status (working/partial/not_working)
- Network/WiFi status (working/partial/not_working)
- Overall condition score (1-10)
- Grade (A/B/C/D/Fail)
- Technician notes (rich text)
- Additional checklist results (JSON)

**All fields**:
- Editable by admin/technician
- Saved with timestamps
- Fully auditable

#### C) Attachments:
- Upload inspection photos
- Upload logs/reports (PDF, TXT)
- View/delete existing images
- Image type classification
- Sort order management

#### D) Final Inspection Decision:
- **Pass Inspection** - Deal continues, warranty starts
- **Fail Inspection** - Deal cancelled, payment refunded
- Decision notes
- Decision timestamp
- Admin who made decision

#### E) Status & Actions:
- Update order status
- Assign technician
- Admin notes (internal)
- Auto-date setting based on status

#### F) Important Dates:
- Order created
- Device received
- Inspection date
- Delivery date
- Warranty start/end dates
- Remaining warranty days (countdown)

#### G) Audit Trail:
- Complete log of all admin actions
- User who performed action
- Action type
- Old/new values
- Timestamps

### 4️⃣ Warranty Management Page
**Route**: `/inspection-warranty/warranties`

**Features**:
- ✅ View active warranties
- ✅ View expired warranties
- ✅ View all warranties
- ✅ Filter by status
- ✅ Warranty details:
  - Order number
  - Product
  - Buyer
  - Warranty start date
  - Warranty end date
  - Remaining days (highlighted)
  - Condition score
  - Grade

### 5️⃣ Warranty Claims & Disputes
**Route**: `/inspection-warranty/claims`

**Features**:
- ✅ View all warranty claims
- ✅ Filter by status (pending, under_review, approved, rejected, resolved)
- ✅ Search functionality
- ✅ Claim details per row

**Table Columns**:
- Claim Number
- Order Number
- Product
- Buyer
- Description (truncated)
- Status
- Decision Outcome
- Refund Amount
- Resolved By
- Created Date

### 6️⃣ Claim Detail & Resolution Page
**Route**: `/inspection-warranty/claims/{id}`

**Features**:

#### Claim Information:
- Claim number
- Linked order number
- Product details
- Buyer information
- Current status
- Creation date

#### Inspection Report Comparison:
- Display original inspection report baseline
- Condition score at inspection
- Grade at inspection
- Battery health at inspection
- Technician notes

#### Claim Details:
- Full claim description
- Claim images (if any)

#### Admin Resolution Panel:
- **Decision** (required):
  - Approve
  - Reject
  - Mark as Resolved

- **Decision Outcome** (required):
  - Full Refund
  - Partial Refund (with amount field)
  - Repair
  - Replacement
  - Reject Claim
  - No Action Required

- **Admin Response** (required):
  - Detailed explanation
  - Decision reasoning
  - Next steps

- **Refund Amount** (if applicable):
  - Editable field for partial refunds
  - Auto-calculated for full refunds

---

## 🔌 API Endpoints

### Public Endpoints (No Auth):

#### `GET /api/get-inspection-config`
Get inspection service configuration
- **Response**: Fee percentage, warranty duration, terms, covered/excluded items, service status

### Authenticated Endpoints (Auth Required):

#### `GET /api/get-inspection-order`
Get inspection order details
- **Params**: `item_id` OR `order_id`
- **Response**: Full order details with inspection report

#### `POST /api/create-inspection-order`
Create new inspection order
- **Body**: `item_id`
- **Response**: Created order with pricing breakdown

#### `GET /api/get-warranty-claims`
Get user's warranty claims
- **Params**: `offset`, `limit` (pagination)
- **Response**: List of user's warranty claims

#### `POST /api/create-warranty-claim`
Create warranty claim
- **Body**: `order_id`, `description`, `images[]`
- **Response**: Created claim details

---

## 🔐 Security & Permissions

### Required Permissions (Add to permissions table):

```php
'inspection-order-list'
'inspection-order-view'
'inspection-order-update'
'inspection-report-update'
'warranty-list'
'warranty-claim-list'
'warranty-claim-view'
'warranty-claim-update'
'settings-update'  // (Already exists)
```

### Permission-Based Access:

- **Settings Page**: Requires `settings-update`
- **Orders List**: Requires `inspection-order-list`
- **Order Detail**: Requires `inspection-order-view`
- **Update Status**: Requires `inspection-order-update`
- **Submit Report**: Requires `inspection-report-update`
- **Warranties**: Requires `warranty-list`
- **Claims List**: Requires `warranty-claim-list`
- **Claim Detail**: Requires `warranty-claim-view`
- **Resolve Claim**: Requires `warranty-claim-update`

### Audit Trail:

**All admin actions are logged** in `inspection_audit_logs`:
- Status changes
- Technician assignments
- Inspection decisions
- Claim resolutions
- Settings updates

**Every log entry includes**:
- User who performed action
- Action type
- Action description
- Old values (before change)
- New values (after change)
- Additional notes
- Timestamp

---

## 📁 File Structure

### Controllers:
- `app/Http/Controllers/InspectionWarrantyController.php` - Main admin controller
- `app/Http/Controllers/ApiController.php` - API endpoints (updated)

### Models:
- `app/Models/InspectionConfiguration.php` - Global settings
- `app/Models/InspectionOrder.php` - Orders
- `app/Models/InspectionReport.php` - Reports
- `app/Models/InspectionReportImage.php` - Report images
- `app/Models/WarrantyClaim.php` - Claims
- `app/Models/WarrantyClaimImage.php` - Claim images
- `app/Models/InspectionAuditLog.php` - Audit trail

### Migrations:
- `database/migrations/2025_12_10_100000_create_inspection_warranty_tables.php`

### Views:
- `resources/views/inspection-warranty/settings.blade.php` - Settings page
- `resources/views/inspection-warranty/orders.blade.php` - Orders list
- `resources/views/inspection-warranty/order-detail.blade.php` - Order detail
- `resources/views/inspection-warranty/warranties.blade.php` - Warranties
- `resources/views/inspection-warranty/claims.blade.php` - Claims list
- `resources/views/inspection-warranty/claim-detail.blade.php` - Claim detail

### Routes:
- `routes/web.php` - Admin routes (updated)
- `routes/api.php` - API routes (updated)

### Layout:
- `resources/views/layouts/sidebar.blade.php` - Sidebar menu (updated)

---

## 🚀 Installation Steps

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Seed Default Configuration
```php
// In tinker or seeder:
InspectionConfiguration::getConfiguration(); // Creates default config
```

### 3. Add Permissions
Add the following permissions to your permissions table:

```sql
INSERT INTO permissions (name, guard_name, created_at, updated_at) VALUES
('inspection-order-list', 'web', NOW(), NOW()),
('inspection-order-view', 'web', NOW(), NOW()),
('inspection-order-update', 'web', NOW(), NOW()),
('inspection-report-update', 'web', NOW(), NOW()),
('warranty-list', 'web', NOW(), NOW()),
('warranty-claim-list', 'web', NOW(), NOW()),
('warranty-claim-view', 'web', NOW(), NOW()),
('warranty-claim-update', 'web', NOW(), NOW());
```

### 4. Assign Permissions to Roles
Assign these permissions to appropriate admin roles (Admin, Manager, etc.)

---

## 📊 Workflow Overview

### Inspection Flow:

1. **Buyer creates order** → Status: `pending`
2. **Device received at office** → Status: `device_received`
3. **Technician assigned** → Status: `under_inspection`
4. **Inspection performed** → Admin fills checklist
5. **Decision made** → `pass` or `fail`
   - If `pass`: Status → `passed` → `delivered` → `warranty_active`
   - If `fail`: Status → `failed` → Payment refunded → Deal cancelled
6. **Warranty period** → 5 days (configurable) from delivery
7. **Warranty claims** → Buyers can submit claims during warranty

### Status Flow Diagram:

```
pending → device_received → under_inspection → passed → delivered → warranty_active → warranty_expired
                                                      ↓
                                                   failed
```

---

## 🎨 UI/UX Features

### Modern Design:
- ✅ Clean, professional interface
- ✅ Bootstrap table for data display
- ✅ Status badges with colors
- ✅ Responsive design
- ✅ Clear information hierarchy

### User Experience:
- ✅ One-click status updates
- ✅ Auto-date setting
- ✅ Real-time validation
- ✅ Success/error notifications
- ✅ Audit trail visibility
- ✅ Image upload/management
- ✅ Rich text editing support

---

## 📝 Notes & Best Practices

### Important Considerations:

1. **Singleton Pattern**: Only one configuration record should exist. Use `getConfiguration()` method.

2. **Order Numbers**: Auto-generated format: `IW-YYYY-XXX` (e.g., IW-2025-001)

3. **Claim Numbers**: Auto-generated format: `WC-YYYY-XXX` (e.g., WC-2025-001)

4. **Status Management**: Status changes trigger automatic date updates where applicable.

5. **Decision Control**: Final inspection decision (`pass`/`fail`) controls entire deal flow.

6. **Audit Trail**: Every admin action is logged. Cannot be disabled for compliance.

7. **File Storage**: Images stored in `storage/app/public/inspection-reports/` and `storage/app/public/warranty-claims/`

8. **Permissions**: All pages protected by permission checks. Admins cannot access without proper permissions.

---

## 🔧 Configuration

### Default Settings:
- **Fee Percentage**: 4%
- **Warranty Duration**: 5 days
- **Service Status**: Active (can be disabled globally)

### Editable Settings:
All settings are editable via admin panel. Changes take effect immediately for new orders.

---

## 📱 Integration Points

### Frontend Integration:
- Mobile app uses API endpoints to:
  - Fetch configuration
  - Create orders
  - Track order status
  - Submit warranty claims

- Web app uses API endpoints to:
  - Display pricing breakdown
  - Create orders
  - Show inspection status
  - Display reports

---

## 🐛 Troubleshooting

### Common Issues:

1. **Migration fails**: Check foreign key constraints. Ensure `items` and `users` tables exist.

2. **Permission errors**: Verify permissions are added and assigned to admin roles.

3. **Image upload fails**: Check `storage/app/public` permissions and ensure symlink exists.

4. **Status not updating**: Check validation rules and ensure all required fields are provided.

---

## 📚 Additional Resources

- See `INSPECTION_WARRANTY_ADMIN_DOCUMENTATION.md` in Web app folder for API documentation
- See `FLUTTER_INSPECTION_WARRANTY_IMPLEMENTATION.md` in Flutter app folder for mobile implementation guide

---

## ✅ Implementation Checklist

- [x] Database migrations created
- [x] Models created with relationships
- [x] Controller with all CRUD operations
- [x] Routes configured
- [x] Admin views created
- [x] Sidebar menu updated
- [x] API endpoints implemented
- [x] Audit trail implemented
- [x] Permission checks added
- [x] File upload handling
- [x] Status management
- [x] Documentation complete

---

**The Inspection & Warranty Admin Panel module is now fully operational!**

This is a core business module that provides complete operational control over the inspection and warranty service, ensuring transparency, accountability, and efficient management of all orders, inspections, and claims.

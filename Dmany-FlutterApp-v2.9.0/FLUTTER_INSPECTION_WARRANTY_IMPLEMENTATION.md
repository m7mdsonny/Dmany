# Flutter Mobile App - Inspection & Warranty Implementation Guide

## ✅ Completed Implementation

### 1. Product Details Screen - Inspection & Warranty Button
- ✅ Added Inspection & Warranty button to `ad_details_screen.dart`
- ✅ Button appears below Chat and Make Offer buttons
- ✅ Trust-focused design with blue-to-indigo gradient
- ✅ Icon: `Icons.verified_user`
- ✅ Supporting text: "Buy safely with professional inspection & 5-day warranty"
- ✅ Navigation to Inspection & Warranty screen integrated

### 2. Routes Added
- ✅ Added `Routes.inspectionWarrantyScreen` route
- ✅ Added `Routes.inspectionWarrantyTrackScreen` route
- ✅ Route handlers added in `routes.dart`

## 📋 TODO - Required Implementation

### 3. Create Inspection & Warranty Screens

#### A) Inspection & Warranty Overview Screen
**File**: `lib/ui/screens/item/inspection_warranty/inspection_warranty_screen.dart`

**Requirements**:
- Service overview with 4-step timeline:
  1. Payment Secured
  2. Office Inspection (physical, by technician)
  3. Delivery to Buyer
  4. Warranty Active (5 days)
- Pricing breakdown section:
  - Product price
  - Inspection & Warranty fee (4%)
  - Total amount
  - Info tooltip explaining fee
- Warranty coverage section:
  - What's covered (list)
  - What's NOT covered (list)
  - Warranty duration: 5 days
- "Proceed with Inspection & Warranty" CTA button
- Reassuring message: "Your payment is secured until inspection is completed"

**UI Elements Needed**:
- Modern mobile cards
- Step-by-step timeline visualization
- Price breakdown card
- Coverage lists with icons
- Trust-focused color scheme (blue/indigo gradient)
- Smooth animations

#### B) Deal Tracking Screen
**File**: `lib/ui/screens/item/inspection_warranty/inspection_warranty_track_screen.dart`

**Requirements**:
- Deal status indicator with progress:
  - Waiting for inspection
  - Under inspection
  - Approved / Rejected
  - Delivered
  - Warranty Active
- Inspection summary (when available):
  - Condition score (1-10)
  - Grade (A / B / C / D)
  - Battery health (%)
  - Technician notes
- Warranty countdown timer (5 days)
- Inspection report view/download
- Real-time status updates (polling or websocket)

**UI Elements Needed**:
- Progress indicator/stepper
- Status badges
- Score displays
- Timer widget
- Report viewer/download button
- Refresh/polling mechanism

### 4. API Endpoints Integration

**File**: `lib/utils/api.dart`

**Required Endpoints**:
```dart
// Add to Api class
static String getInspectionConfigApi = "get-inspection-config";
static String getInspectionOrderApi = "get-inspection-order";
static String createInspectionOrderApi = "create-inspection-order";
static String getInspectionReportApi = "get-inspection-report";
static String getWarrantyClaimsApi = "get-warranty-claims";
static String createWarrantyClaimApi = "create-warranty-claim";
```

**Methods to Add**:
- `getInspectionConfig()` - Get fee percentage, warranty duration, terms
- `getInspectionOrder(itemId, orderId)` - Get order details
- `createInspectionOrder(itemId)` - Create new order
- `getInspectionReport(orderId)` - Get inspection report

### 5. State Management (Cubits)

**Create new Cubits**:
1. `lib/data/cubits/inspection/get_inspection_config_cubit.dart`
   - Fetches inspection service configuration
   
2. `lib/data/cubits/inspection/create_inspection_order_cubit.dart`
   - Creates new inspection order
   
3. `lib/data/cubits/inspection/get_inspection_order_cubit.dart`
   - Fetches inspection order details
   
4. `lib/data/cubits/inspection/get_inspection_report_cubit.dart`
   - Fetches inspection report

### 6. Data Models

**Create Models**:
1. `lib/data/model/inspection/inspection_config_model.dart`
   - fee_percentage
   - warranty_duration
   - terms_conditions
   - covered_items
   - excluded_items

2. `lib/data/model/inspection/inspection_order_model.dart`
   - id, order_number
   - item_id, buyer_id, seller_id
   - device_price, inspection_fee, total_amount
   - status
   - assigned_technician_id
   - warranty_start_date, warranty_end_date
   - warranty_duration

3. `lib/data/model/inspection/inspection_report_model.dart`
   - inspection_order_id
   - condition_score
   - grade
   - battery_health
   - technician_notes
   - final_decision
   - report_url
   - images[]

4. `lib/data/model/inspection/warranty_claim_model.dart`
   - id, claim_number
   - inspection_order_id
   - user_id
   - description
   - status
   - admin_response
   - images[]

### 7. UI Design Guidelines

#### Colors:
- **Primary Trust Blue**: `Color(0xFF2563EB)`
- **Secondary Indigo**: `Color(0xFF4F46E5)`
- **Success Green**: `Color(0xFF10B981)`
- **Warning Yellow**: `Color(0xFFF59E0B)`

#### Typography:
- **Headings**: Bold, 18-24px
- **Body**: Regular, 14-16px
- **Supporting**: Light, 12-14px

#### Spacing:
- **Card Padding**: 16px
- **Section Gap**: 24px
- **Element Gap**: 12px

#### Animations:
- Page transitions: Slide from right (300ms)
- Button press: Scale down (100ms)
- Loading: Pulse/shimmer effect
- Success: Checkmark animation

### 8. Mobile-Specific Features

#### Bottom Sheets:
- Use for price breakdown details
- Use for warranty coverage details
- Use for confirmation dialogs

#### Pull-to-Refresh:
- Implement on tracking screen
- Refresh order status every 30 seconds

#### Offline Support:
- Cache inspection config
- Cache order status
- Show offline indicator

#### Error Handling:
- Network errors
- API failures
- Invalid order states

### 9. Testing Checklist

- [ ] Button appears on product details screen
- [ ] Navigation to overview screen works
- [ ] Pricing breakdown calculates correctly
- [ ] Order creation flow works
- [ ] Tracking screen updates correctly
- [ ] Status transitions work
- [ ] Report display works
- [ ] Warranty timer counts down correctly
- [ ] Offline mode handles gracefully
- [ ] Error states display properly

## 🚀 Implementation Priority

1. **High Priority**:
   - Create Inspection & Warranty overview screen
   - Add API endpoints
   - Create basic data models
   - Create state management cubits

2. **Medium Priority**:
   - Create Deal Tracking screen
   - Implement status updates
   - Add animations

3. **Low Priority**:
   - Offline support
   - Advanced error handling
   - Performance optimization

## 📱 Next Steps

1. Create screen files in `lib/ui/screens/item/inspection_warranty/`
2. Add API methods in `lib/utils/api.dart`
3. Create Cubits for state management
4. Create data models
5. Register Cubits in `lib/app/register_cubits.dart`
6. Test complete flow

---

**Note**: This is a comprehensive guide. Implement screens step by step, testing each component before moving to the next.

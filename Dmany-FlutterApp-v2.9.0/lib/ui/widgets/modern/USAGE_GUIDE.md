# Modern UI Components - Usage Guide

## دليل استخدام المكونات الحديثة / Modern Components Usage Guide

### 1. إضافة صفحة جديدة باستخدام الانتقالات الحديثة / Adding New Pages with Modern Transitions

```dart
// Instead of MaterialPageRoute, use:
Navigator.push(
  context,
  SlideFromRightPageRoute(child: NewScreen()),
);

// For modals:
Navigator.push(
  context,
  ScaleFadePageRoute(child: ModalScreen()),
);

// For bottom sheets:
Navigator.push(
  context,
  SlideFromBottomPageRoute(child: BottomSheetScreen()),
);
```

### 2. تحديث قائمة باستخدام الرسوم المتحركة / Updating Lists with Animations

#### Before (Old):
```dart
ListView.builder(
  itemCount: items.length,
  itemBuilder: (context, index) {
    return ListTile(
      title: Text(items[index].name),
      onTap: () => navigateToItem(items[index]),
    );
  },
)
```

#### After (Modern):
```dart
ListView.builder(
  itemCount: items.length,
  itemBuilder: (context, index) {
    return FadeInWidget(
      delay: Duration(milliseconds: index * 50),
      child: SlideInFromBottom(
        delay: Duration(milliseconds: index * 50),
        child: ModernListItemCard(
          title: items[index].name,
          subtitle: items[index].description,
          trailing: items[index].price,
          onTap: () => navigateToItem(items[index]),
        ),
      ),
    );
  },
)
```

### 3. حالات التحميل الحديثة / Modern Loading States

#### Before (Old):
```dart
if (isLoading)
  Center(child: CircularProgressIndicator())
else
  ListView(...)
```

#### After (Modern):
```dart
if (isLoading)
  Column(
    children: List.generate(
      5,
      (index) => SkeletonListItem(
        showAvatar: true,
        lines: 3,
        animationDelay: index * 50,
      ),
    ),
  )
else
  ListView(...)
```

### 4. الأزرار الحديثة / Modern Buttons

#### Before (Old):
```dart
ElevatedButton(
  onPressed: () => {},
  child: Text('Submit'),
)
```

#### After (Modern):
```dart
ModernButton(
  label: 'Submit',
  icon: Icons.check,
  onPressed: () => handleSubmit(),
  style: ModernButtonStyle.primary,
  isLoading: isSubmitting,
)
```

### 5. البطاقات الحديثة / Modern Cards

#### Before (Old):
```dart
Card(
  child: ListTile(
    title: Text('Title'),
    subtitle: Text('Subtitle'),
  ),
)
```

#### After (Modern):
```dart
ModernCard(
  onTap: () => navigateToDetails(),
  enableAnimation: true,
  child: Column(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      Text(
        'Title',
        style: DesignTypography.titleMedium(),
      ),
      SizedBox(height: DesignSpacing.sm),
      Text(
        'Subtitle',
        style: DesignTypography.bodyMedium(),
      ),
    ],
  ),
)
```

### 6. تدرج الألوان والظلال / Color Gradients and Shadows

```dart
// Trust gradient button
Container(
  decoration: BoxDecoration(
    gradient: DesignColors.trustGradient,
    borderRadius: BorderRadius.circular(DesignRadius.button),
    boxShadow: DesignColors.buttonShadow,
  ),
  child: Material(
    color: Colors.transparent,
    child: InkWell(
      onTap: () => {},
      child: Padding(...),
    ),
  ),
)
```

### 7. Typography الحديث / Modern Typography

```dart
// Headlines
Text('Title', style: DesignTypography.headlineLarge())

// Titles
Text('Subtitle', style: DesignTypography.titleMedium())

// Body
Text('Content', style: DesignTypography.bodyLarge())

// Labels
Text('Label', style: DesignTypography.labelMedium())
```

### 8. Spacing متسق / Consistent Spacing

```dart
// Use spacing tokens
SizedBox(height: DesignSpacing.base)  // 16px
SizedBox(height: DesignSpacing.lg)    // 24px
SizedBox(width: DesignSpacing.sm)     // 8px

// Component spacing
padding: EdgeInsets.all(DesignSpacing.cardPadding)
margin: EdgeInsets.symmetric(
  horizontal: DesignSpacing.screenPadding,
  vertical: DesignSpacing.md,
)
```

### 9. رسوم متحركة للنجاح / Success Animations

```dart
if (isSuccess)
  SuccessAnimation(
    size: 48.0,
    onComplete: () => navigateNext(),
  )
else
  ModernButton(
    label: 'Submit',
    onPressed: () => handleSubmit(),
  )
```

### 10. مثال كامل لشاشة / Complete Screen Example

```dart
class ModernScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: DesignColors.backgroundLight,
      appBar: AppBar(
        title: Text(
          'Modern Screen',
          style: DesignTypography.titleLarge(),
        ),
      ),
      body: RefreshIndicator(
        onRefresh: () async => await refreshData(),
        child: ListView.builder(
          padding: EdgeInsets.all(DesignSpacing.screenPadding),
          itemCount: items.length,
          itemBuilder: (context, index) {
            return FadeInWidget(
              delay: Duration(milliseconds: index * 50),
              child: SlideInFromBottom(
                delay: Duration(milliseconds: index * 50),
                child: ModernCard(
                  onTap: () => navigateToDetails(items[index]),
                  margin: EdgeInsets.only(bottom: DesignSpacing.md),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        items[index].title,
                        style: DesignTypography.titleMedium(),
                      ),
                      SizedBox(height: DesignSpacing.sm),
                      Text(
                        items[index].description,
                        style: DesignTypography.bodyMedium(),
                      ),
                    ],
                  ),
                ),
              ),
            );
          },
        ),
      ),
      bottomNavigationBar: SafeArea(
        child: Padding(
          padding: EdgeInsets.all(DesignSpacing.cardPadding),
          child: ModernButton(
            label: 'Continue',
            icon: Icons.arrow_forward,
            onPressed: () => navigateNext(),
            style: ModernButtonStyle.primary,
          ),
        ),
      ),
    );
  }
}
```

## أفضل الممارسات / Best Practices

### ✅ افعل / Do:
- استخدم المكونات الحديثة في الشاشات الجديدة
- استخدم Spacing tokens بدلاً من القيم الثابتة
- أضف رسوم متحركة متدرجة للقوائم
- استخدم Skeleton loading بدلاً من CircularProgressIndicator
- احترم تفضيلات reduced motion

### ❌ لا تفعل / Don't:
- لا تستخدم الألوان القاسية (neon)
- لا تخلط الأنماط القديمة والحديثة في نفس الشاشة
- لا تضيف رسوم متحركة طويلة (أكثر من 500ms)
- لا تنسى اختبار الأداء على الأجهزة المنخفضة
- لا تستخدم ألوان مخصصة خارج نظام التصميم

## التكامل التدريجي / Gradual Integration

يمكنك تطبيق المكونات الحديثة تدريجياً:

1. **ابدأ بالشاشات الجديدة** - استخدم المكونات الحديثة في الشاشات الجديدة
2. **حدث الأزرار** - استبدل الأزرار القديمة بـ ModernButton
3. **حدث البطاقات** - استبدل Card القديم بـ ModernCard
4. **أضف الرسوم المتحركة** - أضف انتقالات الصفحات والرسوم المتحركة للقوائم
5. **حسّن حالات التحميل** - استخدم Skeleton loading

## الدعم / Support

للمزيد من المعلومات، راجع:
- `ANIMATION_GUIDELINES.md` - دليل الرسوم المتحركة
- `MODERN_UI_REDESIGN_SUMMARY.md` - ملخص التصميم الحديث
- `lib/ui/theme/design_system.dart` - نظام التصميم الكامل

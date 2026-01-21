# Modern UI Components

This directory contains modern, trust-focused UI components with smooth animations.

## Components

### ModernButton
Trust-focused button with smooth press animations.

```dart
ModernButton(
  label: 'Continue',
  icon: Icons.arrow_forward,
  onPressed: () => navigateNext(),
  style: ModernButtonStyle.primary,
)
```

**Styles:**
- `primary` - Trust blue gradient (default)
- `secondary` - Neutral gray
- `outline` - Bordered
- `success` - Green gradient
- `ghost` - Transparent

### ModernCard
Premium card with lift animation on tap.

```dart
ModernCard(
  onTap: () => navigateToDetails(),
  enableAnimation: true,
  animationDelay: Duration(milliseconds: 100),
  child: Column(
    children: [
      Text('Card Title'),
      Text('Card Content'),
    ],
  ),
)
```

**Variants:**
- `ModernCard` - Standard with shadow
- `ModernCardMinimal` - Subtle border, no shadow
- `ModernCardElevated` - Prominent shadow

### ModernListItem
Trust-focused list item with smooth animations.

```dart
ModernListItem(
  title: 'Item Title',
  subtitle: 'Item Description',
  trailing: 'Value',
  leading: Icon(Icons.favorite),
  onTap: () => navigateToDetails(),
  animationDelay: 50,
)
```

**Variants:**
- `ModernListItem` - Standard list item
- `ModernListItemCard` - Card-styled list item

### Skeleton Loading
Shimmer loading effects for better UX.

```dart
// Skeleton Text
SkeletonText(width: 200, height: 16)

// Skeleton Card
SkeletonCard(height: 200)

// Skeleton List Item
SkeletonListItem(showAvatar: true, lines: 3)

// Skeleton Button
SkeletonButton(width: 150)
```

## Usage Examples

### Animated List
```dart
ListView.builder(
  itemCount: items.length,
  itemBuilder: (context, index) {
    return FadeInWidget(
      delay: Duration(milliseconds: index * 50),
      child: SlideInFromBottom(
        delay: Duration(milliseconds: index * 50),
        child: ModernCard(
          onTap: () => navigateToItem(items[index]),
          child: ItemWidget(items[index]),
        ),
      ),
    );
  },
)
```

### Loading State
```dart
if (isLoading)
  Column(
    children: List.generate(5, (index) => SkeletonListItem()),
  )
else
  ListView.builder(...)
```

### Card Grid
```dart
GridView.builder(
  gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
    crossAxisCount: 2,
    crossAxisSpacing: DesignSpacing.cardGap,
    mainAxisSpacing: DesignSpacing.cardGap,
  ),
  itemCount: items.length,
  itemBuilder: (context, index) {
    return FadeInWidget(
      delay: Duration(milliseconds: index * 50),
      child: ModernCard(
        onTap: () => navigateToDetails(items[index]),
        child: ItemCardWidget(items[index]),
      ),
    );
  },
)
```

## Best Practices

1. **Use staggered animations** for lists to create smooth reveal effects
2. **Show skeleton loaders** instead of spinners for better perceived performance
3. **Enable animations** only when appropriate (not during rapid scrolling)
4. **Respect reduced motion** preferences for accessibility
5. **Keep animations subtle** - they should enhance, not distract

## Design System Integration

All components use tokens from `DesignSystem`:
- Colors: `DesignColors.*`
- Typography: `DesignTypography.*`
- Spacing: `DesignSpacing.*`
- Radius: `DesignRadius.*`
- Durations: `DesignDurations.*`

This ensures consistency across the entire app.

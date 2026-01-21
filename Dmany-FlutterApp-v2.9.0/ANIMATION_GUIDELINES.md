# Animation Guidelines - Modern Mobile App

This document outlines animation principles, patterns, and best practices for creating a premium, trust-focused mobile experience.

## 🎯 Core Principles

### 1. **Purpose-Driven Animations**
- Every animation should have a clear purpose
- Enhance UX clarity, not distract
- Guide user attention naturally
- Provide feedback for user actions

### 2. **Trust & Professionalism**
- Smooth, polished animations build trust
- Avoid flashy or aggressive motions
- Keep animations subtle and refined
- Maintain consistent timing across the app

### 3. **Performance First**
- Animations should never drop frames
- Use optimized curves and durations
- Consider device capabilities
- Test on lower-end devices

## ⏱️ Timing & Duration

### Standard Durations
```dart
DesignDurations.instant = 100ms  // Micro-interactions
DesignDurations.fast = 200ms     // Quick feedback
DesignDurations.normal = 300ms   // Standard transitions
DesignDurations.slow = 400ms     // Deliberate movements
DesignDurations.slower = 500ms   // Major transitions
```

### When to Use Which Duration

| Duration | Use Case |
|----------|----------|
| 100ms | Button press, icon tap, checkbox toggle |
| 200ms | Card hover, chip selection, tooltip show/hide |
| 300ms | Page transitions, modal open/close, drawer slide |
| 400ms | Complex layout changes, card reveal |
| 500ms | Major screen transitions, onboarding flows |

## 🎬 Animation Curves

### Standard Curves
```dart
DesignCurves.standard = Curves.easeInOut      // Default smooth
DesignCurves.smooth = Curves.easeInOutCubic   // Premium feel
DesignCurves.spring = Curves.easeOutCubic     // Natural motion
DesignCurves.bounce = Curves.easeOutBack      // Playful emphasis
DesignCurves.sharp = Curves.easeInOutQuad     // Quick response
```

### Curve Selection Guide

| Curve | Best For | Feeling |
|-------|----------|---------|
| `easeInOut` | Standard transitions | Balanced, professional |
| `easeInOutCubic` | Card reveals, modals | Smooth, premium |
| `easeOutCubic` | Button presses, lifts | Natural, responsive |
| `easeOutBack` | Success animations | Playful, satisfying |
| `easeInOutQuad` | Quick state changes | Sharp, direct |

## 🎨 Animation Patterns

### 1. **Page Transitions**

#### Fade Transition
**Use for**: Content changes, refreshing data
```dart
PageTransitions.fadeTransition(context, animation, secondaryAnimation, child)
```

#### Slide from Right
**Use for**: Navigation, pushing new screens
```dart
PageTransitions.slideFromRight(context, animation, secondaryAnimation, child)
```

#### Slide from Bottom
**Use for**: Bottom sheets, modals
```dart
PageTransitions.slideFromBottom(context, animation, secondaryAnimation, child)
```

#### Scale and Fade
**Use for**: Dialogs, important modals
```dart
PageTransitions.scaleAndFade(context, animation, secondaryAnimation, child)
```

### 2. **Micro-Interactions**

#### Button Press Animation
**Use for**: All buttons, interactive elements
```dart
AnimatedButtonPress(
  onPressed: () => {},
  child: Button(),
)
```

**Behavior**:
- Scale down to 0.98 on press
- Duration: 100ms
- Immediate visual feedback
- No delay on tap

#### Card Lift Effect
**Use for**: Cards with tap interactions
```dart
AnimatedCard(
  onTap: () => {},
  child: CardContent(),
)
```

**Behavior**:
- Subtle elevation increase
- Smooth shadow transition
- Duration: 200ms
- Provides depth feedback

### 3. **Content Reveals**

#### Fade In
**Use for**: New content appearing, lists loading
```dart
FadeInWidget(
  delay: Duration(milliseconds: 100),
  child: Content(),
)
```

#### Slide In from Bottom
**Use for**: Cards in lists, stacked content
```dart
SlideInFromBottom(
  delay: Duration(milliseconds: 100),
  offset: 20.0,
  child: Card(),
)
```

**Staggered Animation Pattern**:
```dart
// For lists, stagger delays
for (int i = 0; i < items.length; i++)
  FadeInWidget(
    delay: Duration(milliseconds: i * 50),
    child: ItemCard(),
  )
```

### 4. **Loading States**

#### Shimmer Loading
**Use for**: Skeleton screens, content loading
```dart
ShimmerLoading(
  baseColor: DesignColors.neutral200,
  highlightColor: DesignColors.neutral100,
  child: SkeletonWidget(),
)
```

**Guidelines**:
- Duration: 1200ms (continuous loop)
- Subtle gradient movement
- Match content shape
- Don't overuse

### 5. **Success & Confirmation**

#### Success Checkmark
**Use for**: Form submissions, completed actions
```dart
SuccessAnimation(
  size: 48.0,
  onComplete: () => {},
)
```

**Behavior**:
- Scale up with bounce
- Checkmark draws in
- Duration: 600ms
- Satisfying completion feel

## 📱 Mobile-Specific Considerations

### 1. **One-Hand Usage**
- Keep important actions in thumb zone
- Bottom sheet animations should feel natural
- Swipe gestures should be responsive (200-300ms)

### 2. **Performance**
- Use `RepaintBoundary` for complex animations
- Avoid animating layout properties when possible
- Use `Transform` instead of changing position
- Consider using `AnimatedOpacity` instead of rebuilding

### 3. **Accessibility**
- Respect `prefers-reduced-motion`
- Provide alternative feedback for animations
- Ensure animations don't trigger motion sickness
- Test with screen readers

## 🚫 Anti-Patterns (What NOT to Do)

### ❌ Don't:
- Use animations longer than 500ms for standard interactions
- Animate too many things simultaneously
- Use jarring curves (e.g., sharp linear)
- Add animations that don't serve a purpose
- Make animations feel slow or laggy
- Use different timing for similar actions

### ✅ Do:
- Keep animations consistent across similar elements
- Use subtle animations that enhance UX
- Test performance on real devices
- Provide immediate feedback for user actions
- Use animations to guide attention
- Make animations feel responsive and natural

## 🎯 Animation Checklist

Before implementing an animation, ask:

- [ ] Does this animation serve a clear purpose?
- [ ] Is the duration appropriate (100-500ms)?
- [ ] Does it feel smooth and polished?
- [ ] Will it work on lower-end devices?
- [ ] Is it consistent with other animations?
- [ ] Does it respect accessibility preferences?
- [ ] Does it enhance trust and professionalism?

## 📚 Example Implementations

### Staggered List Animation
```dart
ListView.builder(
  itemCount: items.length,
  itemBuilder: (context, index) {
    return FadeInWidget(
      delay: Duration(milliseconds: index * 50),
      child: SlideInFromBottom(
        delay: Duration(milliseconds: index * 50),
        child: ModernCard(child: ItemWidget()),
      ),
    );
  },
)
```

### Button with Success Animation
```dart
StatefulBuilder(
  builder: (context, setState) {
    if (isSuccess) {
      return SuccessAnimation(
        onComplete: () => navigateNext(),
      );
    }
    return ModernButton(
      label: 'Submit',
      onPressed: () => handleSubmit(),
      isLoading: isLoading,
    );
  },
)
```

### Pull-to-Refresh with Animation
```dart
RefreshIndicator(
  onRefresh: () async {
    await refreshData();
  },
  child: AnimatedList(
    duration: DesignDurations.normal,
    // List items with animations
  ),
)
```

## 🎨 Design System Integration

All animations should use tokens from `DesignSystem`:

- **Durations**: `DesignDurations.*`
- **Curves**: `DesignCurves.*`
- **Colors**: `DesignColors.*`
- **Spacing**: `DesignSpacing.*`

This ensures consistency and makes updates easier.

---

**Remember**: The goal is to create a trustworthy, modern, and premium experience. Animations should enhance, not distract. When in doubt, opt for subtle and smooth over flashy and complex.

/// Modern Card Component with Smooth Animations
/// Features: Premium design, subtle lift effect, trust-focused

import 'package:flutter/material.dart';
import '../../theme/design_system.dart';
import '../animations/animation_utils.dart';

class ModernCard extends StatelessWidget {
  final Widget child;
  final VoidCallback? onTap;
  final EdgeInsets? padding;
  final EdgeInsets? margin;
  final Color? color;
  final double borderRadius;
  final List<BoxShadow>? shadows;
  final bool enableAnimation;
  final Duration? animationDelay;

  const ModernCard({
    super.key,
    required this.child,
    this.onTap,
    this.padding,
    this.margin,
    this.color,
    this.borderRadius = DesignRadius.card,
    this.shadows,
    this.enableAnimation = true,
    this.animationDelay,
  });

  @override
  Widget build(BuildContext context) {
    Widget card = Container(
      padding: padding ?? EdgeInsets.all(DesignSpacing.cardPadding),
      margin: margin ?? EdgeInsets.symmetric(
        horizontal: DesignSpacing.screenPadding,
        vertical: DesignSpacing.sm,
      ),
      decoration: BoxDecoration(
        color: color ?? Theme.of(context).cardColor,
        borderRadius: BorderRadius.circular(borderRadius),
        boxShadow: shadows ?? DesignColors.cardShadow,
        border: Border.all(
          color: DesignColors.neutral200,
          width: 1,
        ),
      ),
      child: child,
    );

    if (onTap != null) {
      card = AnimatedCard(
        onTap: onTap,
        padding: padding ?? EdgeInsets.all(DesignSpacing.cardPadding),
        margin: margin,
        color: color ?? Theme.of(context).cardColor,
        shadows: shadows,
        borderRadius: borderRadius,
        child: child,
      );
    }

    if (enableAnimation && animationDelay != null) {
      return FadeInWidget(
        delay: animationDelay!,
        child: SlideInFromBottom(
          delay: animationDelay!,
          offset: 20.0,
          child: card,
        ),
      );
    } else if (enableAnimation) {
      return FadeInWidget(child: card);
    }

    return card;
  }
}

/// Minimal card variant (no shadow, subtle border)
class ModernCardMinimal extends StatelessWidget {
  final Widget child;
  final VoidCallback? onTap;
  final EdgeInsets? padding;
  final EdgeInsets? margin;
  final Color? color;

  const ModernCardMinimal({
    super.key,
    required this.child,
    this.onTap,
    this.padding,
    this.margin,
    this.color,
  });

  @override
  Widget build(BuildContext context) {
    return ModernCard(
      onTap: onTap,
      padding: padding,
      margin: margin,
      color: color,
      shadows: [],
      borderRadius: DesignRadius.md,
      child: child,
    );
  }
}

/// Elevated card variant (prominent shadow)
class ModernCardElevated extends StatelessWidget {
  final Widget child;
  final VoidCallback? onTap;
  final EdgeInsets? padding;
  final EdgeInsets? margin;
  final Color? color;

  const ModernCardElevated({
    super.key,
    required this.child,
    this.onTap,
    this.padding,
    this.margin,
    this.color,
  });

  @override
  Widget build(BuildContext context) {
    return ModernCard(
      onTap: onTap,
      padding: padding,
      margin: margin,
      color: color,
      shadows: DesignColors.elevatedShadow,
      child: child,
    );
  }
}

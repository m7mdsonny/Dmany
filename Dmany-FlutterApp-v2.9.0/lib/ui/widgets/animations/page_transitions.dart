/// Custom Page Transitions for Modern Mobile App
/// Provides smooth, trust-focused page transitions

import 'package:flutter/material.dart';
import '../../theme/design_system.dart';
import 'animation_utils.dart';

/// Creates a smooth slide-from-right page transition (iOS-style)
class SlideFromRightPageRoute<T> extends PageRouteBuilder<T> {
  final Widget child;

  SlideFromRightPageRoute({required this.child})
      : super(
          pageBuilder: (context, animation, secondaryAnimation) => child,
          transitionDuration: DesignDurations.pageTransition,
          reverseTransitionDuration: DesignDurations.pageTransition,
          transitionsBuilder: (context, animation, secondaryAnimation, child) {
            return PageTransitions.slideFromRight(
              context,
              animation,
              secondaryAnimation,
              child,
            );
          },
        );
}

/// Creates a smooth fade page transition
class FadePageRoute<T> extends PageRouteBuilder<T> {
  final Widget child;

  FadePageRoute({required this.child})
      : super(
          pageBuilder: (context, animation, secondaryAnimation) => child,
          transitionDuration: DesignDurations.normal,
          reverseTransitionDuration: DesignDurations.normal,
          transitionsBuilder: (context, animation, secondaryAnimation, child) {
            return PageTransitions.fadeTransition(
              context,
              animation,
              secondaryAnimation,
              child,
            );
          },
        );
}

/// Creates a scale and fade page transition (modern modal style)
class ScaleFadePageRoute<T> extends PageRouteBuilder<T> {
  final Widget child;

  ScaleFadePageRoute({required this.child})
      : super(
          pageBuilder: (context, animation, secondaryAnimation) => child,
          transitionDuration: DesignDurations.normal,
          reverseTransitionDuration: DesignDurations.fast,
          transitionsBuilder: (context, animation, secondaryAnimation, child) {
            return PageTransitions.scaleAndFade(
              context,
              animation,
              secondaryAnimation,
              child,
            );
          },
        );
}

/// Creates a slide from bottom page transition (bottom sheet style)
class SlideFromBottomPageRoute<T> extends PageRouteBuilder<T> {
  final Widget child;

  SlideFromBottomPageRoute({required this.child})
      : super(
          pageBuilder: (context, animation, secondaryAnimation) => child,
          transitionDuration: DesignDurations.slow,
          reverseTransitionDuration: DesignDurations.normal,
          transitionsBuilder: (context, animation, secondaryAnimation, child) {
            return PageTransitions.slideFromBottom(
              context,
              animation,
              secondaryAnimation,
              child,
            );
          },
        );
}

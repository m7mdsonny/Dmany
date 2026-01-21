/// Modern Trust-Focused Button Component
/// Features: Smooth animations, premium design, thumb-friendly

import 'package:flutter/material.dart';
import '../../theme/design_system.dart';
import '../animations/animation_utils.dart';

class ModernButton extends StatelessWidget {
  final String label;
  final VoidCallback? onPressed;
  final IconData? icon;
  final bool isLoading;
  final ModernButtonStyle style;
  final double? width;
  final double height;
  final EdgeInsets? padding;

  const ModernButton({
    super.key,
    required this.label,
    this.onPressed,
    this.icon,
    this.isLoading = false,
    this.style = ModernButtonStyle.primary,
    this.width,
    this.height = DesignMobile.buttonMinHeight,
    this.padding,
  });

  @override
  Widget build(BuildContext context) {
    final buttonStyle = _getButtonStyle(context);

    Widget buttonContent = Row(
      mainAxisSize: MainAxisSize.min,
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        if (isLoading) ...[
          SizedBox(
            width: 20,
            height: 20,
            child: CircularProgressIndicator(
              strokeWidth: 2,
              valueColor: AlwaysStoppedAnimation<Color>(
                buttonStyle.textColor,
              ),
            ),
          ),
          SizedBox(width: DesignSpacing.sm),
        ] else if (icon != null) ...[
          Icon(icon, size: 20, color: buttonStyle.textColor),
          SizedBox(width: DesignSpacing.sm),
        ],
        Text(
          label,
          style: DesignTypography.labelLarge(
            color: buttonStyle.textColor,
            fontWeight: DesignTypography.semiBold,
          ),
        ),
      ],
    );

    Widget button = Container(
      width: width,
      height: height,
      decoration: BoxDecoration(
        gradient: buttonStyle.gradient,
        color: buttonStyle.gradient == null ? buttonStyle.backgroundColor : null,
        borderRadius: BorderRadius.circular(DesignRadius.button),
        boxShadow: onPressed != null && !isLoading
            ? DesignColors.buttonShadow
            : null,
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: isLoading ? null : onPressed,
          borderRadius: BorderRadius.circular(DesignRadius.button),
          child: Padding(
            padding: padding ??
                EdgeInsets.symmetric(
                  horizontal: DesignSpacing.buttonPaddingHorizontal,
                  vertical: DesignSpacing.buttonPaddingVertical,
                ),
            child: Center(child: buttonContent),
          ),
        ),
      ),
    );

    return onPressed != null && !isLoading
        ? AnimatedButtonPress(
            onPressed: onPressed,
            child: button,
          )
        : button;
  }

  _ModernButtonStyle _getButtonStyle(BuildContext context) {
    switch (style) {
      case ModernButtonStyle.primary:
        return _ModernButtonStyle(
          backgroundColor: DesignColors.trustBlue,
          gradient: DesignColors.trustGradient,
          textColor: Colors.white,
        );
      case ModernButtonStyle.secondary:
        return _ModernButtonStyle(
          backgroundColor: DesignColors.neutral200,
          textColor: DesignColors.neutral900,
        );
      case ModernButtonStyle.outline:
        return _ModernButtonStyle(
          backgroundColor: Colors.transparent,
          textColor: DesignColors.trustBlue,
          borderColor: DesignColors.trustBlue,
        );
      case ModernButtonStyle.success:
        return _ModernButtonStyle(
          backgroundColor: DesignColors.successGreen,
          gradient: DesignColors.successGradient,
          textColor: Colors.white,
        );
      case ModernButtonStyle.ghost:
        return _ModernButtonStyle(
          backgroundColor: Colors.transparent,
          textColor: DesignColors.neutral700,
        );
    }
  }
}

enum ModernButtonStyle {
  primary,
  secondary,
  outline,
  success,
  ghost,
}

class _ModernButtonStyle {
  final Color backgroundColor;
  final LinearGradient? gradient;
  final Color textColor;
  final Color? borderColor;

  _ModernButtonStyle({
    required this.backgroundColor,
    this.gradient,
    required this.textColor,
    this.borderColor,
  });
}

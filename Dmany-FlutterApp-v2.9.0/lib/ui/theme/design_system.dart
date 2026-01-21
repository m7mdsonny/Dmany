/// Modern Design System for Trust-Focused Mobile App
/// This file defines colors, typography, spacing, and animations
/// for a premium, professional mobile experience

import 'package:flutter/material.dart';

// ==========================================
// 🎨 MODERN COLOR PALETTE
// ==========================================

class DesignColors {
  // Trust-Focused Primary Colors
  static const Color trustBlue = Color(0xFF2563EB); // Primary trust blue
  static const Color trustBlueLight = Color(0xFF3B82F6); // Lighter blue
  static const Color trustBlueDark = Color(0xFF1E40AF); // Darker blue
  
  static const Color trustIndigo = Color(0xFF4F46E5); // Secondary indigo
  static const Color trustIndigoLight = Color(0xFF6366F1); // Lighter indigo
  static const Color trustIndigoDark = Color(0xFF4338CA); // Darker indigo
  
  // Calm, Modern Neutrals
  static const Color neutral50 = Color(0xFFF9FAFB);
  static const Color neutral100 = Color(0xFFF3F4F6);
  static const Color neutral200 = Color(0xFFE5E7EB);
  static const Color neutral300 = Color(0xFFD1D5DB);
  static const Color neutral400 = Color(0xFF9CA3AF);
  static const Color neutral500 = Color(0xFF6B7280);
  static const Color neutral600 = Color(0xFF4B5563);
  static const Color neutral700 = Color(0xFF374151);
  static const Color neutral800 = Color(0xFF1F2937);
  static const Color neutral900 = Color(0xFF111827);
  
  // Success & Status Colors (Soft)
  static const Color successGreen = Color(0xFF10B981);
  static const Color successGreenLight = Color(0xFF34D399);
  static const Color warningYellow = Color(0xFFF59E0B);
  static const Color warningYellowLight = Color(0xFFFBBF24);
  static const Color errorRed = Color(0xFFEF4444);
  static const Color errorRedLight = Color(0xFFF87171);
  static const Color infoBlue = Color(0xFF3B82F6);
  
  // Background Colors
  static const Color backgroundLight = Color(0xFFFAFBFC);
  static const Color backgroundDark = Color(0xFF0F172A);
  static const Color surfaceLight = Color(0xFFFFFFFF);
  static const Color surfaceDark = Color(0xFF1E293B);
  
  // Gradient Colors for Trust Elements
  static const LinearGradient trustGradient = LinearGradient(
    colors: [trustBlue, trustIndigo],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );
  
  static const LinearGradient successGradient = LinearGradient(
    colors: [successGreen, Color(0xFF059669)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );
  
  // Shadows (Soft, Professional)
  static List<BoxShadow> get cardShadow => [
    BoxShadow(
      color: trustBlue.withOpacity(0.08),
      blurRadius: 12,
      offset: const Offset(0, 4),
      spreadRadius: 0,
    ),
  ];
  
  static List<BoxShadow> get buttonShadow => [
    BoxShadow(
      color: trustBlue.withOpacity(0.25),
      blurRadius: 12,
      offset: const Offset(0, 4),
      spreadRadius: 0,
    ),
  ];
  
  static List<BoxShadow> get elevatedShadow => [
    BoxShadow(
      color: neutral900.withOpacity(0.1),
      blurRadius: 20,
      offset: const Offset(0, 8),
      spreadRadius: -4,
    ),
  ];
}

// ==========================================
// 📝 MODERN TYPOGRAPHY
// ==========================================

class DesignTypography {
  // Font Family
  static const String fontFamily = "Manrope";
  static const String displayFontFamily = "Manrope";
  
  // Font Sizes (Mobile-Optimized)
  static const double xs = 12.0;
  static const double sm = 14.0;
  static const double base = 16.0;
  static const double lg = 18.0;
  static const double xl = 20.0;
  static const double xxl = 24.0;
  static const double xxxl = 28.0;
  static const double display = 32.0;
  
  // Font Weights
  static const FontWeight light = FontWeight.w300;
  static const FontWeight regular = FontWeight.w400;
  static const FontWeight medium = FontWeight.w500;
  static const FontWeight semiBold = FontWeight.w600;
  static const FontWeight bold = FontWeight.w700;
  static const FontWeight extraBold = FontWeight.w800;
  
  // Text Styles
  static TextStyle headlineLarge({
    Color? color,
    FontWeight? fontWeight,
  }) =>
      TextStyle(
        fontFamily: fontFamily,
        fontSize: xxxl,
        fontWeight: fontWeight ?? bold,
        color: color ?? DesignColors.neutral900,
        height: 1.2,
        letterSpacing: -0.5,
      );
  
  static TextStyle headlineMedium({
    Color? color,
    FontWeight? fontWeight,
  }) =>
      TextStyle(
        fontFamily: fontFamily,
        fontSize: xxl,
        fontWeight: fontWeight ?? semiBold,
        color: color ?? DesignColors.neutral900,
        height: 1.3,
        letterSpacing: -0.3,
      );
  
  static TextStyle titleLarge({
    Color? color,
    FontWeight? fontWeight,
  }) =>
      TextStyle(
        fontFamily: fontFamily,
        fontSize: xl,
        fontWeight: fontWeight ?? semiBold,
        color: color ?? DesignColors.neutral900,
        height: 1.4,
      );
  
  static TextStyle titleMedium({
    Color? color,
    FontWeight? fontWeight,
  }) =>
      TextStyle(
        fontFamily: fontFamily,
        fontSize: lg,
        fontWeight: fontWeight ?? medium,
        color: color ?? DesignColors.neutral900,
        height: 1.4,
      );
  
  static TextStyle bodyLarge({
    Color? color,
    FontWeight? fontWeight,
  }) =>
      TextStyle(
        fontFamily: fontFamily,
        fontSize: base,
        fontWeight: fontWeight ?? regular,
        color: color ?? DesignColors.neutral700,
        height: 1.5,
      );
  
  static TextStyle bodyMedium({
    Color? color,
    FontWeight? fontWeight,
  }) =>
      TextStyle(
        fontFamily: fontFamily,
        fontSize: sm,
        fontWeight: fontWeight ?? regular,
        color: color ?? DesignColors.neutral600,
        height: 1.5,
      );
  
  static TextStyle bodySmall({
    Color? color,
    FontWeight? fontWeight,
  }) =>
      TextStyle(
        fontFamily: fontFamily,
        fontSize: xs,
        fontWeight: fontWeight ?? regular,
        color: color ?? DesignColors.neutral500,
        height: 1.5,
      );
  
  static TextStyle labelLarge({
    Color? color,
    FontWeight? fontWeight,
  }) =>
      TextStyle(
        fontFamily: fontFamily,
        fontSize: sm,
        fontWeight: fontWeight ?? medium,
        color: color ?? DesignColors.neutral700,
        height: 1.4,
        letterSpacing: 0.1,
      );
  
  static TextStyle labelMedium({
    Color? color,
    FontWeight? fontWeight,
  }) =>
      TextStyle(
        fontFamily: fontFamily,
        fontSize: xs,
        fontWeight: fontWeight ?? medium,
        color: color ?? DesignColors.neutral600,
        height: 1.4,
        letterSpacing: 0.1,
      );
}

// ==========================================
// 📏 SPACING SYSTEM
// ==========================================

class DesignSpacing {
  // Base spacing unit (4px)
  static const double xs = 4.0;
  static const double sm = 8.0;
  static const double md = 12.0;
  static const double base = 16.0;
  static const double lg = 24.0;
  static const double xl = 32.0;
  static const double xxl = 48.0;
  static const double xxxl = 64.0;
  
  // Component-specific spacing
  static const double cardPadding = 16.0;
  static const double cardGap = 12.0;
  static const double sectionGap = 24.0;
  static const double screenPadding = 16.0;
  static const double screenPaddingHorizontal = 20.0;
  
  // Button spacing
  static const double buttonPaddingVertical = 14.0;
  static const double buttonPaddingHorizontal = 24.0;
  static const double buttonGap = 8.0;
}

// ==========================================
// 🎭 BORDER RADIUS
// ==========================================

class DesignRadius {
  static const double xs = 4.0;
  static const double sm = 8.0;
  static const double md = 12.0;
  static const double lg = 16.0;
  static const double xl = 24.0;
  static const double full = 9999.0;
  
  // Component-specific
  static const double card = 16.0;
  static const double button = 12.0;
  static const double chip = 8.0;
  static const double input = 12.0;
  static const double bottomSheet = 24.0;
}

// ==========================================
// ⏱️ ANIMATION DURATIONS
// ==========================================

class DesignDurations {
  static const Duration instant = Duration(milliseconds: 100);
  static const Duration fast = Duration(milliseconds: 200);
  static const Duration normal = Duration(milliseconds: 300);
  static const Duration slow = Duration(milliseconds: 400);
  static const Duration slower = Duration(milliseconds: 500);
  
  // Component-specific
  static const Duration pageTransition = Duration(milliseconds: 300);
  static const Duration buttonPress = Duration(milliseconds: 100);
  static const Duration cardHover = Duration(milliseconds: 200);
  static const Duration loadingAnimation = Duration(milliseconds: 1200);
}

// ==========================================
// 🎬 ANIMATION CURVES
// ==========================================

class DesignCurves {
  // Smooth, premium feeling curves
  static const Curve standard = Curves.easeInOut;
  static const Curve smooth = Curves.easeInOutCubic;
  static const Curve spring = Curves.easeOutCubic;
  static const Curve bounce = Curves.easeOutBack;
  static const Curve sharp = Curves.easeInOutQuad;
  
  // Custom curves for specific interactions
  static const Curve buttonPress = Curves.easeInOut;
  static const Curve pageTransition = Curves.easeInOutCubic;
  static const Curve cardLift = Curves.easeOutCubic;
}

// ==========================================
// 📱 MOBILE-SPECIFIC DESIGN TOKENS
// ==========================================

class DesignMobile {
  // Button sizes (thumb-friendly)
  static const double buttonMinHeight = 48.0;
  static const double buttonMinWidth = 120.0;
  static const double touchTarget = 44.0; // Minimum touch target
  
  // Card dimensions
  static const double cardMinHeight = 100.0;
  static const double cardElevation = 2.0;
  
  // Bottom navigation
  static const double bottomNavHeight = 72.0;
  static const double bottomNavIconSize = 24.0;
  
  // Safe areas
  static const double safeAreaPadding = 20.0;
  
  // Grid spacing
  static const double gridGap = 12.0;
  static const int gridCrossAxisCount = 2;
}

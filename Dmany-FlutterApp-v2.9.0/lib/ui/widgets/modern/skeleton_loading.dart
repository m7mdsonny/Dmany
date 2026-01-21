/// Skeleton Loading Widgets
/// Provides shimmer loading effects for modern UI

import 'package:flutter/material.dart';
import '../../theme/design_system.dart';
import '../animations/animation_utils.dart';

/// Skeleton text line with shimmer effect
class SkeletonText extends StatelessWidget {
  final double width;
  final double height;
  final double borderRadius;

  const SkeletonText({
    super.key,
    this.width = double.infinity,
    this.height = 16.0,
    this.borderRadius = 4.0,
  });

  @override
  Widget build(BuildContext context) {
    return ShimmerLoading(
      baseColor: DesignColors.neutral200,
      highlightColor: DesignColors.neutral100,
      child: Container(
        width: width,
        height: height,
        decoration: BoxDecoration(
          color: DesignColors.neutral200,
          borderRadius: BorderRadius.circular(borderRadius),
        ),
      ),
    );
  }
}

/// Skeleton card with shimmer effect
class SkeletonCard extends StatelessWidget {
  final double? width;
  final double height;
  final EdgeInsets? padding;
  final EdgeInsets? margin;

  const SkeletonCard({
    super.key,
    this.width,
    this.height = 200.0,
    this.padding,
    this.margin,
  });

  @override
  Widget build(BuildContext context) {
    return ShimmerLoading(
      baseColor: DesignColors.neutral200,
      highlightColor: DesignColors.neutral100,
      child: Container(
        width: width,
        height: height,
        padding: padding ?? EdgeInsets.all(DesignSpacing.cardPadding),
        margin: margin ?? EdgeInsets.symmetric(
          horizontal: DesignSpacing.screenPadding,
          vertical: DesignSpacing.sm,
        ),
        decoration: BoxDecoration(
          color: DesignColors.neutral200,
          borderRadius: BorderRadius.circular(DesignRadius.card),
          border: Border.all(
            color: DesignColors.neutral200,
            width: 1,
          ),
        ),
      ),
    );
  }
}

/// Skeleton list item
class SkeletonListItem extends StatelessWidget {
  final bool showAvatar;
  final int lines;

  const SkeletonListItem({
    super.key,
    this.showAvatar = false,
    this.lines = 3,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.all(DesignSpacing.cardPadding),
      margin: EdgeInsets.symmetric(
        horizontal: DesignSpacing.screenPadding,
        vertical: DesignSpacing.sm,
      ),
      decoration: BoxDecoration(
        color: Theme.of(context).cardColor,
        borderRadius: BorderRadius.circular(DesignRadius.card),
        border: Border.all(
          color: DesignColors.neutral200,
          width: 1,
        ),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (showAvatar) ...[
            ShimmerLoading(
              baseColor: DesignColors.neutral200,
              highlightColor: DesignColors.neutral100,
              child: Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: DesignColors.neutral200,
                  shape: BoxShape.circle,
                ),
              ),
            ),
            SizedBox(width: DesignSpacing.base),
          ],
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: List.generate(
                lines,
                (index) => Padding(
                  padding: EdgeInsets.only(
                    bottom: index < lines - 1 ? DesignSpacing.sm : 0,
                  ),
                  child: SkeletonText(
                    width: index == 0
                        ? double.infinity
                        : index == lines - 1
                            ? MediaQuery.of(context).size.width * 0.6
                            : MediaQuery.of(context).size.width * 0.8,
                    height: 16.0,
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

/// Skeleton button
class SkeletonButton extends StatelessWidget {
  final double? width;
  final double height;

  const SkeletonButton({
    super.key,
    this.width,
    this.height = DesignMobile.buttonMinHeight,
  });

  @override
  Widget build(BuildContext context) {
    return ShimmerLoading(
      baseColor: DesignColors.neutral200,
      highlightColor: DesignColors.neutral100,
      child: Container(
        width: width,
        height: height,
        decoration: BoxDecoration(
          color: DesignColors.neutral200,
          borderRadius: BorderRadius.circular(DesignRadius.button),
        ),
      ),
    );
  }
}

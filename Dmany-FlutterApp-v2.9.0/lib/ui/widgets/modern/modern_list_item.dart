/// Modern List Item Component
/// Trust-focused list item with smooth animations

import 'package:flutter/material.dart';
import '../../theme/design_system.dart';
import '../animations/animation_utils.dart';
import 'modern_card.dart';

class ModernListItem extends StatelessWidget {
  final String title;
  final String? subtitle;
  final String? trailing;
  final Widget? leading;
  final VoidCallback? onTap;
  final bool showDivider;
  final EdgeInsets? padding;
  final int? animationDelay;

  const ModernListItem({
    super.key,
    required this.title,
    this.subtitle,
    this.trailing,
    this.leading,
    this.onTap,
    this.showDivider = false,
    this.padding,
    this.animationDelay,
  });

  @override
  Widget build(BuildContext context) {
    Widget content = Container(
      padding: padding ?? EdgeInsets.symmetric(
        horizontal: DesignSpacing.screenPadding,
        vertical: DesignSpacing.md,
      ),
      child: Row(
        children: [
          if (leading != null) ...[
            leading!,
            SizedBox(width: DesignSpacing.base),
          ],
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  title,
                  style: DesignTypography.titleMedium(
                    color: DesignColors.neutral900,
                    fontWeight: DesignTypography.medium,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                if (subtitle != null) ...[
                  SizedBox(height: DesignSpacing.xs),
                  Text(
                    subtitle!,
                    style: DesignTypography.bodyMedium(
                      color: DesignColors.neutral600,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ],
            ),
          ),
          if (trailing != null) ...[
            SizedBox(width: DesignSpacing.base),
            Text(
              trailing!,
              style: DesignTypography.bodyMedium(
                color: DesignColors.neutral600,
              ),
            ),
          ],
          if (onTap != null) ...[
            SizedBox(width: DesignSpacing.sm),
            Icon(
              Icons.chevron_right,
              color: DesignColors.neutral400,
              size: 20,
            ),
          ],
        ],
      ),
    );

    if (showDivider) {
      content = Column(
        children: [
          content,
          Divider(
            height: 1,
            thickness: 1,
            color: DesignColors.neutral200,
            indent: DesignSpacing.screenPadding,
          ),
        ],
      );
    }

    if (animationDelay != null) {
      return FadeInWidget(
        delay: Duration(milliseconds: animationDelay!),
        child: SlideInFromBottom(
          delay: Duration(milliseconds: animationDelay!),
          offset: 10.0,
          child: content,
        ),
      );
    }

    return onTap != null
        ? InkWell(
            onTap: onTap,
            child: content,
          )
        : content;
  }
}

/// Modern list item with card styling
class ModernListItemCard extends StatelessWidget {
  final String title;
  final String? subtitle;
  final String? trailing;
  final Widget? leading;
  final Widget? trailingWidget;
  final VoidCallback? onTap;
  final EdgeInsets? padding;

  const ModernListItemCard({
    super.key,
    required this.title,
    this.subtitle,
    this.trailing,
    this.leading,
    this.trailingWidget,
    this.onTap,
    this.padding,
  });

  @override
  Widget build(BuildContext context) {
    return ModernCard(
      onTap: onTap,
      padding: padding,
      child: Row(
        children: [
          if (leading != null) ...[
            leading!,
            SizedBox(width: DesignSpacing.base),
          ],
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  title,
                  style: DesignTypography.titleMedium(
                    color: DesignColors.neutral900,
                    fontWeight: DesignTypography.semiBold,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                if (subtitle != null) ...[
                  SizedBox(height: DesignSpacing.xs),
                  Text(
                    subtitle!,
                    style: DesignTypography.bodyMedium(
                      color: DesignColors.neutral600,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ],
            ),
          ),
          if (trailing != null || trailingWidget != null) ...[
            SizedBox(width: DesignSpacing.base),
            trailingWidget ??
                Text(
                  trailing!,
                  style: DesignTypography.bodyMedium(
                    color: DesignColors.neutral600,
                  ),
                ),
          ],
          if (onTap != null) ...[
            SizedBox(width: DesignSpacing.sm),
            Icon(
              Icons.chevron_right,
              color: DesignColors.neutral400,
              size: 20,
            ),
          ],
        ],
      ),
    );
  }
}

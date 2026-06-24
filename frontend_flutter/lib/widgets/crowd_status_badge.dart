import 'package:flutter/material.dart';

class CrowdStatusBadge extends StatelessWidget {
  final String? crowdLevel;
  final String? crowdLabel;
  final double? crowdScore;

  const CrowdStatusBadge({
    super.key,
    this.crowdLevel,
    this.crowdLabel,
    this.crowdScore,
  });

  @override
  Widget build(BuildContext context) {
    Color backgroundColor;
    Color textColor = Colors.white;
    String text = crowdLabel ?? 'Unknown';

    switch (crowdLevel) {
      case 'low':
        backgroundColor = Colors.green.shade600;
        break;
      case 'moderate':
        backgroundColor = Colors.blue.shade600;
        break;
      case 'high':
        backgroundColor = Colors.orange.shade600;
        break;
      case 'packed':
        backgroundColor = Colors.red.shade600;
        break;
      default:
        backgroundColor = Colors.grey.shade600;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: backgroundColor,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(
            Icons.people,
            size: 14,
            color: textColor,
          ),
          const SizedBox(width: 4),
          Text(
            text,
            style: TextStyle(
              color: textColor,
              fontSize: 12,
              fontWeight: FontWeight.bold,
            ),
          ),
          if (crowdScore != null) ...[
            const SizedBox(width: 4),
            Text(
              '(${(crowdScore! * 100).toInt()}%)',
              style: TextStyle(
                color: textColor.withValues(alpha: 0.8),
                fontSize: 10,
              ),
            ),
          ]
        ],
      ),
    );
  }
}

import 'package:flutter/material.dart';
import 'package:frontend_flutter/models/destination_model.dart';
import 'package:frontend_flutter/screens/destination_detail_screen.dart';
import 'package:frontend_flutter/utils/formatters.dart';

class DestinationCard extends StatelessWidget {
  final DestinationModel destination;
  final bool isFavorite;
  final VoidCallback? onTap;
  final VoidCallback? onFavoritePressed;
  final VoidCallback? onAddToPlanPressed;

  const DestinationCard({
    super.key,
    required this.destination,
    this.isFavorite = false,
    this.onTap,
    this.onFavoritePressed,
    this.onAddToPlanPressed,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      clipBehavior: Clip.antiAlias,
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
      child: InkWell(
        onTap: onTap ?? () => _openDetail(context),
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(
                    child: Text(
                      destination.name,
                      style: const TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                  if (onFavoritePressed != null)
                    IconButton(
                      tooltip: isFavorite ? 'Hapus dari favorit' : 'Tambah ke favorit',
                      onPressed: onFavoritePressed,
                      icon: Icon(isFavorite ? Icons.favorite : Icons.favorite_border),
                      color: isFavorite ? Colors.red.shade600 : Colors.grey.shade700,
                    ),
                ],
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.blue.shade50,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      destination.categoryName,
                      style: TextStyle(
                        color: Colors.blue.shade700,
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                  const Spacer(),
                  if (onAddToPlanPressed != null)
                    TextButton.icon(
                      onPressed: onAddToPlanPressed,
                      icon: const Icon(Icons.add_location_alt_outlined, size: 18),
                      label: const Text('Rencana'),
                    ),
                ],
              ),
              const SizedBox(height: 8),
              if (destination.address != null) ...[
                Row(
                  children: [
                    const Icon(Icons.location_on, size: 16, color: Colors.grey),
                    const SizedBox(width: 4),
                    Expanded(
                      child: Text(
                        destination.address!,
                        style: const TextStyle(color: Colors.grey, fontSize: 14),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
              ],
              Row(
                children: [
                  const Icon(Icons.people_outline, size: 16, color: Colors.grey),
                  const SizedBox(width: 4),
                  Text(
                    'Kapasitas: ${destination.maxCapacity ?? '-'}',
                    style: const TextStyle(color: Colors.grey, fontSize: 14),
                  ),
                  const SizedBox(width: 16),
                  const Icon(Icons.confirmation_number_outlined, size: 16, color: Colors.grey),
                  const SizedBox(width: 4),
                  Text(
                    Formatters.formatRupiah(destination.ticketPrice),
                    style: const TextStyle(color: Colors.grey, fontSize: 14),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _openDetail(BuildContext context) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => DestinationDetailScreen(destinationId: destination.id),
      ),
    );
  }
}

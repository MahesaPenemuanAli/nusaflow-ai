import 'package:flutter/material.dart';
import 'package:frontend_flutter/models/destination_model.dart';
import 'package:frontend_flutter/widgets/destination_card.dart';
import 'package:frontend_flutter/widgets/empty_view.dart';

class FavoritesScreen extends StatelessWidget {
  final List<DestinationModel> destinations;
  final Set<int> favoriteDestinationIds;
  final ValueChanged<DestinationModel> onOpenDestination;
  final ValueChanged<DestinationModel> onToggleFavorite;
  final ValueChanged<DestinationModel> onAddToPlan;

  const FavoritesScreen({
    super.key,
    required this.destinations,
    required this.favoriteDestinationIds,
    required this.onOpenDestination,
    required this.onToggleFavorite,
    required this.onAddToPlan,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Favorit'),
      ),
      body: destinations.isEmpty
          ? const EmptyView(
              message: 'Belum ada destinasi favorit. Tandai destinasi dari menu Jelajah.',
            )
          : ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: destinations.length,
              itemBuilder: (context, index) {
                final destination = destinations[index];
                return DestinationCard(
                  destination: destination,
                  isFavorite: favoriteDestinationIds.contains(destination.id),
                  onTap: () => onOpenDestination(destination),
                  onFavoritePressed: () => onToggleFavorite(destination),
                  onAddToPlanPressed: () => onAddToPlan(destination),
                );
              },
            ),
    );
  }
}

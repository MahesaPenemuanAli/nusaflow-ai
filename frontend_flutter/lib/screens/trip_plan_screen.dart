import 'package:flutter/material.dart';
import 'package:frontend_flutter/models/destination_model.dart';
import 'package:frontend_flutter/utils/formatters.dart';
import 'package:frontend_flutter/widgets/empty_view.dart';

class TripPlanScreen extends StatelessWidget {
  final List<DestinationModel> destinations;
  final ValueChanged<DestinationModel> onOpenDestination;
  final ValueChanged<DestinationModel> onRemoveDestination;
  final VoidCallback? onClearPlan;
  final ReorderCallback onReorder;

  const TripPlanScreen({
    super.key,
    required this.destinations,
    required this.onOpenDestination,
    required this.onRemoveDestination,
    required this.onClearPlan,
    required this.onReorder,
  });

  double get _estimatedTicketTotal {
    return destinations.fold<double>(
      0,
      (total, destination) => total + (destination.ticketPrice ?? 0),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Rencana'),
        actions: [
          if (onClearPlan != null)
            IconButton(
              tooltip: 'Kosongkan rencana',
              onPressed: onClearPlan,
              icon: const Icon(Icons.delete_sweep_outlined),
            ),
        ],
      ),
      body: destinations.isEmpty
          ? const EmptyView(
              message: 'Rencana masih kosong. Tambahkan destinasi dari Jelajah atau Favorit.',
            )
          : Column(
              children: [
                _TripSummary(
                  count: destinations.length,
                  ticketTotal: _estimatedTicketTotal,
                ),
                Expanded(
                  child: ReorderableListView.builder(
                    padding: const EdgeInsets.fromLTRB(16, 8, 16, 16),
                    itemCount: destinations.length,
                    onReorder: onReorder,
                    itemBuilder: (context, index) {
                      final destination = destinations[index];
                      return Card(
                        key: ValueKey(destination.id),
                        margin: const EdgeInsets.only(bottom: 12),
                        elevation: 1,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: ListTile(
                          leading: CircleAvatar(
                            child: Text('${index + 1}'),
                          ),
                          title: Text(
                            destination.name,
                            style: const TextStyle(fontWeight: FontWeight.w700),
                          ),
                          subtitle: Text(
                            '${destination.categoryName} - ${Formatters.formatRupiah(destination.ticketPrice)}',
                          ),
                          onTap: () => onOpenDestination(destination),
                          trailing: Wrap(
                            spacing: 4,
                            crossAxisAlignment: WrapCrossAlignment.center,
                            children: [
                              ReorderableDragStartListener(
                                index: index,
                                child: const Padding(
                                  padding: EdgeInsets.all(8),
                                  child: Icon(Icons.drag_handle),
                                ),
                              ),
                              IconButton(
                                tooltip: 'Hapus',
                                icon: const Icon(Icons.remove_circle_outline),
                                onPressed: () => onRemoveDestination(destination),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),
              ],
            ),
    );
  }
}

class _TripSummary extends StatelessWidget {
  final int count;
  final double ticketTotal;

  const _TripSummary({
    required this.count,
    required this.ticketTotal,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      color: Theme.of(context).colorScheme.primaryContainer,
      child: Row(
        children: [
          const Icon(Icons.route),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              '$count destinasi dalam rencana',
              style: const TextStyle(fontWeight: FontWeight.w700),
            ),
          ),
          Text(
            Formatters.formatRupiah(ticketTotal),
            style: const TextStyle(fontWeight: FontWeight.w700),
          ),
        ],
      ),
    );
  }
}

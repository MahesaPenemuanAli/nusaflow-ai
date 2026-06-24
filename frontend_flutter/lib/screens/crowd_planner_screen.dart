import 'package:flutter/material.dart';
import 'package:frontend_flutter/models/crowd_status_model.dart';
import 'package:frontend_flutter/models/destination_model.dart';
import 'package:frontend_flutter/services/api_service.dart';
import 'package:frontend_flutter/widgets/crowd_status_badge.dart';
import 'package:frontend_flutter/widgets/destination_card.dart';
import 'package:frontend_flutter/widgets/empty_view.dart';
import 'package:frontend_flutter/widgets/error_view.dart';
import 'package:frontend_flutter/widgets/loading_view.dart';

class CrowdPlannerScreen extends StatefulWidget {
  final bool Function(int destinationId) isDestinationFavorite;
  final ValueChanged<List<DestinationModel>>? onDestinationsLoaded;
  final ValueChanged<DestinationModel> onOpenDestination;
  final ValueChanged<DestinationModel> onToggleFavorite;
  final ValueChanged<DestinationModel> onAddToPlan;

  const CrowdPlannerScreen({
    super.key,
    required this.isDestinationFavorite,
    this.onDestinationsLoaded,
    required this.onOpenDestination,
    required this.onToggleFavorite,
    required this.onAddToPlan,
  });

  @override
  State<CrowdPlannerScreen> createState() => _CrowdPlannerScreenState();
}

class _CrowdPlannerScreenState extends State<CrowdPlannerScreen> {
  final ApiService _apiService = ApiService();

  List<DestinationModel> _destinations = [];
  CrowdStatusModel? _crowdStatus;
  bool _isLoading = true;
  bool _isChecking = false;
  String? _errorMessage;
  int? _selectedDestinationId;
  DateTime _selectedDate = DateTime.now();
  int _selectedHour = DateTime.now().hour;

  DestinationModel? get _selectedDestination {
    for (final destination in _destinations) {
      if (destination.id == _selectedDestinationId) {
        return destination;
      }
    }
    return null;
  }

  @override
  void initState() {
    super.initState();
    _fetchDestinations();
  }

  Future<void> _fetchDestinations() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final destinations = await _apiService.getDestinations(limit: 50);
      if (!mounted) return;

      setState(() {
        _destinations = destinations;
        _selectedDestinationId = destinations.isEmpty ? null : destinations.first.id;
        _isLoading = false;
      });
      widget.onDestinationsLoaded?.call(destinations);
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _errorMessage = e.toString();
        _isLoading = false;
      });
    }
  }

  Future<void> _checkCrowdStatus() async {
    final selectedDestinationId = _selectedDestinationId;
    if (selectedDestinationId == null) return;

    setState(() {
      _isChecking = true;
      _errorMessage = null;
    });

    try {
      final status = await _apiService.getCrowdStatus(
        selectedDestinationId,
        date: _formatDate(_selectedDate),
        hour: _selectedHour,
      );

      if (!mounted) return;
      setState(() {
        _crowdStatus = status;
        _isChecking = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _errorMessage = e.toString();
        _isChecking = false;
      });
    }
  }

  Future<void> _pickDate() async {
    final selected = await showDatePicker(
      context: context,
      initialDate: _selectedDate,
      firstDate: DateTime.now().subtract(const Duration(days: 1)),
      lastDate: DateTime.now().add(const Duration(days: 30)),
    );

    if (selected == null) return;
    setState(() {
      _selectedDate = selected;
      _crowdStatus = null;
    });
  }

  String _formatDate(DateTime value) {
    final month = value.month.toString().padLeft(2, '0');
    final day = value.day.toString().padLeft(2, '0');
    return '${value.year}-$month-$day';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Prediksi'),
      ),
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const LoadingView();
    }

    if (_errorMessage != null && _destinations.isEmpty) {
      return ErrorView(
        message: _errorMessage!,
        onRetry: _fetchDestinations,
      );
    }

    if (_destinations.isEmpty) {
      return const EmptyView(message: 'Belum ada destinasi untuk diprediksi.');
    }

    final selectedDestination = _selectedDestination;

    return RefreshIndicator(
      onRefresh: _fetchDestinations,
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          DropdownButtonFormField<int>(
            initialValue: _selectedDestinationId,
            isExpanded: true,
            decoration: const InputDecoration(
              labelText: 'Destinasi',
              border: OutlineInputBorder(),
            ),
            items: _destinations
                .map(
                  (destination) => DropdownMenuItem<int>(
                    value: destination.id,
                    child: Text(destination.name),
                  ),
                )
                .toList(),
            onChanged: (value) {
              setState(() {
                _selectedDestinationId = value;
                _crowdStatus = null;
              });
            },
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: _pickDate,
                  icon: const Icon(Icons.calendar_today_outlined),
                  label: Text(_formatDate(_selectedDate)),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: () {},
                  icon: const Icon(Icons.schedule),
                  label: Text('${_selectedHour.toString().padLeft(2, '0')}:00'),
                ),
              ),
            ],
          ),
          Slider(
            value: _selectedHour.toDouble(),
            min: 0,
            max: 23,
            divisions: 23,
            label: '${_selectedHour.toString().padLeft(2, '0')}:00',
            onChanged: (value) {
              setState(() {
                _selectedHour = value.round();
                _crowdStatus = null;
              });
            },
          ),
          const SizedBox(height: 8),
          FilledButton.icon(
            onPressed: _isChecking ? null : _checkCrowdStatus,
            icon: _isChecking
                ? const SizedBox(
                    width: 18,
                    height: 18,
                    child: CircularProgressIndicator(strokeWidth: 2),
                  )
                : const Icon(Icons.insights),
            label: const Text('Cek Prediksi'),
          ),
          if (_errorMessage != null && _destinations.isNotEmpty) ...[
            const SizedBox(height: 12),
            Text(
              _errorMessage!,
              style: TextStyle(color: Theme.of(context).colorScheme.error),
            ),
          ],
          if (_crowdStatus != null) ...[
            const SizedBox(height: 16),
            _CrowdPredictionCard(status: _crowdStatus!),
          ],
          if (selectedDestination != null) ...[
            const SizedBox(height: 16),
            DestinationCard(
              destination: selectedDestination,
              isFavorite: widget.isDestinationFavorite(selectedDestination.id),
              onTap: () => widget.onOpenDestination(selectedDestination),
              onFavoritePressed: () => widget.onToggleFavorite(selectedDestination),
              onAddToPlanPressed: () => widget.onAddToPlan(selectedDestination),
            ),
          ],
        ],
      ),
    );
  }
}

class _CrowdPredictionCard extends StatelessWidget {
  final CrowdStatusModel status;

  const _CrowdPredictionCard({required this.status});

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 1,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                const Expanded(
                  child: Text(
                    'Hasil Prediksi',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700),
                  ),
                ),
                CrowdStatusBadge(
                  crowdLevel: status.crowdLevel,
                  crowdLabel: status.crowdLabel,
                  crowdScore: status.crowdScore,
                ),
              ],
            ),
            const SizedBox(height: 12),
            _PredictionRow(
              icon: Icons.people_outline,
              label: 'Estimasi pengunjung',
              value: '${status.visitorCount ?? 0} dari ${status.maxCapacity ?? '-'}',
            ),
            _PredictionRow(
              icon: Icons.calendar_month_outlined,
              label: 'Waktu prediksi',
              value: '${status.predictionDate ?? '-'} pukul ${status.predictionHour ?? '-'}:00',
            ),
            _PredictionRow(
              icon: Icons.settings_suggest_outlined,
              label: 'Metode',
              value: status.method ?? '-',
            ),
          ],
        ),
      ),
    );
  }
}

class _PredictionRow extends StatelessWidget {
  final IconData icon;
  final String label;
  final String value;

  const _PredictionRow({
    required this.icon,
    required this.label,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(top: 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 18, color: Colors.grey.shade700),
          const SizedBox(width: 8),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label, style: TextStyle(fontSize: 12, color: Colors.grey.shade600)),
                Text(value, style: const TextStyle(fontWeight: FontWeight.w600)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

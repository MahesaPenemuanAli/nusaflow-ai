import 'package:flutter/material.dart';
import 'package:frontend_flutter/models/destination_model.dart';
import 'package:frontend_flutter/models/crowd_status_model.dart';
import 'package:frontend_flutter/services/api_service.dart';
import 'package:frontend_flutter/utils/formatters.dart';
import 'package:frontend_flutter/widgets/crowd_status_badge.dart';
import 'package:frontend_flutter/widgets/error_view.dart';
import 'package:frontend_flutter/widgets/loading_view.dart';
import 'package:frontend_flutter/screens/recommendations_screen.dart';

class DestinationDetailScreen extends StatefulWidget {
  final int destinationId;
  final bool isFavorite;
  final ValueChanged<DestinationModel>? onToggleFavorite;
  final ValueChanged<DestinationModel>? onAddToPlan;
  final bool Function(int destinationId)? isDestinationFavorite;

  const DestinationDetailScreen({
    super.key,
    required this.destinationId,
    this.isFavorite = false,
    this.onToggleFavorite,
    this.onAddToPlan,
    this.isDestinationFavorite,
  });

  @override
  State<DestinationDetailScreen> createState() => _DestinationDetailScreenState();
}

class _DestinationDetailScreenState extends State<DestinationDetailScreen> {
  final ApiService _apiService = ApiService();
  
  DestinationModel? _destination;
  CrowdStatusModel? _crowdStatus;
  
  bool _isLoading = true;
  bool _isFavorite = false;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _isFavorite = widget.isFavorite;
    _fetchData();
  }

  Future<void> _fetchData() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final futures = await Future.wait([
        _apiService.getDestinationDetail(widget.destinationId),
        _apiService.getCrowdStatus(widget.destinationId),
      ]);

      final destination = futures[0] as DestinationModel;
      final crowdStatus = futures[1] as CrowdStatusModel;

      setState(() {
        _destination = destination;
        _crowdStatus = crowdStatus;
        _isFavorite = widget.isDestinationFavorite?.call(destination.id) ?? _isFavorite;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _errorMessage = e.toString();
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Destinasi'),
        actions: [
          if (_destination != null && widget.onToggleFavorite != null)
            IconButton(
              tooltip: _isFavorite ? 'Hapus dari favorit' : 'Tambah ke favorit',
              onPressed: _toggleFavorite,
              icon: Icon(_isFavorite ? Icons.favorite : Icons.favorite_border),
            ),
        ],
      ),
      body: _buildBody(),
      bottomNavigationBar: _destination != null ? _buildBottomBar() : null,
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const LoadingView();
    }

    if (_errorMessage != null) {
      return ErrorView(
        message: _errorMessage!,
        onRetry: _fetchData,
      );
    }

    if (_destination == null) {
      return const Center(child: Text('Data tidak ditemukan'));
    }

    final dest = _destination!;

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Text(
                  dest.name,
                  style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                ),
              ),
              if (_crowdStatus != null)
                CrowdStatusBadge(
                  crowdLevel: _crowdStatus!.crowdLevel,
                  crowdLabel: _crowdStatus!.crowdLabel,
                  crowdScore: _crowdStatus!.crowdScore,
                ),
            ],
          ),
          const SizedBox(height: 8),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: Colors.blue.shade100,
              borderRadius: BorderRadius.circular(16),
            ),
            child: Text(
              dest.categoryName,
              style: TextStyle(color: Colors.blue.shade800, fontWeight: FontWeight.w600),
            ),
          ),
          const SizedBox(height: 16),
          const Divider(),
          const SizedBox(height: 8),
          
          _buildInfoRow(Icons.location_on, 'Alamat', dest.address ?? '-'),
          const SizedBox(height: 12),
          _buildInfoRow(Icons.access_time, 'Jam Operasional', '${dest.openingHour ?? '-'} s/d ${dest.closingHour ?? '-'}'),
          const SizedBox(height: 12),
          _buildInfoRow(Icons.people, 'Kapasitas Maksimal', '${dest.maxCapacity ?? '-'} orang'),
          const SizedBox(height: 12),
          _buildInfoRow(Icons.confirmation_number, 'Harga Tiket', Formatters.formatRupiah(dest.ticketPrice)),
          
          const SizedBox(height: 16),
          const Divider(),
          const SizedBox(height: 8),
          
          const Text('Deskripsi', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
          const SizedBox(height: 8),
          Text(
            dest.description ?? 'Belum ada deskripsi.',
            style: const TextStyle(fontSize: 14, height: 1.5),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 20, color: Colors.grey.shade700),
        const SizedBox(width: 8),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: TextStyle(fontSize: 12, color: Colors.grey.shade600)),
              const SizedBox(height: 2),
              Text(value, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500)),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildBottomBar() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 10,
            offset: const Offset(0, -5),
          ),
        ],
      ),
      child: SafeArea(
        child: ElevatedButton(
          onPressed: () {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (context) => RecommendationsScreen(
                  destinationId: widget.destinationId,
                  isDestinationFavorite: widget.isDestinationFavorite,
                  onToggleFavorite: widget.onToggleFavorite,
                  onAddToPlan: widget.onAddToPlan,
                ),
              ),
            );
          },
          style: ElevatedButton.styleFrom(
            padding: const EdgeInsets.symmetric(vertical: 16),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
          ),
          child: const Text('Lihat Rekomendasi Alternatif', style: TextStyle(fontSize: 16)),
        ),
      ),
    );
  }

  void _toggleFavorite() {
    final destination = _destination;
    if (destination == null) return;

    widget.onToggleFavorite?.call(destination);
    setState(() => _isFavorite = !_isFavorite);
  }
}

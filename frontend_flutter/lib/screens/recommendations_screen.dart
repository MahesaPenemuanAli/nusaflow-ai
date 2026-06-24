import 'package:flutter/material.dart';
import 'package:frontend_flutter/models/recommendation_model.dart';
import 'package:frontend_flutter/screens/destination_detail_screen.dart';
import 'package:frontend_flutter/services/api_service.dart';
import 'package:frontend_flutter/widgets/crowd_status_badge.dart';
import 'package:frontend_flutter/widgets/empty_view.dart';
import 'package:frontend_flutter/widgets/error_view.dart';
import 'package:frontend_flutter/widgets/loading_view.dart';

class RecommendationsScreen extends StatefulWidget {
  final int destinationId;

  const RecommendationsScreen({super.key, required this.destinationId});

  @override
  State<RecommendationsScreen> createState() => _RecommendationsScreenState();
}

class _RecommendationsScreenState extends State<RecommendationsScreen> {
  final ApiService _apiService = ApiService();
  
  List<RecommendationModel> _recommendations = [];
  bool _isLoading = true;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _fetchRecommendations();
  }

  Future<void> _fetchRecommendations() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final recommendations = await _apiService.getRecommendations(widget.destinationId);
      setState(() {
        _recommendations = recommendations;
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
        title: const Text('Rekomendasi Alternatif'),
      ),
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const LoadingView();
    }

    if (_errorMessage != null) {
      return ErrorView(
        message: _errorMessage!,
        onRetry: _fetchRecommendations,
      );
    }

    if (_recommendations.isEmpty) {
      return const EmptyView(message: 'Tidak ada rekomendasi destinasi saat ini.');
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _recommendations.length,
      itemBuilder: (context, index) {
        final item = _recommendations[index];
        final dest = item.destination;
        final crowd = item.crowdStatus;

        return Card(
          margin: const EdgeInsets.only(bottom: 12),
          elevation: 1,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
          child: ListTile(
            contentPadding: const EdgeInsets.all(16),
            title: Text(
              dest.name,
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
            ),
            subtitle: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 4),
                Text(dest.categoryName, style: TextStyle(color: Colors.grey.shade600)),
                const SizedBox(height: 8),
                Row(
                  children: [
                    CrowdStatusBadge(
                      crowdLevel: crowd.crowdLevel,
                      crowdLabel: crowd.crowdLabel,
                    ),
                    const Spacer(),
                    Text(
                      'Score: ${item.relevanceScore.toInt()}',
                      style: TextStyle(fontSize: 12, color: Colors.blue.shade700, fontWeight: FontWeight.w600),
                    ),
                  ],
                ),
              ],
            ),
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => DestinationDetailScreen(destinationId: dest.id),
                ),
              );
            },
          ),
        );
      },
    );
  }
}

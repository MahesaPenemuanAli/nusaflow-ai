import 'package:flutter/material.dart';
import 'package:frontend_flutter/models/destination_model.dart';
import 'package:frontend_flutter/services/api_service.dart';
import 'package:frontend_flutter/widgets/destination_card.dart';
import 'package:frontend_flutter/widgets/empty_view.dart';
import 'package:frontend_flutter/widgets/error_view.dart';
import 'package:frontend_flutter/widgets/loading_view.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final ApiService _apiService = ApiService();
  
  List<DestinationModel> _destinations = [];
  bool _isLoading = true;
  String? _errorMessage;

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
      final destinations = await _apiService.getDestinations();
      setState(() {
        _destinations = destinations;
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
        title: const Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('NusaFlow AI', style: TextStyle(fontWeight: FontWeight.bold)),
            Text(
              'Cek keramaian destinasi wisata',
              style: TextStyle(fontSize: 12, fontWeight: FontWeight.normal),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _fetchDestinations,
          ),
        ],
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
        onRetry: _fetchDestinations,
      );
    }

    if (_destinations.isEmpty) {
      return const EmptyView(message: 'Tidak ada destinasi ditemukan');
    }

    return RefreshIndicator(
      onRefresh: _fetchDestinations,
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: _destinations.length,
        itemBuilder: (context, index) {
          return DestinationCard(destination: _destinations[index]);
        },
      ),
    );
  }
}

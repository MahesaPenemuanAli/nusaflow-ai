import 'package:flutter/material.dart';
import 'package:frontend_flutter/models/destination_model.dart';
import 'package:frontend_flutter/screens/destination_detail_screen.dart';
import 'package:frontend_flutter/services/api_service.dart';
import 'package:frontend_flutter/widgets/destination_card.dart';
import 'package:frontend_flutter/widgets/empty_view.dart';
import 'package:frontend_flutter/widgets/error_view.dart';
import 'package:frontend_flutter/widgets/loading_view.dart';

class HomeScreen extends StatefulWidget {
  final Set<int> favoriteDestinationIds;
  final ValueChanged<List<DestinationModel>>? onDestinationsLoaded;
  final ValueChanged<DestinationModel>? onOpenDestination;
  final ValueChanged<DestinationModel>? onToggleFavorite;
  final ValueChanged<DestinationModel>? onAddToPlan;

  const HomeScreen({
    super.key,
    this.favoriteDestinationIds = const {},
    this.onDestinationsLoaded,
    this.onOpenDestination,
    this.onToggleFavorite,
    this.onAddToPlan,
  });

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final ApiService _apiService = ApiService();
  final TextEditingController _searchController = TextEditingController();

  List<DestinationModel> _destinations = [];
  Map<String, String> _categoriesBySlug = {};
  bool _isLoading = true;
  String? _errorMessage;
  String? _selectedCategorySlug;
  String? _selectedCrowdLevel;

  final Map<String, String> _crowdLevels = const {
    'low': 'Sepi',
    'moderate': 'Normal',
    'high': 'Ramai',
    'packed': 'Penuh',
  };

  @override
  void initState() {
    super.initState();
    _fetchDestinations();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _fetchDestinations() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final destinations = await _apiService.getDestinations(
        search: _searchController.text.trim(),
        categorySlug: _selectedCategorySlug,
        crowdLevel: _selectedCrowdLevel,
        limit: 50,
      );

      final categories = Map<String, String>.from(_categoriesBySlug);
      for (final destination in destinations) {
        categories[destination.categorySlug] = destination.categoryName;
      }

      if (!mounted) return;
      setState(() {
        _destinations = destinations;
        _categoriesBySlug = categories;
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

  void _resetFilters() {
    _searchController.clear();
    setState(() {
      _selectedCategorySlug = null;
      _selectedCrowdLevel = null;
    });
    _fetchDestinations();
  }

  void _openDestination(DestinationModel destination) {
    if (widget.onOpenDestination != null) {
      widget.onOpenDestination!(destination);
    } else {
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => DestinationDetailScreen(destinationId: destination.id),
        ),
      );
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
      body: Column(
        children: [
          _buildFilters(),
          Expanded(child: _buildBody()),
        ],
      ),
    );
  }

  Widget _buildBody() {
    if (_isLoading && _destinations.isEmpty) {
      return const LoadingView();
    }

    if (_errorMessage != null && _destinations.isEmpty) {
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
          final destination = _destinations[index];
          return DestinationCard(
            destination: destination,
            isFavorite: widget.favoriteDestinationIds.contains(destination.id),
            onTap: () => _openDestination(destination),
            onFavoritePressed: widget.onToggleFavorite == null
                ? null
                : () => widget.onToggleFavorite!(destination),
            onAddToPlanPressed: widget.onAddToPlan == null
                ? null
                : () => widget.onAddToPlan!(destination),
          );
        },
      ),
    );
  }

  Widget _buildFilters() {
    final theme = Theme.of(context);
    final hasActiveFilter = _searchController.text.trim().isNotEmpty ||
        _selectedCategorySlug != null ||
        _selectedCrowdLevel != null;

    return Material(
      color: theme.colorScheme.surface,
      elevation: 1,
      child: SafeArea(
        bottom: false,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(16, 12, 16, 10),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              TextField(
                controller: _searchController,
                textInputAction: TextInputAction.search,
                decoration: InputDecoration(
                  hintText: 'Cari destinasi atau lokasi',
                  prefixIcon: const Icon(Icons.search),
                  suffixIcon: hasActiveFilter
                      ? IconButton(
                          tooltip: 'Reset filter',
                          icon: const Icon(Icons.close),
                          onPressed: _resetFilters,
                        )
                      : null,
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                onSubmitted: (_) => _fetchDestinations(),
              ),
              const SizedBox(height: 10),
              Row(
                children: [
                  FilledButton.icon(
                    onPressed: _fetchDestinations,
                    icon: const Icon(Icons.tune),
                    label: const Text('Terapkan'),
                  ),
                  if (_isLoading && _destinations.isNotEmpty) ...[
                    const SizedBox(width: 12),
                    const SizedBox(
                      width: 18,
                      height: 18,
                      child: CircularProgressIndicator(strokeWidth: 2),
                    ),
                  ],
                ],
              ),
              const SizedBox(height: 10),
              _buildCategoryFilters(),
              const SizedBox(height: 8),
              _buildCrowdFilters(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildCategoryFilters() {
    final entries = _categoriesBySlug.entries.toList()
      ..sort((a, b) => a.value.compareTo(b.value));

    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      child: Row(
        children: [
          FilterChip(
            label: const Text('Semua kategori'),
            selected: _selectedCategorySlug == null,
            onSelected: (_) {
              setState(() => _selectedCategorySlug = null);
              _fetchDestinations();
            },
          ),
          const SizedBox(width: 8),
          for (final entry in entries) ...[
            FilterChip(
              label: Text(entry.value),
              selected: _selectedCategorySlug == entry.key,
              onSelected: (_) {
                setState(() => _selectedCategorySlug = entry.key);
                _fetchDestinations();
              },
            ),
            const SizedBox(width: 8),
          ],
        ],
      ),
    );
  }

  Widget _buildCrowdFilters() {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      child: Row(
        children: [
          FilterChip(
            label: const Text('Semua keramaian'),
            selected: _selectedCrowdLevel == null,
            onSelected: (_) {
              setState(() => _selectedCrowdLevel = null);
              _fetchDestinations();
            },
          ),
          const SizedBox(width: 8),
          for (final entry in _crowdLevels.entries) ...[
            FilterChip(
              label: Text(entry.value),
              selected: _selectedCrowdLevel == entry.key,
              onSelected: (_) {
                setState(() => _selectedCrowdLevel = entry.key);
                _fetchDestinations();
              },
            ),
            const SizedBox(width: 8),
          ],
        ],
      ),
    );
  }
}

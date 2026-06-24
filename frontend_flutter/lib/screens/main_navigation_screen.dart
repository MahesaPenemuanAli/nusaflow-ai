import 'package:flutter/material.dart';
import 'package:frontend_flutter/models/destination_model.dart';
import 'package:frontend_flutter/screens/crowd_planner_screen.dart';
import 'package:frontend_flutter/screens/destination_detail_screen.dart';
import 'package:frontend_flutter/screens/favorites_screen.dart';
import 'package:frontend_flutter/screens/home_screen.dart';
import 'package:frontend_flutter/screens/profile_screen.dart';
import 'package:frontend_flutter/screens/trip_plan_screen.dart';

class MainNavigationScreen extends StatefulWidget {
  const MainNavigationScreen({super.key});

  @override
  State<MainNavigationScreen> createState() => _MainNavigationScreenState();
}

class _MainNavigationScreenState extends State<MainNavigationScreen> {
  int _selectedIndex = 0;

  final Map<int, DestinationModel> _knownDestinations = {};
  final Set<int> _favoriteDestinationIds = {};
  final List<int> _tripPlanDestinationIds = [];

  List<DestinationModel> get _favoriteDestinations => _favoriteDestinationIds
      .map((id) => _knownDestinations[id])
      .whereType<DestinationModel>()
      .toList()
    ..sort((a, b) => a.name.compareTo(b.name));

  List<DestinationModel> get _tripPlanDestinations => _tripPlanDestinationIds
      .map((id) => _knownDestinations[id])
      .whereType<DestinationModel>()
      .toList();

  bool _isFavorite(int destinationId) {
    return _favoriteDestinationIds.contains(destinationId);
  }

  void _rememberDestinations(List<DestinationModel> destinations) {
    if (destinations.isEmpty) return;

    setState(() {
      for (final destination in destinations) {
        _knownDestinations[destination.id] = destination;
      }
    });
  }

  void _rememberDestination(DestinationModel destination) {
    _knownDestinations[destination.id] = destination;
  }

  void _toggleFavorite(DestinationModel destination) {
    final willAdd = !_favoriteDestinationIds.contains(destination.id);

    setState(() {
      _rememberDestination(destination);
      if (willAdd) {
        _favoriteDestinationIds.add(destination.id);
      } else {
        _favoriteDestinationIds.remove(destination.id);
      }
    });

    _showSnackBar(
      willAdd
          ? '${destination.name} masuk favorit'
          : '${destination.name} dihapus dari favorit',
    );
  }

  void _addToTripPlan(DestinationModel destination) {
    final alreadyAdded = _tripPlanDestinationIds.contains(destination.id);

    setState(() {
      _rememberDestination(destination);
      if (!alreadyAdded) {
        _tripPlanDestinationIds.add(destination.id);
      }
    });

    _showSnackBar(
      alreadyAdded
          ? '${destination.name} sudah ada di rencana'
          : '${destination.name} ditambahkan ke rencana',
    );
  }

  void _removeFromTripPlan(DestinationModel destination) {
    setState(() {
      _tripPlanDestinationIds.remove(destination.id);
    });

    _showSnackBar('${destination.name} dihapus dari rencana');
  }

  void _clearTripPlan() {
    setState(_tripPlanDestinationIds.clear);
    _showSnackBar('Rencana perjalanan dikosongkan');
  }

  void _reorderTripPlan(int oldIndex, int newIndex) {
    setState(() {
      if (newIndex > oldIndex) {
        newIndex -= 1;
      }
      final item = _tripPlanDestinationIds.removeAt(oldIndex);
      _tripPlanDestinationIds.insert(newIndex, item);
    });
  }

  void _openDestination(DestinationModel destination) {
    _rememberDestination(destination);

    Navigator.of(context).push(
      MaterialPageRoute(
        builder: (context) => DestinationDetailScreen(
          destinationId: destination.id,
          isFavorite: _isFavorite(destination.id),
          onToggleFavorite: _toggleFavorite,
          onAddToPlan: _addToTripPlan,
          isDestinationFavorite: _isFavorite,
        ),
      ),
    );
  }

  void _showSnackBar(String message) {
    ScaffoldMessenger.of(context)
      ..hideCurrentSnackBar()
      ..showSnackBar(SnackBar(content: Text(message)));
  }

  @override
  Widget build(BuildContext context) {
    final destinations = <NavigationDestination>[
      const NavigationDestination(
        icon: Icon(Icons.explore_outlined),
        selectedIcon: Icon(Icons.explore),
        label: 'Jelajah',
      ),
      const NavigationDestination(
        icon: Icon(Icons.insights_outlined),
        selectedIcon: Icon(Icons.insights),
        label: 'Prediksi',
      ),
      const NavigationDestination(
        icon: Icon(Icons.route_outlined),
        selectedIcon: Icon(Icons.route),
        label: 'Rencana',
      ),
      const NavigationDestination(
        icon: Icon(Icons.favorite_border),
        selectedIcon: Icon(Icons.favorite),
        label: 'Favorit',
      ),
      const NavigationDestination(
        icon: Icon(Icons.info_outline),
        selectedIcon: Icon(Icons.info),
        label: 'Info',
      ),
    ];

    final pages = [
      HomeScreen(
        favoriteDestinationIds: _favoriteDestinationIds,
        onDestinationsLoaded: _rememberDestinations,
        onOpenDestination: _openDestination,
        onToggleFavorite: _toggleFavorite,
        onAddToPlan: _addToTripPlan,
      ),
      CrowdPlannerScreen(
        isDestinationFavorite: _isFavorite,
        onDestinationsLoaded: _rememberDestinations,
        onOpenDestination: _openDestination,
        onToggleFavorite: _toggleFavorite,
        onAddToPlan: _addToTripPlan,
      ),
      TripPlanScreen(
        destinations: _tripPlanDestinations,
        onOpenDestination: _openDestination,
        onRemoveDestination: _removeFromTripPlan,
        onClearPlan: _tripPlanDestinationIds.isEmpty ? null : _clearTripPlan,
        onReorder: _reorderTripPlan,
      ),
      FavoritesScreen(
        destinations: _favoriteDestinations,
        favoriteDestinationIds: _favoriteDestinationIds,
        onOpenDestination: _openDestination,
        onToggleFavorite: _toggleFavorite,
        onAddToPlan: _addToTripPlan,
      ),
      ProfileScreen(
        favoriteCount: _favoriteDestinationIds.length,
        tripPlanCount: _tripPlanDestinationIds.length,
      ),
    ];

    final isWide = MediaQuery.sizeOf(context).width >= 900;

    return Scaffold(
      body: Row(
        children: [
          if (isWide)
            NavigationRail(
              selectedIndex: _selectedIndex,
              onDestinationSelected: (index) {
                setState(() => _selectedIndex = index);
              },
              labelType: NavigationRailLabelType.all,
              destinations: destinations
                  .map(
                    (destination) => NavigationRailDestination(
                      icon: destination.icon,
                      selectedIcon: destination.selectedIcon,
                      label: Text(destination.label),
                    ),
                  )
                  .toList(),
            ),
          Expanded(
            child: IndexedStack(
              index: _selectedIndex,
              children: pages,
            ),
          ),
        ],
      ),
      bottomNavigationBar: isWide
          ? null
          : NavigationBar(
              selectedIndex: _selectedIndex,
              onDestinationSelected: (index) {
                setState(() => _selectedIndex = index);
              },
              destinations: destinations,
            ),
    );
  }
}

import 'package:flutter/material.dart';
import 'package:frontend_flutter/config/api_config.dart';

class ProfileScreen extends StatelessWidget {
  final int favoriteCount;
  final int tripPlanCount;

  const ProfileScreen({
    super.key,
    required this.favoriteCount,
    required this.tripPlanCount,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Info'),
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _InfoHeader(
            favoriteCount: favoriteCount,
            tripPlanCount: tripPlanCount,
          ),
          const SizedBox(height: 16),
          _InfoTile(
            icon: Icons.travel_explore,
            title: 'Aplikasi wisatawan',
            subtitle: 'Jelajahi destinasi, cek prediksi keramaian, simpan favorit, dan susun rencana kunjungan.',
          ),
          _InfoTile(
            icon: Icons.psychology_outlined,
            title: 'Prediksi AI',
            subtitle: 'Status keramaian diambil dari Laravel API yang terhubung ke FastAPI ML service jika service aktif.',
          ),
          _InfoTile(
            icon: Icons.cloud_outlined,
            title: 'API aktif',
            subtitle: ApiConfig.baseUrl,
          ),
          const SizedBox(height: 8),
          const Text(
            'Catatan MVP: favorit dan rencana perjalanan tersimpan sebagai state lokal selama aplikasi berjalan.',
            style: TextStyle(color: Colors.grey),
          ),
        ],
      ),
    );
  }
}

class _InfoHeader extends StatelessWidget {
  final int favoriteCount;
  final int tripPlanCount;

  const _InfoHeader({
    required this.favoriteCount,
    required this.tripPlanCount,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Theme.of(context).colorScheme.primaryContainer,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Row(
        children: [
          const Icon(Icons.account_circle, size: 44),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Wisatawan NusaFlow',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700),
                ),
                const SizedBox(height: 4),
                Text('$favoriteCount favorit - $tripPlanCount rencana'),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _InfoTile extends StatelessWidget {
  final IconData icon;
  final String title;
  final String subtitle;

  const _InfoTile({
    required this.icon,
    required this.title,
    required this.subtitle,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 1,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
      child: ListTile(
        leading: Icon(icon),
        title: Text(title),
        subtitle: Text(subtitle),
      ),
    );
  }
}

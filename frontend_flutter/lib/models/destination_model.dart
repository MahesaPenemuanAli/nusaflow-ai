class DestinationModel {
  final int id;
  final String name;
  final String slug;
  final String categoryName;
  final String categorySlug;
  final String? description;
  final String? address;
  final double? latitude;
  final double? longitude;
  final int? maxCapacity;
  final String? openingHour;
  final String? closingHour;
  final double? ticketPrice;
  final String? image;
  final bool isActive;

  DestinationModel({
    required this.id,
    required this.name,
    required this.slug,
    required this.categoryName,
    required this.categorySlug,
    this.description,
    this.address,
    this.latitude,
    this.longitude,
    this.maxCapacity,
    this.openingHour,
    this.closingHour,
    this.ticketPrice,
    this.image,
    this.isActive = true,
  });

  factory DestinationModel.fromJson(Map<String, dynamic> json) {
    return DestinationModel(
      id: json['id'] as int,
      name: json['name'] as String,
      slug: json['slug'] as String,
      categoryName: json['category'] != null ? json['category']['name'] as String : 'Unknown',
      categorySlug: json['category'] != null ? json['category']['slug'] as String : 'unknown',
      description: json['description'] as String?,
      address: json['address'] as String?,
      latitude: json['latitude'] != null ? double.tryParse(json['latitude'].toString()) : null,
      longitude: json['longitude'] != null ? double.tryParse(json['longitude'].toString()) : null,
      maxCapacity: json['max_capacity'] as int?,
      openingHour: json['opening_hour'] as String?,
      closingHour: json['closing_hour'] as String?,
      ticketPrice: json['ticket_price'] != null ? double.tryParse(json['ticket_price'].toString()) : null,
      image: json['image'] as String?,
      isActive: json['is_active'] ?? true,
    );
  }
}

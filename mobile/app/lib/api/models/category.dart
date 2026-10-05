/// Felagi category — `/api/v1/categories`.
class Category {
  const Category({
    required this.id,
    required this.nameEn,
    required this.nameAm,
    required this.slug,
    this.active = true,
  });

  final String id;
  final String nameEn;
  final String nameAm;
  final String slug;
  final bool active;

  /// Localized name for the current locale.
  String name(String localeCode) => localeCode == 'am' ? nameAm : nameEn;

  factory Category.fromJson(Map<String, dynamic> json) => Category(
        id: json['id'] as String,
        nameEn: (json['name_en'] as String?) ?? '',
        nameAm: (json['name_am'] as String?) ?? '',
        slug: (json['slug'] as String?) ?? '',
        active: (json['active'] as bool?) ?? true,
      );
}

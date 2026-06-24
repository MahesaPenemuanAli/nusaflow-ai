class Formatters {
  static String formatRupiah(double? value) {
    if (value == null) return 'Rp 0';
    if (value == 0) return 'Gratis';
    return 'Rp ${value.toStringAsFixed(0).replaceAllMapped(RegExp(r"(\d{1,3})(?=(\d{3})+(?!\d))"), (Match m) => "${m[1]}.")}';
  }
}

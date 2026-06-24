import 'package:flutter_test/flutter_test.dart';
import 'package:frontend_flutter/app.dart';

void main() {
  testWidgets('App should render NusaFlow AI title', (WidgetTester tester) async {
    // Build our app and trigger a frame.
    await tester.pumpWidget(const NusaFlowApp());

    // Verify that our app renders the correct title
    expect(find.text('NusaFlow AI'), findsOneWidget);
  });
}

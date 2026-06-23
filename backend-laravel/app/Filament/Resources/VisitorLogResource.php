<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitorLogResource\Pages;
use App\Models\VisitorLog;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VisitorLogResource extends Resource
{
    protected static ?string $model = VisitorLog::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    protected static \UnitEnum|string|null $navigationGroup = 'Tourism Operations';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Visitor Logs';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Visitor Log Entry')
                    ->schema([
                        Forms\Components\Select::make('destination_id')
                            ->relationship('destination', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Destination'),

                        Forms\Components\DatePicker::make('visit_date')
                            ->required()
                            ->native(false)
                            ->default(now()),

                        Forms\Components\Select::make('visit_hour')
                            ->options(
                                collect(range(0, 23))->mapWithKeys(fn ($hour) => [
                                    $hour => str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00',
                                ])->toArray()
                            )
                            ->placeholder('Pilih jam (opsional)')
                            ->label('Visit Hour'),

                        Forms\Components\TextInput::make('visitor_count')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->suffix('orang'),

                        Forms\Components\TextInput::make('weather')
                            ->maxLength(50)
                            ->placeholder('e.g. cerah, berawan, hujan'),

                        Forms\Components\Select::make('source')
                            ->options([
                                'admin_input' => 'Admin Input',
                                'qr_checkin' => 'QR Check-in',
                                'ticket_system' => 'Ticket System',
                                'estimated' => 'Estimated',
                            ])
                            ->default('admin_input')
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('destination.name')
                    ->label('Destination')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('visit_date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('visit_hour')
                    ->formatStateUsing(fn (?int $state): string => $state !== null ? str_pad($state, 2, '0', STR_PAD_LEFT) . ':00' : '-')
                    ->label('Hour')
                    ->sortable(),

                Tables\Columns\TextColumn::make('visitor_count')
                    ->numeric()
                    ->sortable()
                    ->suffix(' orang'),

                Tables\Columns\TextColumn::make('weather')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('source')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin_input' => 'primary',
                        'qr_checkin' => 'success',
                        'ticket_system' => 'info',
                        'estimated' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('destination_id')
                    ->relationship('destination', 'name')
                    ->label('Destination')
                    ->preload(),

                Tables\Filters\SelectFilter::make('source')
                    ->options([
                        'admin_input' => 'Admin Input',
                        'qr_checkin' => 'QR Check-in',
                        'ticket_system' => 'Ticket System',
                        'estimated' => 'Estimated',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('visit_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisitorLogs::route('/'),
            'create' => Pages\CreateVisitorLog::route('/create'),
            'edit' => Pages\EditVisitorLog::route('/{record}/edit'),
        ];
    }
}

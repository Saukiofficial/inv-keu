<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;
    protected static ?string $navigationLabel = 'Manajemen Asset';
    public static function getNavigationIcon(): string|\BackedEnum|null
{
    return 'heroicon-o-server-stack';
}
    protected static ?string $pluralModelLabel = 'Asset';
    protected static ?string $modelLabel = 'Asset';
    protected static ?int $navigationSort = 1;

  public static function form(Schema $schema): Schema
{
    return $schema->components([
        Forms\Components\TextInput::make('nama_asset')
            ->label('Nama Asset')
            ->required()
            ->maxLength(255)
            ->columnSpan(1),

        Forms\Components\Select::make('kategori')
            ->label('Kategori')
            ->options([
                'Router' => 'Router',
                'Switch' => 'Switch',
                'Access Point' => 'Access Point',
                'ODP' => 'ODP',
                'ONU' => 'ONU',
                'Kabel' => 'Kabel',
                'Teknisi' => 'Teknisi',
                'Server' => 'Server',
                'Lainnya' => 'Lainnya',
            ])
            ->required()
            ->searchable(),

        Forms\Components\TextInput::make('merk')
            ->label('Merk / Brand')
            ->maxLength(255),

        Forms\Components\TextInput::make('serial_number')
            ->label('Serial Number')
            ->maxLength(255),

        Forms\Components\TextInput::make('lokasi')
            ->label('Lokasi Pemasangan')
            ->maxLength(255),

        Forms\Components\Select::make('kondisi')
            ->label('Kondisi')
            ->options([
                'Baik' => 'Baik',
                'Rusak' => 'Rusak',
                'Dalam Perbaikan' => 'Dalam Perbaikan',
            ])
            ->required()
            ->default('Baik'),

        Forms\Components\DatePicker::make('tanggal_beli')
            ->label('Tanggal Beli')
            ->displayFormat('d/m/Y'),

        Forms\Components\TextInput::make('harga')
            ->label('Harga Beli (Rp)')
            ->numeric()
            ->prefix('Rp'),

        Forms\Components\Textarea::make('catatan')
            ->label('Catatan Tambahan')
            ->rows(3)
            ->columnSpanFull(),
    ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_asset')
                    ->label('Nama Asset')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('merk')
                    ->label('Merk')
                    ->searchable(),

                Tables\Columns\TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('kondisi')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baik' => 'success',
                        'Rusak' => 'danger',
                        'Dalam Perbaikan' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga (Rp)')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_beli')
                    ->label('Tgl Beli')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori')
                    ->label('Filter Kategori')
                    ->options([
                        'Router' => 'Router',
                        'Switch' => 'Switch',
                        'Access Point' => 'Access Point',
                        'ODP' => 'ODP',
                        'ONU' => 'ONU',
                        'Kabel' => 'Kabel',
                        'Tower' => 'Tower',
                        'Server' => 'Server',
                        'Lainnya' => 'Lainnya',
                    ]),

                Tables\Filters\SelectFilter::make('kondisi')
                    ->label('Filter Kondisi')
                    ->options([
                        'Baik' => 'Baik',
                        'Rusak' => 'Rusak',
                        'Dalam Perbaikan' => 'Dalam Perbaikan',
                    ]),
            ])
      ->recordAction(null)
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
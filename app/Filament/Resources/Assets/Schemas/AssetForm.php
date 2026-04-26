<?php

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_asset')
                    ->required(),
                TextInput::make('kategori')
                    ->required(),
                TextInput::make('merk'),
                TextInput::make('serial_number'),
                TextInput::make('lokasi'),
                Select::make('kondisi')
                    ->options(['Baik' => 'Baik', 'Rusak' => 'Rusak', 'Dalam Perbaikan' => 'Dalam perbaikan'])
                    ->default('Baik')
                    ->required(),
                DatePicker::make('tanggal_beli'),
                TextInput::make('harga')
                    ->numeric(),
                Textarea::make('catatan')
                    ->columnSpanFull(),
            ]);
    }
}

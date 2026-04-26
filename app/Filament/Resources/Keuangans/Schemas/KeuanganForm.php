<?php

namespace App\Filament\Resources\Keuangans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KeuanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('jenis')
                    ->options(['Masuk' => 'Masuk', 'Keluar' => 'Keluar'])
                    ->required(),
                TextInput::make('kategori')
                    ->required(),
                TextInput::make('jumlah')
                    ->required()
                    ->numeric(),
                TextInput::make('keterangan'),
                DatePicker::make('tanggal')
                    ->required(),
                TextInput::make('bukti'),
            ]);
    }
}

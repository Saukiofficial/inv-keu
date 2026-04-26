<?php

namespace App\Filament\Resources\Keuangans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class KeuanganInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('jenis')
                    ->badge(),
                TextEntry::make('kategori'),
                TextEntry::make('jumlah')
                    ->numeric(),
                TextEntry::make('keterangan')
                    ->placeholder('-'),
                TextEntry::make('tanggal')
                    ->date(),
                TextEntry::make('bukti')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

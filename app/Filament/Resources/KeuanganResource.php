<?php

namespace App\Filament\Resources;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use App\Filament\Resources\KeuanganResource\Pages;
use App\Models\Keuangan;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class KeuanganResource extends Resource
{
    protected static ?string $model = Keuangan::class;
    protected static ?string $navigationLabel = 'Laporan Keuangan';
    public static function getNavigationIcon(): string|\BackedEnum|null
{
    return 'heroicon-o-banknotes';
}
    protected static ?string $pluralModelLabel = 'Keuangan';
    protected static ?string $modelLabel = 'Transaksi';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
{
    return $schema->components([
        Forms\Components\Select::make('jenis')
            ->label('Jenis Transaksi')
            ->options([
                'Masuk' => '💰 Uang Masuk',
                'Keluar' => '💸 Uang Keluar',
            ])
            ->required()
            ->live(),

        Forms\Components\Select::make('kategori')
            ->label('Kategori')
            ->options(fn (Get $get) => match($get('jenis')) {
                'Masuk' => [
                    'Investor' => 'Investor',
                    'Pinjaman' => 'Pinjaman',
                    'Pemasukan Bulanan ' => 'Pemasukan Bulanan',
                    'Lainnya' => 'Lainnya',
                ],
                'Keluar' => [
                    'Operasional' => 'Operasional',
                    'Gaji Karyawan' => 'Gaji Karyawan',
                    'Pembelian Asset' => 'Pembelian Asset',
                    'Perawatan' => 'Perawatan',
                    'Listrik & Internet' => 'Listrik & Internet',
                    'Konsumsi ' => 'Konsumsi',
                    'Lainnya' => 'Lainnya',
                ],
                default => [],
            })
            ->required()
            ->searchable(),

        Forms\Components\TextInput::make('jumlah')
            ->label('Jumlah (Rp)')
            ->numeric()
            ->prefix('Rp')
            ->required(),

        Forms\Components\DatePicker::make('tanggal')
            ->label('Tanggal Transaksi')
            ->required()
            ->default(now())
            ->displayFormat('d/m/Y'),

        Forms\Components\TextInput::make('keterangan')
            ->label('Keterangan')
            ->maxLength(255)
            ->columnSpanFull(),

        Forms\Components\FileUpload::make('bukti')
            ->label('Bukti Transaksi')
            ->image()
            ->directory('bukti-transaksi')
            ->columnSpanFull(),
    ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Masuk' => 'success',
                        'Keluar' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->searchable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah (Rp)')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->label('Filter Jenis')
                    ->options([
                        'Masuk' => 'Uang Masuk',
                        'Keluar' => 'Uang Keluar',
                    ]),

                Tables\Filters\SelectFilter::make('kategori')
                    ->label('Filter Kategori')
                    ->options([
                        'Pembayaran Pelanggan' => 'Pembayaran Pelanggan',
                        'Instalasi Baru' => 'Instalasi Baru',
                        'Upgrade Paket' => 'Upgrade Paket',
                        'Operasional' => 'Operasional',
                        'Gaji Karyawan' => 'Gaji Karyawan',
                        'Pembelian Asset' => 'Pembelian Asset',
                        'Perawatan' => 'Perawatan',
                        'Listrik & Internet' => 'Listrik & Internet',
                        'Lainnya' => 'Lainnya',
                    ]),

                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari'], fn($q) => $q->whereDate('tanggal', '>=', $data['dari']))
                            ->when($data['sampai'], fn($q) => $q->whereDate('tanggal', '<=', $data['sampai']));
                    }),
            ])
            ->recordAction(null)
            ->headerActions([
    Action::make('cetak_laporan')
        ->label('🖨️ Cetak Laporan')
        ->color('info')
        ->icon('heroicon-o-printer')
        ->form([
            \Filament\Forms\Components\DatePicker::make('dari')
                ->label('Dari Tanggal')
                ->default(now()->startOfMonth())
                ->displayFormat('d/m/Y')
                ->required(),

            \Filament\Forms\Components\DatePicker::make('sampai')
                ->label('Sampai Tanggal')
                ->default(now())
                ->displayFormat('d/m/Y')
                ->required(),

            \Filament\Forms\Components\Select::make('jenis')
                ->label('Jenis Transaksi')
                ->options([
                    '' => 'Semua',
                    'Masuk' => 'Uang Masuk',
                    'Keluar' => 'Uang Keluar',
                ])
                ->default(''),
        ])
        ->action(function (array $data) {
            $dari = $data['dari'];
            $sampai = $data['sampai'];
            $jenis = $data['jenis'] ?? '';

            $url = route('laporan.keuangan.cetak', [
                'dari' => $dari,
                'sampai' => $sampai,
                'jenis' => $jenis,
            ]);

            return redirect()->away($url);
        })
        ->extraAttributes([
            'target' => '_blank',
        ]),
])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
                Action::make('cetak')
                ->label('Cetak Semua')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn () => route('laporan.keuangan.cetak'))
                ->openUrlInNewTab(),
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
            'index' => Pages\ListKeuangans::route('/'),
            'create' => Pages\CreateKeuangan::route('/create'),
            'edit' => Pages\EditKeuangan::route('/{record}/edit'),
        ];
    }
}
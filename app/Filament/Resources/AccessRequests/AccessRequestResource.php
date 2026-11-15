<?php

namespace App\Filament\Resources\AccessRequests;

use App\Filament\Resources\AccessRequests\Pages\ManageAccessRequests;
use App\Models\AccessRequest;
use App\Models\VideoAccess;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AccessRequestResource extends Resource
{
    protected static ?string $model = AccessRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $modelLabel = 'Permintaan Akses';

    protected static ?string $pluralModelLabel = 'Permintaan Akses';

    protected static ?string $navigationLabel = 'Permintaan Akses';

    public static function canCreate(): bool
    {
        return false; // permintaan hanya dibuat oleh customer
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = AccessRequest::where('status', 'pending')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('video.title')
                    ->label('Video')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('videoAccess.expires_at')
                    ->label('Berlaku sampai')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Diminta pada')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (AccessRequest $record): bool => $record->status === 'pending')
                    ->modalHeading('Setujui permintaan akses')
                    ->modalDescription('Tentukan sampai kapan customer boleh menonton video ini.')
                    ->schema([
                        DateTimePicker::make('expires_at')
                            ->label('Akses berlaku sampai')
                            ->required()
                            ->seconds(false)
                            ->default(now()->addDay())
                            ->minDate(now()),
                    ])
                    ->action(function (AccessRequest $record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            $record->update(['status' => 'approved']);

                            VideoAccess::create([
                                'access_request_id' => $record->id,
                                'user_id' => $record->user_id,
                                'video_id' => $record->video_id,
                                'start_at' => now(),
                                'expires_at' => $data['expires_at'],
                            ]);
                        });

                        Notification::make()
                            ->title('Permintaan disetujui')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (AccessRequest $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak permintaan akses?')
                    ->action(function (AccessRequest $record): void {
                        $record->update(['status' => 'rejected']);

                        Notification::make()
                            ->title('Permintaan ditolak')
                            ->success()
                            ->send();
                    }),

                Action::make('edit')
                    ->label('Edit')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->color('gray')
                    ->modalHeading('Edit permintaan akses')
                    ->modalDescription('Ubah status atau waktu akses. Memilih Menunggu atau Ditolak akan mencabut akses customer.')
                    ->fillForm(fn (AccessRequest $record): array => [
                        'status' => $record->status,
                        'expires_at' => $record->videoAccess?->expires_at,
                    ])
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Menunggu',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->required()
                            ->live(),

                        DateTimePicker::make('expires_at')
                            ->label('Akses berlaku sampai')
                            ->seconds(false)
                            ->visible(fn ($get): bool => $get('status') === 'approved')
                            ->required(fn ($get): bool => $get('status') === 'approved'),
                    ])
                    ->action(function (AccessRequest $record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            $record->update(['status' => $data['status']]);

                            if ($data['status'] === 'approved') {
                                VideoAccess::updateOrCreate(
                                    ['access_request_id' => $record->id],
                                    [
                                        'user_id' => $record->user_id,
                                        'video_id' => $record->video_id,
                                        'start_at' => $record->videoAccess?->start_at ?? now(),
                                        'expires_at' => $data['expires_at'],
                                    ]
                                );
                            } else {
                                // Status bukan Disetujui: cabut akses customer
                                $record->videoAccess?->delete();
                            }
                        });

                        Notification::make()
                            ->title('Permintaan diperbarui')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAccessRequests::route('/'),
        ];
    }
}
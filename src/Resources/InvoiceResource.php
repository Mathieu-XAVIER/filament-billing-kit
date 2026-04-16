<?php

namespace Mxavier\FilamentBillingKit\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Mxavier\FilamentBillingKit\Models\BillingInvoice;
use Mxavier\FilamentBillingKit\Resources\InvoiceResource\Pages\ListInvoices;
use UnitEnum;

class InvoiceResource extends Resource
{
    protected static ?string $model = BillingInvoice::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Facture';

    protected static ?string $pluralModelLabel = 'Factures';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('N° Facture')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('billable.name')
                    ->label('Client')
                    ->searchable(),

                TextColumn::make('amount_paid')
                    ->label('Montant')
                    ->formatStateUsing(fn (int $state, BillingInvoice $record): string => number_format($state / 100, 2).' '.$record->currency)
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'open' => 'warning',
                        'void', 'uncollectible' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => 'Payée',
                        'open' => 'En attente',
                        'draft' => 'Brouillon',
                        'void' => 'Annulée',
                        'uncollectible' => 'Irrécouvrable',
                        default => $state,
                    }),

                TextColumn::make('billing_period_start')
                    ->label('Période')
                    ->formatStateUsing(fn ($state, BillingInvoice $record): string => $state
                        ? $state->format('d/m/Y').' → '.($record->billing_period_end?->format('d/m/Y') ?? '?')
                        : '—'
                    ),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'paid' => 'Payée',
                        'open' => 'En attente',
                        'void' => 'Annulée',
                        'uncollectible' => 'Irrécouvrable',
                    ]),
            ])
            ->actions([
                Action::make('download')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (BillingInvoice $record): ?string => $record->invoice_pdf)
                    ->openUrlInNewTab()
                    ->visible(fn (BillingInvoice $record): bool => (bool) $record->invoice_pdf),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
        ];
    }
}

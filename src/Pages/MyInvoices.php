<?php

namespace Mxavier\FilamentBillingKit\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mxavier\FilamentBillingKit\Models\BillingInvoice;
use UnitEnum;

class MyInvoices extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|UnitEnum|null $navigationGroup = 'Mon compte';

    protected static ?string $navigationLabel = 'Mes factures';

    protected static ?int $navigationSort = 11;

    protected string $view = 'filament-billing-kit::pages.my-invoices';

    protected function getBillable(): ?Model
    {
        $mode = config('filament-billing-kit.mode', 'mono-tenant');

        if ($mode === 'multi-tenant') {
            return filament()->getTenant();
        }

        return auth()->user();
    }

    public function table(Table $table): Table
    {
        $billable = $this->getBillable();

        return $table
            ->query(
                BillingInvoice::query()
                    ->when($billable, fn (Builder $query) => $query
                        ->where('billable_type', get_class($billable))
                        ->where('billable_id', $billable->getKey())
                    )
            )
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('N° Facture')
                    ->placeholder('—'),

                TextColumn::make('amount_paid')
                    ->label('Montant')
                    ->formatStateUsing(fn (int $state, BillingInvoice $record): string => number_format($state / 100, 2).' '.$record->currency),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'open' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => 'Payée',
                        'open' => 'En attente',
                        default => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y')
                    ->sortable(),
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
}

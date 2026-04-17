<?php

namespace Mxavier\FilamentBillingKit\Filament\Resources;

use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Mxavier\FilamentBillingKit\Filament\Resources\SubscriptionResource\Pages\ListSubscriptions;
use Mxavier\FilamentBillingKit\Filament\Resources\SubscriptionResource\Pages\ViewSubscription;
use Mxavier\FilamentBillingKit\Models\Subscription;
use UnitEnum;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Abonnement';

    protected static ?string $pluralModelLabel = 'Abonnements';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('billable.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('plan.name')
                    ->label('Plan')
                    ->placeholder('—'),

                TextColumn::make('stripe_status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'trialing' => 'warning',
                        'past_due', 'incomplete', 'incomplete_expired' => 'danger',
                        'paused' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Actif',
                        'trialing' => 'Essai',
                        'past_due' => 'Paiement en retard',
                        'canceled' => 'Annulé',
                        'incomplete' => 'Incomplet',
                        'incomplete_expired' => 'Expiré',
                        'paused' => 'En pause',
                        default => $state,
                    }),

                TextColumn::make('trial_ends_at')
                    ->label('Fin essai')
                    ->dateTime('d/m/Y')
                    ->placeholder('—'),

                TextColumn::make('ends_at')
                    ->label('Fin prévue')
                    ->dateTime('d/m/Y')
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('stripe_status')
                    ->label('Statut')
                    ->options([
                        'active' => 'Actif',
                        'trialing' => 'Essai',
                        'past_due' => 'Paiement en retard',
                        'canceled' => 'Annulé',
                        'incomplete' => 'Incomplet',
                        'paused' => 'En pause',
                    ]),

                SelectFilter::make('plan_id')
                    ->label('Plan')
                    ->relationship('plan', 'name'),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informations générales')->schema([
                TextEntry::make('billable.name')->label('Client'),
                TextEntry::make('plan.name')->label('Plan')->placeholder('—'),
                TextEntry::make('stripe_status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'trialing' => 'warning',
                        'past_due', 'incomplete' => 'danger',
                        default => 'gray',
                    }),
                TextEntry::make('created_at')->label('Début')->dateTime('d/m/Y H:i'),
                TextEntry::make('trial_ends_at')->label('Fin essai')->dateTime('d/m/Y')->placeholder('—'),
                TextEntry::make('ends_at')->label('Fin prévue')->dateTime('d/m/Y')->placeholder('—'),
            ])->columns(3),

            Section::make('Historique des changements de plan')->schema([
                RepeatableEntry::make('planChanges')
                    ->schema([
                        TextEntry::make('fromPlan.name')->label('Depuis')->placeholder('—'),
                        TextEntry::make('toPlan.name')->label('Vers'),
                        TextEntry::make('changed_at')->label('Date')->dateTime('d/m/Y H:i'),
                        TextEntry::make('source')->label('Source')->placeholder('—'),
                    ])
                    ->columns(4)
                    ->placeholder('Aucun changement de plan enregistré.'),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubscriptions::route('/'),
            'view' => ViewSubscription::route('/{record}'),
        ];
    }
}

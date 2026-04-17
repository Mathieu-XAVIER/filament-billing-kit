<?php

namespace Mxavier\FilamentBillingKit\Filament\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Mxavier\FilamentBillingKit\Filament\Resources\PlanResource\Pages\CreatePlan;
use Mxavier\FilamentBillingKit\Filament\Resources\PlanResource\Pages\EditPlan;
use Mxavier\FilamentBillingKit\Filament\Resources\PlanResource\Pages\ListPlans;
use Mxavier\FilamentBillingKit\Models\Plan;
use UnitEnum;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),

            TextInput::make('slug')
                ->required()
                ->maxLength(255)
                ->unique(Plan::class, 'slug', ignoreRecord: true),

            Textarea::make('description')
                ->rows(3)
                ->columnSpanFull(),

            TextInput::make('price_monthly')
                ->numeric()
                ->prefix('¢')
                ->helperText('Prix mensuel en centimes (ex: 2900 = 29,00 €)')
                ->minValue(0),

            TextInput::make('price_yearly')
                ->numeric()
                ->prefix('¢')
                ->helperText('Prix annuel en centimes — laisser vide si non proposé')
                ->minValue(0),

            TextInput::make('currency')
                ->required()
                ->default('EUR')
                ->maxLength(3)
                ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                ->dehydrateStateUsing(fn (string $state): string => strtoupper($state)),

            TextInput::make('trial_days')
                ->numeric()
                ->nullable()
                ->minValue(0)
                ->helperText('Laisser vide pour aucun essai'),

            TextInput::make('provider_price_id_monthly')
                ->label('Price ID mensuel')
                ->maxLength(255)
                ->helperText('Identifiant du prix mensuel chez votre prestataire de paiement (ex : price_xxxxx pour Stripe)'),

            TextInput::make('provider_price_id_yearly')
                ->label('Price ID annuel')
                ->maxLength(255)
                ->helperText('Identifiant du prix annuel — laisser vide si non proposé'),

            TextInput::make('marketing_badge')
                ->maxLength(50)
                ->helperText('Ex: Populaire, Recommandé'),

            TextInput::make('sort_order')
                ->numeric()
                ->default(0)
                ->minValue(0),

            Toggle::make('is_active')
                ->default(true),

            Toggle::make('is_featured')
                ->default(false)
                ->helperText('Met en évidence ce plan sur la page pricing'),

            Toggle::make('is_custom_quote')
                ->default(false)
                ->helperText('Affiche "Nous contacter" à la place du bouton de paiement')
                ->live(),

            TextInput::make('contact_url')
                ->url()
                ->maxLength(500)
                ->helperText('URL de redirection pour les plans sur devis (ex: /contact)')
                ->visible(fn (callable $get) => $get('is_custom_quote'))
                ->columnSpanFull(),

            Repeater::make('features')
                ->relationship()
                ->schema([
                    TextInput::make('key')
                        ->required()
                        ->maxLength(100)
                        ->helperText('ex: max_users, can_export'),

                    Select::make('type')
                        ->required()
                        ->options([
                            'boolean' => 'Booléen (oui/non)',
                            'numeric' => 'Numérique (quota)',
                        ])
                        ->live(),

                    TextInput::make('value')
                        ->required()
                        ->helperText('true/false ou un nombre'),

                    TextInput::make('label')
                        ->maxLength(255)
                        ->helperText('Libellé affiché à l\'utilisateur'),
                ])
                ->columns(2)
                ->columnSpanFull()
                ->defaultItems(0)
                ->addActionLabel('Ajouter une fonctionnalité'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price_monthly')
                    ->label('Mensuel')
                    ->formatStateUsing(fn (?int $state, Plan $record): string => $state
                        ? number_format($state / 100, 2).' '.$record->currency
                        : '—'
                    )
                    ->sortable(),

                TextColumn::make('price_yearly')
                    ->label('Annuel')
                    ->formatStateUsing(fn (?int $state, Plan $record): string => $state
                        ? number_format($state / 100, 2).' '.$record->currency
                        : '—'
                    )
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label('Mis en avant')
                    ->boolean(),

                IconColumn::make('is_custom_quote')
                    ->label('Sur devis')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->sortable(),

                TextColumn::make('features_count')
                    ->counts('features')
                    ->label('Fonctionnalités'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->trueLabel('Actifs')
                    ->falseLabel('Inactifs'),

                TernaryFilter::make('is_featured')
                    ->label('Mis en avant'),
            ])
            ->actions([
                EditAction::make(),
                ReplicateAction::make()
                    ->beforeReplicaSaved(function (Plan $replica): void {
                        $replica->slug = $replica->slug.'-copy-'.Str::random(4);
                        $replica->is_active = false;
                    }),
                DeleteAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlans::route('/'),
            'create' => CreatePlan::route('/create'),
            'edit' => EditPlan::route('/{record}/edit'),
        ];
    }
}

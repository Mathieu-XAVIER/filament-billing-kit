<?php

namespace Mxavier\FilamentBillingKit\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class FailedPaymentsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        if (! config('filament-billing-kit.enable_widgets', true)) {
            return [];
        }

        $count = DB::table('subscriptions')
            ->whereIn('stripe_status', ['past_due', 'incomplete'])
            ->count();

        return [
            Stat::make('Paiements en échec', $count)
                ->description('past_due + incomplete')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($count > 0 ? 'danger' : 'success'),
        ];
    }
}

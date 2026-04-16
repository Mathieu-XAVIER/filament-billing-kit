<?php

namespace Mxavier\FilamentBillingKit\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ActiveSubscriptionsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        if (! config('filament-billing-kit.enable_widgets', true)) {
            return [];
        }

        $count = DB::table('subscriptions')
            ->where('stripe_status', 'active')
            ->count();

        return [
            Stat::make('Abonnements actifs', $count)
                ->description('Abonnements en cours')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}

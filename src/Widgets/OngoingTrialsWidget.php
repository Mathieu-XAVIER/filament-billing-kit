<?php

namespace Mxavier\FilamentBillingKit\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class OngoingTrialsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        if (! config('filament-billing-kit.enable_widgets', true)) {
            return [];
        }

        $count = DB::table('subscriptions')
            ->where('stripe_status', 'trialing')
            ->count();

        $expiringCount = DB::table('subscriptions')
            ->where('stripe_status', 'trialing')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<=', now()->addDays(7))
            ->count();

        return [
            Stat::make('Essais en cours', $count)
                ->description("{$expiringCount} expirent dans 7 jours")
                ->icon('heroicon-o-clock')
                ->color('warning'),
        ];
    }
}

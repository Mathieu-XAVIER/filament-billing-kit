<?php

namespace Mxavier\FilamentBillingKit\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mxavier\FilamentBillingKit\Contracts\PaymentDriverContract;
use Mxavier\FilamentBillingKit\Models\Plan;
use RuntimeException;
use Throwable;

#[Layout('filament-billing-kit::layouts.billing')]
class SubscriptionPage extends Component
{
    public string $step = 'plans';

    public string $billingPeriod = 'monthly';

    public ?string $errorMessage = null;

    public function mount(): void
    {
        if (request()->query('checkout') === 'success') {
            $this->step = 'success';
        } elseif (request()->query('checkout') === 'canceled') {
            $this->step = 'canceled';
        }
    }

    public function getBillable(): ?Model
    {
        $mode = config('filament-billing-kit.mode', 'mono-tenant');

        return $mode === 'multi-tenant'
            ? filament()->getTenant()
            : auth()->user();
    }

    public function getPlans(): Collection
    {
        return Plan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with('features')
            ->get();
    }

    public function getCurrentPlan(): ?Plan
    {
        $billable = $this->getBillable();

        if (! $billable || ! method_exists($billable, 'currentPlan')) {
            return null;
        }

        return $billable->currentPlan();
    }

    public function getSubscription(): mixed
    {
        $billable = $this->getBillable();

        if (! $billable) {
            return null;
        }

        return $billable->subscription(
            config('filament-billing-kit.subscription_name', 'default')
        );
    }

    public function hasYearlyPrices(): bool
    {
        $driver = app(PaymentDriverContract::class);

        return $this->getPlans()->contains(
            fn (Plan $p) => $driver->hasYearlyPricing($p)
        );
    }

    public function toggleBillingPeriod(): void
    {
        $this->billingPeriod = $this->billingPeriod === 'monthly' ? 'yearly' : 'monthly';
        $this->errorMessage = null;
    }

    public function subscribe(int $planId): mixed
    {
        $this->errorMessage = null;

        $billable = $this->getBillable();

        if (! $billable) {
            $this->errorMessage = 'Impossible d\'identifier votre compte.';

            return null;
        }

        $plan = Plan::find($planId);

        if (! $plan) {
            $this->errorMessage = 'Plan introuvable.';

            return null;
        }

        if ($plan->is_custom_quote) {
            $url = $plan->contact_url ?? config('filament-billing-kit.contact_url');
            if ($url) {
                return $this->redirect($url, navigate: false);
            }
            $this->errorMessage = 'Veuillez nous contacter pour ce plan.';

            return null;
        }

        $currentUrl = route('billing.index');

        try {
            $url = app(PaymentDriverContract::class)->checkout(
                $billable,
                $plan,
                $this->billingPeriod,
                $currentUrl.'?checkout=success',
                $currentUrl.'?checkout=canceled',
            );
        } catch (RuntimeException $e) {
            $this->errorMessage = $e->getMessage();

            return null;
        } catch (Throwable $e) {
            $this->errorMessage = 'Une erreur inattendue est survenue. Veuillez réessayer.';
            report($e);

            return null;
        }

        return $this->redirect($url, navigate: false);
    }

    public function manageBilling(): mixed
    {
        $billable = $this->getBillable();

        if (! $billable) {
            $this->errorMessage = 'Impossible d\'identifier votre compte.';

            return null;
        }

        try {
            $url = app(PaymentDriverContract::class)->manageBilling($billable, route('billing.index'));
        } catch (RuntimeException $e) {
            $this->errorMessage = $e->getMessage();

            return null;
        } catch (Throwable $e) {
            $this->errorMessage = 'Une erreur inattendue est survenue. Veuillez réessayer.';
            report($e);

            return null;
        }

        return $this->redirect($url, navigate: false);
    }

    public function backToPlans(): void
    {
        $this->step = 'plans';
        $this->errorMessage = null;
    }

    public function render(): View
    {
        return view('filament-billing-kit::livewire.subscription-page', [
            'plans' => $this->getPlans(),
            'currentPlan' => $this->getCurrentPlan(),
            'subscription' => $this->getSubscription(),
            'hasYearlyPrices' => $this->hasYearlyPrices(),
        ]);
    }
}

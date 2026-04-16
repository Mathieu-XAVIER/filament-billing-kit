<div>
    @if($errorMessage)
        <div class="fbk-status-alert fbk-status-alert--danger mb-6" role="alert">
            <p class="font-semibold">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.error_title') }}</p>
            <p class="mt-1">{{ $errorMessage }}</p>
        </div>
    @endif

    @if($step === 'success')
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30 mb-4">
                <svg class="h-8 w-8 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ trans('filament-billing-kit::filament-billing-kit.pages.plans.subscription_activated') }}</h2>
            <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-sm">
                {{ trans('filament-billing-kit::filament-billing-kit.pages.plans.subscription_activated_message') }}
            </p>
            <button
                wire:click="backToPlans"
                class="mt-8 rounded-lg bg-gray-900 dark:bg-white px-5 py-2.5 text-sm font-semibold text-white dark:text-gray-900 hover:opacity-80 transition-opacity"
            >
                {{ trans('filament-billing-kit::filament-billing-kit.pages.plans.view_subscription') }}
            </button>
        </div>

    @elseif($step === 'canceled')
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 mb-4">
                <svg class="h-8 w-8 text-yellow-600 dark:text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ trans('filament-billing-kit::filament-billing-kit.pages.plans.payment_canceled') }}</h2>
            <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-sm">
                {{ trans('filament-billing-kit::filament-billing-kit.pages.plans.payment_canceled_message') }}
            </p>
            <button
                wire:click="backToPlans"
                class="mt-8 rounded-lg bg-gray-900 dark:bg-white px-5 py-2.5 text-sm font-semibold text-white dark:text-gray-900 hover:opacity-80 transition-opacity"
            >
                {{ trans('filament-billing-kit::filament-billing-kit.pages.plans.back_to_plans') }}
            </button>
        </div>

    @else
        @if($subscription)
            <div class="mb-8 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-1">
                        {{ trans('filament-billing-kit::filament-billing-kit.pages.plans.current_subscription_label') }}
                    </p>
                    <div class="flex items-center gap-3 flex-wrap">
                        @if($currentPlan)
                            <span class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $currentPlan->name }}
                            </span>
                        @endif

                        @php
                            $statusLabel = match($subscription->stripe_status) {
                                'active'   => trans('filament-billing-kit::filament-billing-kit.subscription.statuses.active'),
                                'trialing' => trans('filament-billing-kit::filament-billing-kit.subscription.statuses.trialing'),
                                'past_due' => trans('filament-billing-kit::filament-billing-kit.subscription.statuses.past_due'),
                                'canceled' => trans('filament-billing-kit::filament-billing-kit.subscription.statuses.canceled'),
                                default    => $subscription->stripe_status,
                            };
                            $statusClass = match($subscription->stripe_status) {
                                'active'   => 'fbk-invoice-badge--paid',
                                'trialing' => 'fbk-invoice-badge--open',
                                'past_due' => 'fbk-invoice-badge--danger',
                                default    => 'fbk-invoice-badge--default',
                            };
                        @endphp

                        <span class="fbk-invoice-badge {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>

                        @if($subscription->onGracePeriod())
                            <span class="fbk-status-alert fbk-status-alert--warning py-0.5 px-2.5 text-xs rounded-full">
                                {{ trans('filament-billing-kit::filament-billing-kit.pages.plans.grace_period_ends', ['date' => $subscription->ends_at->format('d/m/Y')]) }}
                            </span>
                        @endif
                    </div>
                </div>

                <button
                    wire:click="manageBilling"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors shrink-0"
                >
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M2.5 4A1.5 1.5 0 001 5.5V6h18v-.5A1.5 1.5 0 0017.5 4h-15zM19 8.5H1v6A1.5 1.5 0 002.5 16h15a1.5 1.5 0 001.5-1.5v-6zM3 13.25a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5h-1.5a.75.75 0 01-.75-.75zm4.75-.75a.75.75 0 000 1.5h3.5a.75.75 0 000-1.5h-3.5z" clip-rule="evenodd"/>
                    </svg>
                    {{ trans('filament-billing-kit::filament-billing-kit.pages.plans.manage_billing') }}
                    <span wire:loading wire:target="manageBilling">
                        <svg class="h-3 w-3 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </span>
                </button>
            </div>
        @endif

        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">
                {{ $currentPlan
                    ? trans('filament-billing-kit::filament-billing-kit.pages.plans.title_change')
                    : trans('filament-billing-kit::filament-billing-kit.pages.plans.title_choose') }}
            </h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">
                {{ trans('filament-billing-kit::filament-billing-kit.pages.plans.subtitle') }}
            </p>
        </div>

        @if($hasYearlyPrices)
            <div class="fbk-billing-toggle-wrapper">
                <span class="fbk-billing-toggle-label {{ $billingPeriod === 'monthly' ? 'font-semibold text-gray-900 dark:text-white' : '' }}">
                    {{ trans('filament-billing-kit::filament-billing-kit.pages.plans.monthly') }}
                </span>

                <button
                    wire:click="toggleBillingPeriod"
                    type="button"
                    role="switch"
                    aria-checked="{{ $billingPeriod === 'yearly' ? 'true' : 'false' }}"
                    class="fbk-switch {{ $billingPeriod === 'yearly' ? 'fbk-switch--on' : '' }}"
                >
                    <span class="fbk-switch__thumb"></span>
                </button>

                <span class="fbk-billing-toggle-label {{ $billingPeriod === 'yearly' ? 'font-semibold text-gray-900 dark:text-white' : '' }}">
                    {{ trans('filament-billing-kit::filament-billing-kit.pages.plans.yearly') }}
                    <span class="fbk-billing-discount">-20%</span>
                </span>
            </div>
        @endif

        @if($plans->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <p class="text-sm">{{ trans('filament-billing-kit::filament-billing-kit.pages.plans.empty') }}</p>
            </div>
        @else
            <div class="fbk-pricing-grid">
                @foreach($plans as $plan)
                    @php
                        $isCurrent   = $currentPlan && $currentPlan->id === $plan->id;
                        $features    = $plan->features ?? collect();
                        $isYearly    = $billingPeriod === 'yearly';
                        $price       = $isYearly && $plan->price_yearly ? $plan->price_yearly : $plan->price_monthly;
                        $pricePeriod = $isYearly
                            ? trans('filament-billing-kit::filament-billing-kit.pages.plans.price_period_yearly')
                            : trans('filament-billing-kit::filament-billing-kit.pages.plans.price_period_monthly');
                    @endphp

                    <div class="fbk-plan-card {{ $plan->is_featured ? 'fbk-plan-card--featured' : '' }} {{ $isCurrent ? 'fbk-plan-card--current' : '' }}">

                        @if($plan->marketing_badge)
                            <div class="fbk-plan-badge">{{ $plan->marketing_badge }}</div>
                        @endif

                        <p class="fbk-plan-name">{{ $plan->name }}</p>

                        @if($plan->description)
                            <p class="fbk-plan-description">{{ $plan->description }}</p>
                        @endif

                        @if($price !== null)
                            <div class="fbk-plan-price">
                                <span class="fbk-plan-price__currency">{{ $plan->currency ?? '€' }}</span>
                                <span class="fbk-plan-price__amount">{{ number_format($price / 100, 0) }}</span>
                                <span class="fbk-plan-price__period">/ {{ $pricePeriod }}</span>
                            </div>
                        @else
                            <div class="fbk-plan-price">
                                <span class="fbk-plan-price__amount" style="font-size:1.5rem">{{ trans('filament-billing-kit::filament-billing-kit.pages.plans.custom_quote') }}</span>
                            </div>
                        @endif

                        @if($features->isNotEmpty())
                            <ul class="fbk-feature-list" role="list">
                                @foreach($features as $feature)
                                    <li class="fbk-feature-item">
                                        @if($feature->isBoolean())
                                            @if($feature->getValue())
                                                <svg class="fbk-feature-icon--check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>{{ $feature->label ?? $feature->key }}</span>
                                            @else
                                                <svg class="fbk-feature-icon--cross" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="text-gray-400 dark:text-gray-600 line-through">{{ $feature->label ?? $feature->key }}</span>
                                            @endif
                                        @else
                                            <svg class="fbk-feature-icon--check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>
                                                {{ $feature->label ?? $feature->key }} :
                                                <span class="fbk-feature-value">{{ $feature->getValue() }}</span>
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="fbk-plan-cta">
                            @if($isCurrent)
                                <button disabled class="fbk-plan-cta__button fbk-plan-cta__button--current">
                                    {{ trans('filament-billing-kit::filament-billing-kit.pages.plans.current_plan') }}
                                </button>
                            @elseif($plan->is_custom_quote)
                                <button
                                    wire:click="subscribe({{ $plan->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="subscribe({{ $plan->id }})"
                                    class="fbk-plan-cta__button {{ $plan->is_featured ? 'fbk-plan-cta__button--featured' : '' }}"
                                >
                                    <span wire:loading.remove wire:target="subscribe({{ $plan->id }})">{{ trans('filament-billing-kit::filament-billing-kit.pages.plans.contact_us') }}</span>
                                    <span wire:loading wire:target="subscribe({{ $plan->id }})" class="inline-flex items-center justify-center gap-2">
                                        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                        {{ trans('filament-billing-kit::filament-billing-kit.pages.plans.redirecting') }}
                                    </span>
                                </button>
                            @else
                                <button
                                    wire:click="subscribe({{ $plan->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="subscribe({{ $plan->id }})"
                                    class="fbk-plan-cta__button {{ $plan->is_featured ? 'fbk-plan-cta__button--featured' : '' }}"
                                >
                                    <span wire:loading.remove wire:target="subscribe({{ $plan->id }})">
                                        {{ $currentPlan
                                            ? trans('filament-billing-kit::filament-billing-kit.pages.plans.change_plan')
                                            : trans('filament-billing-kit::filament-billing-kit.pages.plans.get_started') }}
                                    </span>
                                    <span wire:loading wire:target="subscribe({{ $plan->id }})" class="inline-flex items-center justify-center gap-2">
                                        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                        {{ trans('filament-billing-kit::filament-billing-kit.pages.plans.redirecting') }}
                                    </span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>

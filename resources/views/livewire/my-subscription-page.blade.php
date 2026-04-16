<div>
    @if($errorMessage)
        <div class="fbk-status-alert fbk-status-alert--danger mb-6" role="alert">
            <p class="font-semibold">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.error_title') }}</p>
            <p class="mt-1">{{ $errorMessage }}</p>
        </div>
    @endif

    @if($subscription && $subscription->onGracePeriod())
        <div class="fbk-status-alert fbk-status-alert--warning mb-6" role="alert">
            <p class="font-semibold">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.grace_period_title') }}</p>
            <p class="mt-1">
                {!! trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.grace_period_message', ['date' => $subscription->ends_at?->format('d/m/Y')]) !!}
            </p>
        </div>
    @elseif(!$subscription || $subscription->canceled())
        <div class="fbk-status-alert fbk-status-alert--warning mb-6" role="alert">
            <p class="font-semibold">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.no_subscription_title') }}</p>
            <p class="mt-1">
                {{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.no_subscription_message') }}
                <a href="{{ route('billing.index') }}" class="font-semibold underline">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.no_subscription_cta') }}</a>
            </p>
        </div>
    @endif

    {{-- Abonnement actuel --}}
    <div class="fbk-section mb-6">
        <h2 class="fbk-section-heading">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.heading') }}</h2>

        @if($subscription && $currentPlan)
            <div class="fbk-subscription-grid">
                <div class="fbk-subscription-stat">
                    <p class="fbk-subscription-stat-label">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.plan_label') }}</p>
                    <p class="fbk-subscription-stat-value">
                        {{ $currentPlan->name }}
                        @if($currentPlan->marketing_badge)
                            <span class="fbk-billing-discount ml-1">{{ $currentPlan->marketing_badge }}</span>
                        @endif
                    </p>
                </div>

                <div class="fbk-subscription-stat">
                    <p class="fbk-subscription-stat-label">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.status_label') }}</p>
                    <p class="mt-1">
                        @php
                            $statusLabel = match($subscription->stripe_status) {
                                'active'   => trans('filament-billing-kit::filament-billing-kit.subscription.statuses.active'),
                                'trialing' => trans('filament-billing-kit::filament-billing-kit.subscription.statuses.trialing'),
                                'past_due' => trans('filament-billing-kit::filament-billing-kit.subscription.statuses.past_due'),
                                'canceled' => trans('filament-billing-kit::filament-billing-kit.subscription.statuses.canceled'),
                                default    => $subscription->stripe_status,
                            };
                            $statusColor = match($subscription->stripe_status) {
                                'active'   => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'trialing' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'past_due' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                default    => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                            };
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </p>
                </div>

                <div class="fbk-subscription-stat">
                    <p class="fbk-subscription-stat-label">
                        @if($subscription->onTrial()) {{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.trial_end') }}
                        @elseif($subscription->ends_at) {{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.scheduled_end') }}
                        @else {{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.renewal') }} @endif
                    </p>
                    <p class="fbk-subscription-stat-value">
                        @if($subscription->onTrial())
                            {{ $subscription->trial_ends_at?->format('d/m/Y') ?? '—' }}
                        @elseif($subscription->ends_at)
                            {{ $subscription->ends_at->format('d/m/Y') }}
                        @else
                            —
                        @endif
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <button
                    wire:click="manageBilling"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                >
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M2.5 4A1.5 1.5 0 001 5.5V6h18v-.5A1.5 1.5 0 0017.5 4h-15zM19 8.5H1v6A1.5 1.5 0 002.5 16h15a1.5 1.5 0 001.5-1.5v-6zM3 13.25a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5h-1.5a.75.75 0 01-.75-.75zm4.75-.75a.75.75 0 000 1.5h3.5a.75.75 0 000-1.5h-3.5z" clip-rule="evenodd"/>
                    </svg>
                    {{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.manage_billing') }}
                    <span wire:loading wire:target="manageBilling">
                        <svg class="h-3 w-3 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </span>
                </button>
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.no_active_subscription') }}</p>
        @endif
    </div>

    {{-- Fonctionnalités incluses --}}
    @if($currentPlan && $currentPlan->features->isNotEmpty())
        <div class="fbk-section">
            <h2 class="fbk-section-heading">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.features_heading') }}</h2>
            <ul class="fbk-feature-list" role="list">
                @foreach($currentPlan->features as $feature)
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
                            <span>{{ $feature->label ?? $feature->key }} :
                                <span class="fbk-feature-value">{{ $feature->getValue() }}</span>
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

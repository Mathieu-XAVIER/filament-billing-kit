<x-filament-panels::page>
    @php
        $subscription = $this->getSubscription();
        $plan = $this->getCurrentPlan();
        $billable = $this->getBillable();
    @endphp

    <div class="space-y-6">
        {{-- Status alert --}}
        @if($subscription && $subscription->onGracePeriod())
            <x-filament::section>
                <x-slot name="heading">
                    <span class="text-warning-600">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.grace_period_title') }}</span>
                </x-slot>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {!! trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.grace_period_message', ['date' => $subscription->ends_at?->format('d/m/Y')]) !!}
                </p>
            </x-filament::section>
        @elseif(!$subscription || $subscription->canceled())
            <x-filament::section>
                <x-slot name="heading">
                    <span class="text-danger-600">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.no_subscription_title') }}</span>
                </x-slot>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.no_subscription_message_full') }}
                </p>
            </x-filament::section>
        @endif

        {{-- Current plan --}}
        <x-filament::section>
            <x-slot name="heading">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.heading') }}</x-slot>

            @if($subscription && $plan)
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.plan_label') }}</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $plan->name }}
                            @if($plan->marketing_badge)
                                <x-filament::badge color="warning" class="ml-2">{{ $plan->marketing_badge }}</x-filament::badge>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.status_label') }}</p>
                        <p class="mt-1">
                            <x-filament::badge
                                :color="match($subscription->stripe_status) {
                                    'active' => 'success',
                                    'trialing' => 'warning',
                                    'past_due' => 'danger',
                                    default => 'gray',
                                }"
                            >
                                {{ match($subscription->stripe_status) {
                                    'active'   => trans('filament-billing-kit::filament-billing-kit.subscription.statuses.active'),
                                    'trialing' => trans('filament-billing-kit::filament-billing-kit.subscription.statuses.trialing'),
                                    'past_due' => trans('filament-billing-kit::filament-billing-kit.subscription.statuses.past_due'),
                                    'canceled' => trans('filament-billing-kit::filament-billing-kit.subscription.statuses.canceled'),
                                    default    => $subscription->stripe_status,
                                } }}
                            </x-filament::badge>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">
                            @if($subscription->onTrial()) {{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.trial_end') }}
                            @elseif($subscription->ends_at) {{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.scheduled_end') }}
                            @else {{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.renewal') }} @endif
                        </p>
                        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
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

                {{-- Manage billing button --}}
                <div class="mt-6">
                    <x-filament::button
                        wire:click="redirectToStripePortal"
                        icon="heroicon-o-arrow-top-right-on-square"
                    >
                        {{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.manage_billing') }}
                    </x-filament::button>
                </div>
            @else
                <p class="text-sm text-gray-500">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.no_active_subscription') }}</p>
            @endif
        </x-filament::section>

        {{-- Plan features / entitlements --}}
        @if($plan && $plan->features->isNotEmpty())
            <x-filament::section>
                <x-slot name="heading">{{ trans('filament-billing-kit::filament-billing-kit.pages.my_subscription.features_heading') }}</x-slot>

                <ul class="space-y-2">
                    @foreach($plan->features as $feature)
                        <li class="flex items-center gap-3 text-sm">
                            @if($feature->isBoolean())
                                @if($feature->getValue())
                                    <x-filament::icon icon="heroicon-o-check-circle" class="h-5 w-5 text-success-500" />
                                @else
                                    <x-filament::icon icon="heroicon-o-x-circle" class="h-5 w-5 text-danger-500" />
                                @endif
                                <span>{{ $feature->label ?? $feature->key }}</span>
                            @else
                                <x-filament::icon icon="heroicon-o-chart-bar" class="h-5 w-5 text-primary-500" />
                                <span>{{ $feature->label ?? $feature->key }} : <strong>{{ $feature->getValue() }}</strong></span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>

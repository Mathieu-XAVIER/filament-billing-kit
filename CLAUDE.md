# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
./vendor/bin/pest                    # run all tests
./vendor/bin/pest --filter=ClassName # run a single test class
./vendor/bin/pest tests/Unit/PlanFeatureTest.php  # run a specific file
./vendor/bin/phpstan analyse --memory-limit=512M  # static analysis (level 5, src/ only)
./vendor/bin/pint                    # format code
./vendor/bin/pint --test             # check formatting without applying

composer check   # pint:test + phpstan
composer fix     # pint + phpstan
```

## Architecture

This is a **Laravel package** (not an application). It integrates as a Filament v5 plugin via `FilamentBillingKitPlugin::make()`.

### Boot flow

1. `FilamentBillingKitServiceProvider` (extends Spatie's `PackageServiceProvider`) registers config, migrations, views, routes, and Livewire components.
2. `EntitlementsManager` and `PaymentDriverContract` are bound as singletons.
3. Stripe events are registered only when `StripeDriver` (or a subclass) is the active driver — checked in `registerStripeEvents()`.
4. `FilamentBillingKitPlugin` registers Filament resources, pages, and widgets into the panel at boot time.

### Payment driver abstraction

`PaymentDriverContract` defines three methods: `checkout()`, `manageBilling()`, `hasYearlyPricing()`. The default implementation is `StripeDriver`. Custom drivers are set via `filament-billing-kit.payment_driver` in config. Stripe events are only wired when the driver is `StripeDriver` or a subclass.

### Entitlements

Two entry points for the same logic:
- `HasEntitlements` trait — added directly to the billable Eloquent model (`User` or `Team`)
- `EntitlementsManager` service — injectable, use `->forBillable($model)` first

`currentPlan()` resolves the plan via `subscription->plan_id` (set by the event listener), falling back to `provider_price_id_monthly/yearly` matching.

### Modes

`filament-billing-kit.mode`:
- `mono-tenant` — billable resolved from `auth()->user()`
- `multi-tenant` — billable resolved from `filament()->getTenant()`

### Testing

Tests use **Pest v3** with **orchestra/testbench** and an **in-memory SQLite** database. The base `TestCase` loads migrations from `database/migrations/` and sets `Fixtures\User` as the billable model. No Stripe API calls should be made in tests — use fakes or mocks.

### Key config keys

`config/filament-billing-kit.php`: `mode`, `billable_model`, `subscription_name`, `payment_driver`, `enable_invoices`, `enable_entitlements`, `enable_widgets`, `require_subscription`, `route_prefix`, `route_middleware`, `billing_return_route`.
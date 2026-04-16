# Filament Billing Kit

A plug-and-play **Filament v5** plugin for Laravel SaaS applications. Manage plans, subscriptions, invoices and feature entitlements from your Filament back-office — powered by **Stripe via Laravel Cashier**.

---

## Features

- **Subscription page** — Standalone frontend (`/billing`) with plan cards, monthly/yearly toggle, and Stripe Checkout tunnel
- **Plans** — CRUD with pricing, periodicity (monthly/annual), trial periods, and Stripe Price ID mapping
- **Subscriptions** — Read-only view with full status lifecycle (active, trialing, past_due, canceled, grace period) and plan change history
- **Invoices** — Cached from Stripe webhooks, displayed with PDF download links
- **Entitlements** — Feature flags and numeric quotas per plan (`hasFeature`, `getFeatureLimit`, `hasReachedLimit`)
- **Admin resources** — Filament resources for Plans, Subscriptions, and Invoices
- **Tenant pages** — "My Subscription" and "My Invoices" pages inside the Filament panel
- **Dashboard widgets** — Active subscriptions, ongoing trials, and failed payments stats
- **Webhook listeners** — Automatically syncs Stripe events to the local database

---

## Requirements

- PHP 8.2+
- Laravel 11+
- Filament 5.x
- Laravel Cashier 15.x
- Livewire 3.x

---

## Installation

### 1. Install via Composer

```bash
composer require vendor/filament-billing-kit
```

### 2. Run the install command

```bash
php artisan filament-billing-kit:install
```

This publishes the config file and runs the migrations.

### 3. Add the `Billable` trait and `HasEntitlements` to your billable model

```php
use Laravel\Cashier\Billable;
use Vendor\FilamentBillingKit\Traits\HasEntitlements;

class User extends Authenticatable
{
    use Billable;
    use HasEntitlements;
}
```

### 4. Register the plugin in your Panel provider

```php
use Vendor\FilamentBillingKit\FilamentBillingKitPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentBillingKitPlugin::make(),
        ]);
}
```

### 5. Configure Stripe

```env
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

Register the Cashier webhook route in `routes/web.php`:

```php
Route::cashierWebhooks('/stripe/webhook');
```

---

## Subscription page (frontend)

The package automatically registers a standalone subscription page accessible at `/billing`.

This page is a Livewire full-page component that handles the complete subscription tunnel:

1. Displays all active plans with pricing and features
2. Allows toggling between monthly and yearly billing
3. Redirects to **Stripe Checkout** on plan selection
4. Handles the return from Stripe (`?checkout=success` / `?checkout=canceled`)
5. Shows the current subscription status with a link to the **Stripe Customer Portal**

### Configuration

```env
BILLING_KIT_ROUTE_PREFIX=billing
```

The route middleware can be customized in `config/filament-billing-kit.php`:

```php
'route_middleware' => ['web', 'auth'],
```

### Customization

Publish the views to customize the layout and templates:

```bash
php artisan vendor:publish --tag=filament-billing-kit-views
```

Publish the CSS source file to customize styles with your Tailwind pipeline:

```bash
php artisan vendor:publish --tag=filament-billing-kit-assets
```

The published CSS uses `@apply` directives and must be compiled with Tailwind. Without publishing, the pre-compiled CSS is loaded automatically.

---

## Configuration

`config/filament-billing-kit.php`:

```php
return [
    'mode'                 => 'mono-tenant',      // 'mono-tenant' | 'multi-tenant'
    'billable_model'       => \App\Models\User::class,
    'subscription_name'    => 'default',
    'enable_invoices'      => true,
    'enable_entitlements'  => true,
    'enable_widgets'       => true,
    'require_subscription' => false,
    'billing_return_route' => 'filament.admin.pages.my-subscription',
    'route_prefix'         => 'billing',
    'route_middleware'     => ['web', 'auth'],
];
```

---

## Usage

### Plans

Navigate to **Billing > Plans** in your admin panel to create and manage plans.

Each plan can have:
- Name, slug, description, display order, and a marketing badge (e.g. "Popular")
- Monthly and yearly prices in cents
- Payment provider Price IDs (`provider_price_id_monthly`, `provider_price_id_yearly`)
- An optional trial period (in days)
- Feature entitlements via a repeater

| Key | Type | Value | Label |
|---|---|---|---|
| `can_export` | boolean | `true` | Export CSV |
| `max_users` | numeric | `50` | Max team members |
| `api_access` | boolean | `false` | API access |

---

### Entitlements

```php
$user->hasFeature('api_access');             // true / false
$user->getFeatureLimit('max_users');         // 50 (or null if unlimited)
$user->hasReachedLimit('max_users', 47);     // false
$user->hasReachedLimit('max_users', 50);     // true
$user->getRemainingQuota('max_users', 47);   // 3 (or null if unlimited)
$user->currentPlan();                        // Plan model
```

You can also inject `EntitlementsManager` directly:

```php
use Vendor\FilamentBillingKit\Services\EntitlementsManager;

public function store(Request $request, EntitlementsManager $entitlements)
{
    $manager = $entitlements->forBillable($request->user());

    if ($manager->hasReachedLimit('max_projects', $request->user()->projects()->count())) {
        abort(403, 'Project limit reached.');
    }
}
```

---

### Require active subscription (middleware)

```php
Route::middleware(\Vendor\FilamentBillingKit\Middleware\RequiresActiveSubscription::class)
    ->group(function () {
        // Protected routes
    });
```

Or set `require_subscription => true` in the config to apply it globally.

---

### Plugin options

```php
FilamentBillingKitPlugin::make()
    ->adminResources(false)  // hide Plans / Subscriptions / Invoices resources
    ->tenantPages(false)     // hide My Subscription / My Invoices pages
    ->widgets(false),        // hide dashboard stat widgets
```

---

## Multi-tenant mode

When `mode` is set to `multi-tenant`, the plugin resolves the billable entity from `filament()->getTenant()` instead of `auth()->user()`.

```php
'mode'           => 'multi-tenant',
'billable_model' => \App\Models\Team::class,
```

---

## Database tables

| Table | Description |
|---|---|
| `plans` | Plan definitions (pricing, periodicity, Stripe price IDs) |
| `plan_features` | Feature flags and quotas per plan |
| `subscription_plan_changes` | Audit log of plan changes |
| `subscriptions` | Extended with a `plan_id` column (added to Cashier's table) |
| `billing_invoices` | Invoice cache populated by Stripe webhooks |

---

## Stripe webhooks

| Stripe event | Effect |
|---|---|
| `customer.subscription.created` | Links the subscription to the matching local plan |
| `customer.subscription.updated` | Detects plan changes and writes to audit log |
| `customer.subscription.deleted` | Logged for audit |
| `invoice.payment_succeeded` | Upserts invoice with status `paid` |
| `invoice.payment_failed` | Upserts invoice with status `open` |

---

## Testing

```bash
composer test
```

---

## License

MIT — see [LICENSE](LICENSE).

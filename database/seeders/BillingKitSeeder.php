<?php

namespace Mxavier\FilamentBillingKit\Database\Seeders;

use Illuminate\Database\Seeder;
use Mxavier\FilamentBillingKit\Models\Plan;
use Mxavier\FilamentBillingKit\Models\PlanFeature;

class BillingKitSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'plan' => [
                    'name' => 'Starter',
                    'slug' => 'starter',
                    'description' => 'Idéal pour les indépendants et les petites équipes qui démarrent.',
                    'is_active' => true,
                    'is_featured' => false,
                    'sort_order' => 1,
                    'marketing_badge' => null,
                    'price_monthly' => 900,    // 9,00 €
                    'price_yearly' => null,
                    'currency' => 'EUR',
                    'trial_days' => 14,
                    'provider_price_id_monthly' => 'price_1TMoldGtkjAzdWQkHq7CDZb2',
                    'provider_price_id_yearly' => null,
                ],
                'features' => [
                    ['key' => 'max_users', 'type' => 'numeric', 'value' => '3', 'label' => 'Membres d\'équipe'],
                    ['key' => 'max_projects', 'type' => 'numeric', 'value' => '5', 'label' => 'Projets actifs'],
                    ['key' => 'storage_gb', 'type' => 'numeric', 'value' => '5', 'label' => 'Stockage (Go)'],
                    ['key' => 'api_access', 'type' => 'boolean', 'value' => 'false', 'label' => 'Accès API'],
                    ['key' => 'can_export', 'type' => 'boolean', 'value' => 'false', 'label' => 'Export CSV/PDF'],
                    ['key' => 'priority_support', 'type' => 'boolean', 'value' => 'false', 'label' => 'Support prioritaire'],
                ],
            ],
            [
                'plan' => [
                    'name' => 'Pro',
                    'slug' => 'pro',
                    'description' => 'Pour les équipes en croissance avec des besoins avancés.',
                    'is_active' => true,
                    'is_featured' => true,
                    'sort_order' => 2,
                    'marketing_badge' => 'Populaire',
                    'price_monthly' => 2900,   // 29,00 €
                    'price_yearly' => 29000,  // 290,00 € (2 mois offerts)
                    'currency' => 'EUR',
                    'trial_days' => 14,
                    'provider_price_id_monthly' => 'price_1TMoldGtkjAzdWQkmHFZUcMt',
                    'provider_price_id_yearly' => 'price_1TMoldGtkjAzdWQkBOG3SNCl',
                ],
                'features' => [
                    ['key' => 'max_users', 'type' => 'numeric', 'value' => '15', 'label' => 'Membres d\'équipe'],
                    ['key' => 'max_projects', 'type' => 'numeric', 'value' => '50', 'label' => 'Projets actifs'],
                    ['key' => 'storage_gb', 'type' => 'numeric', 'value' => '50', 'label' => 'Stockage (Go)'],
                    ['key' => 'api_access', 'type' => 'boolean', 'value' => 'true', 'label' => 'Accès API'],
                    ['key' => 'can_export', 'type' => 'boolean', 'value' => 'true', 'label' => 'Export CSV/PDF'],
                    ['key' => 'priority_support', 'type' => 'boolean', 'value' => 'false', 'label' => 'Support prioritaire'],
                ],
            ],
            [
                'plan' => [
                    'name' => 'Enterprise',
                    'slug' => 'enterprise',
                    'description' => 'Pour les grandes organisations avec des volumes élevés et un support dédié.',
                    'is_active' => true,
                    'is_featured' => false,
                    'sort_order' => 3,
                    'marketing_badge' => null,
                    'price_monthly' => null,
                    'price_yearly' => 99900,  // 999,00 € / an
                    'currency' => 'EUR',
                    'trial_days' => null,
                    'provider_price_id_monthly' => null,
                    'provider_price_id_yearly' => 'price_1TMoleGtkjAzdWQkndHGz44x',
                ],
                'features' => [
                    ['key' => 'max_users', 'type' => 'numeric', 'value' => '100', 'label' => 'Membres d\'équipe'],
                    ['key' => 'max_projects', 'type' => 'numeric', 'value' => '500', 'label' => 'Projets actifs'],
                    ['key' => 'storage_gb', 'type' => 'numeric', 'value' => '500', 'label' => 'Stockage (Go)'],
                    ['key' => 'api_access', 'type' => 'boolean', 'value' => 'true', 'label' => 'Accès API'],
                    ['key' => 'can_export', 'type' => 'boolean', 'value' => 'true', 'label' => 'Export CSV/PDF'],
                    ['key' => 'priority_support', 'type' => 'boolean', 'value' => 'true', 'label' => 'Support prioritaire'],
                ],
            ],
        ];

        foreach ($plans as $data) {
            $plan = Plan::updateOrCreate(
                ['slug' => $data['plan']['slug']],
                $data['plan']
            );

            $plan->features()->delete();

            foreach ($data['features'] as $feature) {
                PlanFeature::create(array_merge($feature, ['plan_id' => $plan->id]));
            }
        }

        $this->command->info('BillingKitSeeder : 3 plans créés avec leurs fonctionnalités.');
    }
}

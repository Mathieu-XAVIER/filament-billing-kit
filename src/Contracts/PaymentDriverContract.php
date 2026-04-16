<?php

namespace Mxavier\FilamentBillingKit\Contracts;

use Illuminate\Database\Eloquent\Model;
use Mxavier\FilamentBillingKit\Models\Plan;
use RuntimeException;

interface PaymentDriverContract
{
    /**
     * Initie une session de checkout et retourne l'URL de redirection.
     *
     * @throws RuntimeException en cas d'erreur (prix manquant, API, etc.)
     */
    public function checkout(
        Model $billable,
        Plan $plan,
        string $billingPeriod,
        string $successUrl,
        string $cancelUrl
    ): string;

    /**
     * Retourne l'URL du portail de gestion d'abonnement.
     *
     * @throws RuntimeException en cas d'erreur
     */
    public function manageBilling(Model $billable, string $returnUrl): string;

    /**
     * Indique si le plan proposé par ce driver supporte la facturation annuelle.
     */
    public function hasYearlyPricing(Plan $plan): bool;
}

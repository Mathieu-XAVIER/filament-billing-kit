<?php

return [
    'plan' => [
        'label' => 'Plan',
        'plural_label' => 'Plans',
        'fields' => [
            'name' => 'Nom',
            'slug' => 'Slug',
            'description' => 'Description',
            'is_active' => 'Actif',
            'periodicity' => 'Période de facturation',
            'price' => 'Prix (en centimes)',
            'currency' => 'Devise',
            'trial_days' => "Jours d'essai",
            'provider_price_id' => 'ID prix fournisseur',
            'marketing_badge' => 'Badge',
            'display_order' => "Ordre d'affichage",
        ],
        'periodicity' => [
            'monthly' => 'Mensuel',
            'annual' => 'Annuel',
        ],
    ],

    'subscription' => [
        'label' => 'Abonnement',
        'plural_label' => 'Abonnements',
        'statuses' => [
            'active' => 'Actif',
            'trialing' => 'Essai',
            'past_due' => 'Paiement en retard',
            'canceled' => 'Annulé',
            'incomplete' => 'Incomplet',
            'incomplete_expired' => 'Expiré',
            'paused' => 'En pause',
        ],
    ],

    'invoice' => [
        'label' => 'Facture',
        'plural_label' => 'Factures',
        'statuses' => [
            'paid' => 'Payée',
            'open' => 'En attente',
            'draft' => 'Brouillon',
            'void' => 'Annulée',
            'uncollectible' => 'Irrécouvrable',
        ],
    ],

    'layout' => [
        'page_title' => 'Facturation',
        'brand' => 'Facturation',
        'nav' => [
            'aria_label' => 'Navigation facturation',
            'plans' => 'Plans',
            'subscription' => 'Mon abonnement',
            'invoices' => 'Mes factures',
        ],
    ],

    'pages' => [
        'plans' => [
            'error_title' => 'Une erreur est survenue',
            'subscription_activated' => 'Abonnement activé !',
            'subscription_activated_message' => 'Votre abonnement est maintenant actif. Bienvenue à bord !',
            'view_subscription' => 'Voir mon abonnement',
            'payment_canceled' => 'Paiement annulé',
            'payment_canceled_message' => "Vous avez annulé le processus de paiement. Aucun montant n'a été débité.",
            'back_to_plans' => 'Retour aux plans',
            'current_subscription_label' => 'Abonnement actuel',
            'manage_billing' => 'Gérer la facturation',
            'grace_period_ends' => 'Se termine le :date',
            'title_change' => 'Changer de plan',
            'title_choose' => 'Choisissez votre plan',
            'subtitle' => "Commencez dès aujourd'hui, annulez à tout moment.",
            'monthly' => 'Mensuel',
            'yearly' => 'Annuel',
            'empty' => 'Aucun plan disponible pour le moment.',
            'custom_quote' => 'Sur devis',
            'current_plan' => 'Plan actuel',
            'contact_us' => 'Nous contacter',
            'get_started' => 'Commencer',
            'change_plan' => 'Changer de plan',
            'redirecting' => 'Redirection…',
            'price_period_monthly' => 'mois',
            'price_period_yearly' => 'an',
        ],

        'my_subscription' => [
            'title' => 'Mon abonnement',
            'error_title' => 'Une erreur est survenue',
            'grace_period_title' => 'Abonnement en période de grâce',
            'grace_period_message' => 'Votre abonnement a été annulé et prendra fin le <strong>:date</strong>. Vous pouvez le réactiver avant cette date.',
            'no_subscription_title' => 'Aucun abonnement actif',
            'no_subscription_message' => "Vous n'avez pas d'abonnement actif.",
            'no_subscription_message_full' => "Vous n'avez pas d'abonnement actif. Souscrivez un plan pour accéder à toutes les fonctionnalités.",
            'no_subscription_cta' => 'Choisir un plan →',
            'heading' => 'Mon abonnement actuel',
            'plan_label' => 'Plan',
            'status_label' => 'Statut',
            'trial_end' => "Fin d'essai",
            'scheduled_end' => 'Fin prévue',
            'renewal' => 'Renouvellement',
            'manage_billing' => 'Gérer ma facturation',
            'no_active_subscription' => 'Aucun abonnement actif.',
            'features_heading' => 'Fonctionnalités incluses',
        ],

        'my_invoices' => [
            'title' => 'Mes factures',
            'empty' => 'Aucune facture pour le moment.',
            'columns' => [
                'number' => 'N° Facture',
                'amount' => 'Montant',
                'status' => 'Statut',
                'date' => 'Date',
            ],
            'pdf' => 'PDF',
        ],
    ],

    'widgets' => [
        'active_subscriptions' => 'Abonnements actifs',
        'ongoing_trials' => 'Essais en cours',
        'failed_payments' => 'Paiements échoués',
    ],
];

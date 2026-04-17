<?php

return [
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
];

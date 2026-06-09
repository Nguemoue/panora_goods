<?php

return [
    'attributes' => [
        // User attributes
        'name' => 'Nom complet',
        'email' => 'Adresse e-mail',
        'password' => 'Mot de passe',
        'password_confirmation' => 'Confirmation du mot de passe',
        'status' => 'Statut',
        'role' => 'Rôle',
        'email_verified_at' => 'E-mail vérifié à',
        'two_factor_confirmed_at' => 'A2F confirmée à',
        'two_factor_secret' => 'Secret A2F',

        // Supplier attributes
        'phone' => 'Numéro de téléphone',
        'contact_person' => 'Personne de contact',
        'address' => 'Adresse',
        'description' => 'Description',

        // Phone attributes
        'brand' => 'Marque',
        'brand_id' => 'Marque',
        'supplier' => 'Fournisseur',
        'supplier_id' => 'Fournisseur',
        'model_number' => 'Numéro de modèle',
        'color' => 'Couleur',
        'supplier_price' => 'Prix d\'achat',
        'selling_price' => 'Prix de vente',
        'stock_quantity' => 'Quantité en stock',
        'ram' => 'RAM',
        'storage' => 'Stockage',
        'specs' => 'Spécifications',
        'images' => 'Images',
        'image' => 'Image',

        // Sale attributes
        'phone_id' => 'Téléphone',
        'quantity' => 'Quantité',
        'supplier_price_at_sale' => 'Prix d\'achat à la vente',
        'sale_price' => 'Prix de vente',
        'profit' => 'Bénéfice',
        'customer_name' => 'Nom du client',
        'sold_at' => 'Date de la vente',

        // Brand attributes
        'brand_name' => 'Nom de la marque',
    ],

    'required' => ':attribute est requis.',
    'email' => ':attribute doit être une adresse e-mail valide.',
    'unique' => ':attribute a déjà été pris.',
    'min' => ':attribute doit comporter au moins :min caractères.',
    'max' => ':attribute ne peut pas dépasser :max caractères.',
    'numeric' => ':attribute doit être un nombre.',
    'integer' => ':attribute doit être un entier.',
    'confirmed' => 'La confirmation de :attribute ne correspond pas.',
    'min_value' => ':attribute doit être au moins de :min.',
    'max_value' => ':attribute ne peut pas dépasser :max.',
];

<?php

return [
    'attributes' => [
        // User attributes
        'name' => 'Full Name',
        'email' => 'Email Address',
        'password' => 'Password',
        'password_confirmation' => 'Password Confirmation',
        'status' => 'Status',
        'role' => 'Role',
        'email_verified_at' => 'Email Verified At',
        'two_factor_confirmed_at' => 'Two-Factor Confirmed At',
        'two_factor_secret' => 'Two-Factor Secret',

        // Supplier attributes
        'phone' => 'Phone Number',
        'contact_person' => 'Contact Person',
        'address' => 'Address',

        // Phone attributes
        'brand' => 'Brand',
        'brand_id' => 'Brand',
        'supplier' => 'Supplier',
        'supplier_id' => 'Supplier',
        'model_number' => 'Model Number',
        'color' => 'Color',
        'supplier_price' => 'Supplier Price',
        'selling_price' => 'Selling Price',
        'stock_quantity' => 'Stock Quantity',
        'ram' => 'RAM',
        'storage' => 'Storage',
        'specs' => 'Specifications',
        'images' => 'Images',
        'image' => 'Image',

        // Sale attributes
        'phone_id' => 'Phone',
        'quantity' => 'Quantity',
        'supplier_price_at_sale' => 'Supplier Price at Sale',
        'sale_price' => 'Sale Price',
        'profit' => 'Profit',
        'customer_name' => 'Customer Name',
        'sold_at' => 'Date of Sale',

        // Brand attributes
        'brand_name' => 'Brand Name',
    ],

    'required' => ':attribute is required.',
    'email' => ':attribute must be a valid email address.',
    'unique' => ':attribute has already been taken.',
    'min' => ':attribute must be at least :min characters.',
    'max' => ':attribute may not be greater than :max characters.',
    'numeric' => ':attribute must be a number.',
    'integer' => ':attribute must be an integer.',
    'confirmed' => ':attribute confirmation does not match.',
    'min_value' => ':attribute must be at least :min.',
    'max_value' => ':attribute may not be greater than :max.',
];

# Translation System Documentation

## Overview
This application uses a modular translation system where each resource has its own dedicated translation file for better organization and maintainability.

## Language Files Structure

```
resources/lang/en/
├── messages.php          # Common messages & general translations
├── validation.php        # Form validation attributes & messages
├── actions.php          # Action buttons & form actions
├── users.php            # User resource specific translations
├── suppliers.php        # Supplier resource specific translations
├── sales.php            # Sales resource specific translations
├── phones.php           # Phones resource specific translations
└── brands.php           # Brands resource specific translations
```

## File Descriptions

### 1. **messages.php**
Contains common, shared translations used across multiple resources:
- Navigation group names (Administration, Inventory, Sales & Orders)
- Common status values (Active, Inactive, In Stock, Out of Stock)
- General labels used in multiple places (Name, Email, Status, Created, etc.)
- Common UI messages

### 2. **validation.php**
Contains form validation attribute names and validation messages:
- Attribute translations for all form fields
- Error messages for validation rules

### 3. **actions.php**
Contains action button labels used across all resources:
- CRUD actions (Create, Edit, Delete, Save)
- Bulk actions (Bulk Delete, Select All)
- Navigation actions (Next, Previous, Back)
- Other common button labels

### 4. **users.php**
User resource specific translations:
- Page titles and navigation labels
- Form field labels and helper text
- Tab names (Personal, Security, Verification, Two-Factor)
- Table column headers
- Confirmation messages
- Empty state messages
- Filter labels

### 5. **suppliers.php**
Supplier resource specific translations:
- Page titles and navigation labels
- Form field labels (Name, Email, Phone, Address, Contact Person)
- Table column headers
- Confirmation messages
- Empty state messages

### 6. **sales.php**
Sales resource specific translations:
- Page titles and navigation labels
- Form sections (Sale Details, Pricing & Profit, Customer & Timing)
- Form field labels (Phone, Quantity, Supplier Price, Selling Price, Profit, etc.)
- Table column headers (Date, Phone, Quantity, Total Revenue, Total Profit)
- Filter labels
- Confirmation messages

### 7. **phones.php**
Phones resource specific translations:
- Page titles and navigation labels
- Form tabs (General, Pricing, Specifications, Images)
- Form field labels for each tab
- Status options (In Stock, Out of Stock, Discontinued)
- Table column headers with detailed information
- Filter labels
- Stock management messages
- Empty state messages

### 8. **brands.php**
Brand resource specific translations:
- Page titles and navigation labels
- Simple form field labels (Brand Name)
- Table column headers
- Confirmation messages
- Empty state messages

## How to Use Translations

### In Forms and Tables

```php
// Using translations in form fields
TextInput::make('name')
    ->label(__('validation.attributes.name'))
    ->placeholder(__('phones.enter_phone_name'))
    ->helperText(__('phones.cost_price_help')),

// Using translations in table columns
TextColumn::make('name')
    ->label(__('validation.attributes.name'))
    ->searchable(),

// Using translations in buttons
Action::make('addStock')
    ->label(__('phones.add_stock'))
    ->modalHeading(__('phones.add_stock')),
```

### Translation Keys Hierarchy

1. **Resource-specific**: First, check the resource-specific file (e.g., `phones.enter_phone_name`)
2. **Common actions**: Then, check actions.php (e.g., `actions.save`)
3. **General messages**: Finally, check messages.php (e.g., `messages.status`)
4. **Validation**: For form attributes, use validation.php (e.g., `validation.attributes.name`)

## Adding New Translations

When adding new fields or text to a resource:

1. **Identify the context**: Is it resource-specific or common?
2. **Choose the appropriate file**:
   - Resource-specific text → Use resource file (e.g., phones.php)
   - Action button → Use actions.php
   - Form field attribute → Use validation.php
   - Shared text → Use messages.php
3. **Follow naming conventions**:
   - Use snake_case for translation keys
   - Group related keys together
   - Use descriptive names
4. **Add to both the appropriate file and validation.php** if it's a form field

## Example: Adding a New Field to Phones

If you add a new field `warranty_months` to the Phone form:

1. **In phones.php**:
```php
'warranty' => 'Warranty',
'warranty_months' => 'Warranty (Months)',
'enter_warranty_months' => 'Enter warranty period in months',
```

2. **In validation.php**:
```php
'attributes' => [
    'warranty_months' => 'Warranty Period',
]
```

3. **In the PhoneForm**:
```php
TextInput::make('warranty_months')
    ->label(__('phones.warranty_months'))
    ->placeholder(__('phones.enter_warranty_months'))
```

## Multi-Language Support

When ready to add more languages (e.g., French):

1. Create new language directories:
   - `resources/lang/fr/`
   - `resources/lang/es/`
   - etc.

2. Copy all translation files to the new language directory

3. Translate the values while keeping the keys unchanged

4. Update app configuration to support multiple locales

## Best Practices

✅ **Do**:
- Use `__('namespace.key')` for translations
- Keep keys consistent across files
- Group related translations together
- Use snake_case for keys
- Provide meaningful translation keys

❌ **Don't**:
- Hardcode text in views or forms
- Use inconsistent key naming
- Put all translations in one file
- Use generic key names like 'text1', 'text2'
- Leave untranslated strings

## Quick Reference

| Type | File | Example |
|------|------|---------|
| Resource-specific labels | `phones.php` | `__('phones.enter_phone_name')` |
| Action buttons | `actions.php` | `__('actions.save')` |
| Form attributes | `validation.php` | `__('validation.attributes.name')` |
| Common text | `messages.php` | `__('messages.status')` |

## Accessing Translations in Code

```php
// In PHP files
__('phones.page_title')

// In Blade templates
{{ __('phones.page_title') }}

// With variables
__('users.edit_user', ['name' => $user->name])

// Default value if key doesn't exist
__('phones.missing_key', [], 'Default text')
```

# Phone CRM - Development Guidelines

This document consolidates development guidelines for the Phone CRM project. It's based on Laravel Boost specifications and best practices for this application.

## Table of Contents

1. [Foundational Context](#foundational-context)
2. [Stack & Package Versions](#stack--package-versions)
3. [Core Conventions](#core-conventions)
4. [Skills & Tools](#skills--tools)
5. [PHP & Laravel Guidelines](#php--laravel-guidelines)
6. [Testing](#testing)
7. [Filament](#filament)
8. [Tailwind CSS](#tailwindcss)
9. [Code Formatting](#code-formatting)
10. [Debugging & Tools](#debugging--tools)

---

## Foundational Context

This is a **Laravel application** curated with Laravel Boost specifications. All guidelines should be followed closely to maintain code quality and consistency.

### Key Principles

- **Convention over Configuration**: Follow existing code patterns before creating new ones.
- **Laravel Way**: Use Laravel's built-in features and ORM capabilities.
- **Type Safety**: Always use explicit type hints and return type declarations.
- **Testing First**: Write tests to validate functionality; avoid one-off verification scripts.

---

## Stack & Package Versions

### Core

- **PHP**: 8.4
- **Laravel**: v13
- **Laravel Boost**: v2

### Ecosystem

| Package | Version | Alias | Usage |
|---------|---------|-------|-------|
| filament/filament | v5 | FILAMENT | Admin panels & dashboards |
| livewire/livewire | v4 | LIVEWIRE | Reactive components |
| pestphp/pest | v4 | PEST | Testing framework |
| phpunit/phpunit | v12 | PHPUNIT | Unit testing |
| tailwindcss | v4 | TAILWINDCSS | Utility-first CSS |
| laravel/prompts | v0 | PROMPTS | CLI prompts |
| laravel/pail | v1 | PAIL | Log monitoring |
| laravel/pint | v1 | PINT | Code formatting |
| laravel/mcp | v0 | MCP | Model Context Protocol |

---

## Core Conventions

### Code Style

- **Control Structures**: Always use curly braces, even for single-line bodies.
  ```php
  if ($condition) {
      doSomething();
  }
  ```

- **Variable Naming**: Use descriptive camelCase names.
  ```php
  // Good
  $isRegisteredForDiscounts = true;
  
  // Bad
  $discount = true;
  ```

- **Reusability**: Check for existing components/utilities before creating new ones.

### Directory Structure

- Stick to existing directory structure.
- Do not create new base folders without explicit approval.
- Use the directory organization as a guide for file placement.

---

## Skills & Tools

### Available Skills (Use When Relevant)

1. **`pest-testing`**
   - Activate for: Writing, editing, fixing, or refactoring tests
   - Covers: it()/expect() syntax, datasets, mocking, browser testing, Livewire tests
   - Do NOT use for: Factories, seeders, migrations, controllers, models

2. **`tailwindcss-development`**
   - Activate for: Tailwind utility classes, responsive layouts, component styling
   - Covers: Grid layouts, flex structures, dark mode, spacing, typography
   - Do NOT use for: Backend PHP, database queries, API routes, CSS audits

3. **`Filament-PHP-v5-Development`**
   - Activate for: Building admin panels, resources, forms, tables
   - Covers: Resources, forms, tables, actions, widgets, relation managers, testing patterns

### Laravel Boost Tools

- **`search-docs`**: Search version-specific documentation for installed packages
- **`database-query`**: Execute read-only SQL queries
- **`database-schema`**: Inspect table structure
- **`get-absolute-url`**: Generate URLs with correct scheme/domain/port
- **`browser-logs`**: Debug frontend JavaScript errors
- **`last-error`**: Retrieve backend exceptions

---

## PHP & Laravel Guidelines

### Type Declarations

**Always use explicit return types and parameter hints:**

```php
protected function isAccessible(User $user, ?string $path = null): bool
{
    return $user->can('access', $path);
}
```

### Constructor Property Promotion

Use PHP 8 syntax:

```php
public function __construct(
    public GitHub $github,
    public Logger $logger
) { }
```

Do not allow empty constructors unless private.

### Comments & Documentation

- Prefer **PHPDoc blocks** over inline comments.
- Only use inline comments for exceptionally complex logic.
- Include array shape definitions in PHPDoc when appropriate:
  ```php
  /**
   * @param array{id: int, name: string, email: string} $user
   * @return bool
   */
  ```

### Enums

Use TitleCase for enum keys:
```php
enum CompanyType: string
{
    case Business = 'business';
    case Individual = 'individual';
    case Enterprise = 'enterprise';
}
```

---

## Database & Eloquent

### Best Practices

1. **Use Eloquent Relationships**: Prefer relationship methods over raw queries.
   ```php
   // Good
   $user->orders()->where('status', 'completed')->get();
   
   // Avoid
   DB::table('orders')->where('user_id', $user->id)->get();
   ```

2. **Eager Loading**: Prevent N+1 queries by loading relationships eagerly.
   ```php
   $users = User::with('orders', 'profile')->get();
   ```

3. **Avoid DB::** namespace when Eloquent can be used.
   ```php
   // Prefer
   User::query()->where('active', true)->get();
   
   // Over
   DB::table('users')->where('active', true)->get();
   ```

### Model Creation

When creating new models:
- Use `php artisan make:model --factory --seeder ModelName`
- Ask the user if additional resources (factories, seeders) are needed
- Include return type hints on relationship methods

### Migrations & Form Requests

- Create migrations using `php artisan make:migration`
- Create Form Request classes for validation (never inline validation in controllers)
- Check sibling Form Requests for array/string based validation rules

### APIs & Resources

- Use Eloquent API Resources for API responses
- Follow existing API versioning patterns if present

---

## Testing

### Framework: Pest v4

### Creating Tests

```bash
# Feature test (default)
php artisan make:test --pest FeatureName

# Unit test
php artisan make:test --unit --pest UnitName
```

### Running Tests

```bash
# Run all tests
php artisan test --compact

# Run specific test
php artisan test --compact --filter=testName
```

### Key Rules

- Use **factories** when creating models for tests (check for custom states first)
- Most tests should be **feature tests**, not unit tests
- Use `$this->faker->word()` or `fake()->randomDigit()` for random data
- **Do NOT delete tests without approval**

### Example: Livewire Component Test

```php
use function Pest\Livewire\livewire;

livewire(CreateUser::class)
    ->fillForm([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ])
    ->call('create')
    ->assertNotified()
    ->assertRedirect();
```

---

## Filament

### Overview

Filament is a Server-Driven UI (SDUI) framework for building admin panels. It's built on Livewire, Alpine.js, and Tailwind CSS.

### Creating Filament Resources

Use Filament-specific Artisan commands:

```bash
php artisan make:filament-resource ResourceName --no-interaction
```

### Patterns

#### Static Make Methods

Initialize all components using static `make()`:

```php
Select::make('type')
    ->options(CompanyType::class)
    ->required()
```

#### Conditional Field Visibility

Use `Get $get` to read other field values:

```php
use Filament\Schemas\Components\Utilities\Get;

Select::make('type')
    ->options(CompanyType::class)
    ->required()
    ->live(),

TextInput::make('company_name')
    ->required()
    ->visible(fn (Get $get): bool => $get('type') === 'business'),
```

#### Computed Table Columns

Use `state()` with a Closure:

```php
TextColumn::make('full_name')
    ->state(fn (User $record): string => "{$record->first_name} {$record->last_name}"),
```

#### Actions with Forms

```php
Action::make('updateEmail')
    ->schema([
        TextInput::make('email')->email()->required(),
    ])
    ->action(fn (array $data, User $record) => $record->update($data))
```

### Testing Filament

Always authenticate before testing panel functionality:

```php
use function Pest\Livewire\livewire;

livewire(ListUsers::class)
    ->assertCanSeeTableRecords($users)
    ->searchTable($users->first()->name)
    ->assertCanSeeTableRecords($users->take(1));
```

### Correct Namespaces

| Component Type | Namespace |
|---|---|
| Form fields (TextInput, Select, etc.) | `Filament\Forms\Components\` |
| Infolist entries (TextEntry, etc.) | `Filament\Infolists\Components\` |
| Layout (Grid, Section, Tabs, etc.) | `Filament\Schemas\Components\` |
| Utilities (Get, Set) | `Filament\Schemas\Components\Utilities\` |
| Actions (DeleteAction, etc.) | `Filament\Actions\` |
| Icons | `Filament\Support\Icons\Heroicon` |

### Common Mistakes

1. **File Visibility**: Files are `private` by default. Use `->visibility('public')` when needed.
2. **Layout Width**: Grid/Section do not span full width by default. Explicitly set column spans.

---

## TailwindCSS

### Overview

TailwindCSS v4 is the utility-first CSS framework used in this project.

### Use Cases

Activate the `tailwindcss-development` skill for:
- Writing/fixing Tailwind utility classes in HTML templates
- Building responsive grid layouts (multi-column card grids)
- Creating flex/grid page structures (dashboards, sidebars, topbars)
- Styling UI components (cards, tables, navbars, forms, badges)
- Adding dark mode variants
- Fixing spacing or typography issues

### Do NOT Use For

- Backend PHP logic
- Database queries or API routes
- JavaScript with no HTML/CSS component
- CSS file audits or build configuration
- Vanilla CSS

---

## Code Formatting

### Laravel Pint

This project uses **Laravel Pint** (v1) for PHP code formatting.

**After modifying PHP files, run:**

```bash
vendor/bin/pint --dirty --format agent
```

**Do NOT use:**
```bash
vendor/bin/pint --test --format agent  # This only checks, doesn't fix
```

---

## Configuration & Environment

### Configuration Files

Always read configuration from files, not from `env()` function directly:

```php
// Good
config('app.name')

// Bad
env('APP_NAME')
```

The `env()` function should only be used directly in `config/` files.

### .env Variables

Read the `.env` file directly when checking environment variables.

---

## Debugging & Tools

### Laravel Artisan

**Discover commands:**
```bash
php artisan list
```

**Check command parameters:**
```bash
php artisan [command] --help
```

**Execute PHP code:**
```bash
php artisan tinker --execute "your code here"
```

### Database

- Use `database-schema` tool to inspect table structure
- Use `database-query` tool for read-only SQL queries
- Use migrations for schema changes

### Routes

Inspect routes:
```bash
php artisan route:list
```

### Frontend Issues

If a frontend change isn't reflected in the UI, run:
```bash
npm run build
npm run dev
# or
composer run dev
```

### Browser Logs

Use the `browser-logs` tool to debug JavaScript errors. Only recent logs are useful.

### Vite Manifest Errors

If you see `Unable to locate file in Vite manifest`, run:
```bash
npm run build
# or ask user to run:
npm run dev
```

---

## Best Practices Summary

| Area | Best Practice |
|------|---|
| **Naming** | Use descriptive camelCase (e.g., `isRegisteredForDiscounts`) |
| **Database** | Use Eloquent + eager loading; avoid `DB::` namespace |
| **Validation** | Create Form Request classes; never inline validation |
| **Testing** | Pest v4; use factories; write feature tests primarily |
| **Code Format** | Run `vendor/bin/pint --dirty --format agent` before finalizing |
| **Types** | Always use explicit return types and parameter hints |
| **Reusability** | Check for existing components before creating new ones |
| **Comments** | Use PHPDoc blocks; avoid inline comments |
| **Configuration** | Use `config()` function; never `env()` outside of config files |

---

## Getting Help

- Use **`search-docs`** to find version-specific documentation
- Use **`laravel-boost-last-error`** for backend exceptions
- Use **`laravel-boost-browser-logs`** for frontend errors
- Check **existing files** in the codebase for patterns and conventions

---

**Last Updated**: March 26, 2026  
**For Questions**: Refer to CLAUDE.md, GEMINI.md, or AGENTS.md in the project root

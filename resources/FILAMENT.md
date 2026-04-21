# Filament v5 Development Guide

Quick reference for building admin panels and dashboards with Filament v5.

## Overview

Filament is a Server-Driven UI (SDUI) framework for Laravel built on:
- **Livewire** - Reactive components
- **Alpine.js** - JavaScript framework
- **Tailwind CSS** - Styling

## Creating Resources

```bash
php artisan make:filament-resource ResourceName --no-interaction
```

## Core Patterns

### 1. Static Make Methods

All components use static `make()` initialization:

```php
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

TextInput::make('name')
    ->required()
    ->maxLength(255),

Select::make('status')
    ->options(StatusEnum::class)
    ->required()
```

### 2. Dynamic Configuration with Closures

Most methods accept Closures for dynamic behavior:

```php
TextInput::make('email')
    ->label(fn () => 'User Email Address')
    ->visible(fn () => auth()->user()->isAdmin())
    ->disabled(fn (Model $record) => $record->isLocked())
```

### 3. Conditional Field Visibility

Use `Get $get` to read other form field values:

```php
use Filament\Schemas\Components\Utilities\Get;

Select::make('type')
    ->options(CompanyType::class)
    ->required()
    ->live(),

TextInput::make('company_name')
    ->required()
    ->visible(fn (Get $get): bool => $get('type') === 'business')
```

### 4. Computed Table Columns

Use `state()` with a Closure to compute column values:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('full_name')
    ->state(fn (User $record): string => "{$record->first_name} {$record->last_name}")
    ->sortable(query: fn (Builder $query) => $query->orderBy('first_name')->orderBy('last_name'))
```

### 5. Actions with Modal Forms

Encapsulate a button with optional modal and logic:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;

Action::make('updateEmail')
    ->form([
        TextInput::make('email')
            ->email()
            ->required()
            ->currentPassword()
            ->required(),
    ])
    ->action(fn (array $data, User $record) => $record->update($data))
    ->successNotificationTitle('Email updated')
```

## Component Namespaces

**Form Fields:**
```php
use Filament\Forms\Components\{TextInput, Select, Checkbox, DatePicker};
```

**Infolist Entries:**
```php
use Filament\Infolists\Components\{TextEntry, IconEntry, BadgeEntry};
```

**Layout Components:**
```php
use Filament\Schemas\Components\{Grid, Section, Fieldset, Tabs, Wizard};
```

**Schema Utilities:**
```php
use Filament\Schemas\Components\Utilities\{Get, Set};
```

**Actions:**
```php
use Filament\Actions\{CreateAction, EditAction, DeleteAction, ViewAction};
// Never use: Filament\Tables\Actions\, Filament\Forms\Actions\, etc.
```

**Icons:**
```php
use Filament\Support\Icons\Heroicon;

// Usage: Heroicon::PencilSquare, Heroicon::TrashIcon, etc.
```

## Testing Filament Components

### Basic Setup

Always authenticate before testing panel functionality:

```php
use function Pest\Livewire\livewire;

beforeEach(fn () => $this->actingAs(User::factory()->create()));
```

### Testing Tables

```php
it('can list users', function () {
    $users = User::factory(3)->create();
    
    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->searchTable($users->first()->name)
        ->assertCanSeeTableRecords($users->take(1))
        ->assertCanNotSeeTableRecords($users->skip(1));
});
```

### Testing Create Resource

```php
it('can create a user', function () {
    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseHas(User::class, [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});
```

### Testing Validation

```php
it('validates required fields', function () {
    livewire(CreateUser::class)
        ->fillForm([
            'name' => null,
            'email' => 'invalid-email',
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'email' => 'email',
        ])
        ->assertNotNotified();
});
```

### Testing Actions in Pages

```php
use Filament\Actions\DeleteAction;

it('can delete a user', function () {
    $user = User::factory()->create();
    
    livewire(EditUser::class, ['record' => $user->id])
        ->callAction(DeleteAction::class)
        ->assertNotified()
        ->assertRedirect();

    assertModelMissing($user);
});
```

### Testing Actions in Tables

```php
use Filament\Actions\Testing\TestAction;

it('can promote a user from table', function () {
    $user = User::factory()->create();
    
    livewire(ListUsers::class)
        ->callAction(TestAction::make('promote')->table($user), [
            'role' => 'admin',
        ])
        ->assertNotified();
});
```

## Common Mistakes to Avoid

### 1. File Visibility

Files are **private by default**. Always use `->visibility('public')` when public access is needed:

```php
// Good
FileUpload::make('avatar')
    ->directory('avatars')
    ->visibility('public')
    ->storeFileNamesIn('avatar_file_names'),

// Bad (defaults to private)
FileUpload::make('avatar')
    ->directory('avatars')
```

### 2. Grid/Section Column Spans

Grid, Section, and Fieldset do **not** span all columns by default. Explicitly set column spans:

```php
// Good
Grid::make(2)
    ->schema([
        Section::make('Personal')
            ->columnSpan(1)
            ->schema([...]),
        Section::make('Company')
            ->columnSpan(1)
            ->schema([...]),
    ]),

// Bad (may not align as expected)
Section::make('Personal')
    ->schema([...])
```

### 3. Incorrect Action Namespaces

Always use `Filament\Actions\` for actions:

```php
// Good
use Filament\Actions\DeleteAction;

// Bad
use Filament\Tables\Actions\DeleteAction;  // Wrong namespace
use Filament\Forms\Actions\DeleteAction;   // Wrong namespace
```

## Form Validation Example

```php
public function getFormSchema(): array
{
    return [
        TextInput::make('name')
            ->required()
            ->maxLength(255)
            ->label('Full Name'),

        TextInput::make('email')
            ->email()
            ->required()
            ->unique(User::class, 'email'),

        Select::make('role')
            ->options(RoleEnum::class)
            ->required()
            ->default(RoleEnum::User),

        TextInput::make('password')
            ->password()
            ->required()
            ->minLength(8)
            ->confirmed(),

        TextInput::make('password_confirmation')
            ->password()
            ->required(),
    ];
}
```

## Relationship Management

```php
// One-to-Many
public function orders(): HasMany
{
    return $this->hasMany(Order::class);
}

// Many-to-Many
public function roles(): BelongsToMany
{
    return $this->belongsToMany(Role::class);
}

// Testing relationships in Filament tables
livewire(ListOrders::class, ['record' => $user->id])
    ->assertCanSeeTableRecords($user->orders)
```

## Documentation

For detailed information, visit [filamentphp.com/docs](https://filamentphp.com/docs) or use the `search-docs` tool with queries like:
- "resource form validation"
- "table actions"
- "relation managers"
- "testing livewire"

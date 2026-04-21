# Testing Guide - Pest v4

Complete guide for writing tests with Pest v4 in this Laravel application.

## Overview

This project uses **Pest v4** for testing. Pest is a testing framework built on PHPUnit with a more elegant syntax.

## Creating Tests

### Feature Test (Default)

```bash
php artisan make:test --pest FeatureTestName
```

### Unit Test

```bash
php artisan make:test --unit --pest UnitTestName
```

## Running Tests

### Run All Tests

```bash
php artisan test --compact
```

### Run Specific Test

```bash
php artisan test --compact --filter=testName
```

### Run With Coverage

```bash
php artisan test --coverage
```

## Basic Test Structure

### Feature Test Example

```php
<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('UserController', function () {
    it('can list all users', function () {
        $users = User::factory(3)->create();
        
        $response = $this->actingAs($users->first())
            ->get(route('users.index'));
        
        $response
            ->assertOk()
            ->assertViewHas('users');
    });

    it('can create a user', function () {
        $data = User::factory()->make()->toArray();
        
        $response = $this->post(route('users.store'), $data);
        
        $response->assertRedirect();
        assertDatabaseHas(User::class, $data);
    });
});
```

## Common Assertion Methods

### Response Assertions

```php
$response
    ->assertOk()                              // HTTP 200
    ->assertStatus(201)                       // Specific status
    ->assertRedirect(route('dashboard'))      // Redirect assertion
    ->assertViewHas('users')                  // View data
    ->assertJson(['status' => 'success'])     // JSON response
    ->assertNoContent()                       // HTTP 204
```

### Database Assertions

```php
// Check record exists
assertDatabaseHas(User::class, [
    'email' => 'john@example.com',
]);

// Check record doesn't exist
assertDatabaseMissing(User::class, [
    'email' => 'old@example.com',
]);

// Check model is missing
assertModelMissing($user);
```

### Model Assertions

```php
expect($user->name)->toBe('John');
expect($user->email)->toContain('@example.com');
expect($user->isAdmin)->toBeTrue();
expect($user->orders)->toHaveCount(3);
```

## Testing Patterns

### Using Factories

```php
use function Pest\Laravel\{actingAs, post};

it('can create a post', function () {
    $user = User::factory()->create();
    
    actingAs($user)
        ->post(route('posts.store'), [
            'title' => 'My Post',
            'content' => 'Post content',
        ])
        ->assertRedirect();
});
```

### Testing With RefreshDatabase

```php
uses(RefreshDatabase::class);

it('truncates database after each test', function () {
    $user = User::factory()->create();
    expect(User::count())->toBe(1);
})->repeat(2);  // Runs twice, database is fresh each time
```

### Testing Authentication

```php
it('requires authentication', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

it('allows authenticated users', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertOk();
});
```

### Testing Authorization

```php
it('denies unauthorized access', function () {
    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();
    
    $this->actingAs($user)
        ->delete(route('users.destroy', $admin))
        ->assertForbidden();
});

it('allows authorized access', function () {
    $admin = User::factory()->admin()->create();
    
    $this->actingAs($admin)
        ->delete(route('users.destroy', User::factory()->create()))
        ->assertNoContent();
});
```

## Livewire Component Testing

### Basic Livewire Test

```php
use function Pest\Livewire\livewire;

it('renders the counter component', function () {
    livewire(Counter::class)
        ->assertSee('Count: 0')
        ->call('increment')
        ->assertSee('Count: 1');
});
```

### Testing Form Components

```php
livewire(CreateUserForm::class)
    ->fillForm([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
    ])
    ->call('submit')
    ->assertDispatchedBrowserEvent('user-created');
```

### Testing With Validation Errors

```php
livewire(CreateUserForm::class)
    ->fillForm([
        'name' => '',  // Required field empty
        'email' => 'invalid',  // Invalid email
    ])
    ->call('submit')
    ->assertHasFormErrors([
        'name' => 'required',
        'email' => 'email',
    ]);
```

### Testing Modal Actions

```php
livewire(UserTable::class)
    ->assertCanSeeTableRecords($users)
    ->clickTableAction('edit', $users->first())
    ->assertFormSet(['name' => $users->first()->name]);
```

## Filament Testing

### List Resource Test

```php
use App\Filament\Resources\UserResource\Pages\ListUsers;
use function Pest\Livewire\livewire;

it('can list users', function () {
    $users = User::factory(3)->create();
    
    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->searchTable($users->first()->name)
        ->assertCanSeeTableRecords($users->take(1));
});
```

### Create Resource Test

```php
use App\Filament\Resources\UserResource\Pages\CreateUser;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

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

### Edit Resource Test

```php
use App\Filament\Resources\UserResource\Pages\EditUser;
use function Pest\Livewire\livewire;

it('can update a user', function () {
    $user = User::factory()->create();
    
    livewire(EditUser::class, ['record' => $user->id])
        ->fillForm([
            'name' => 'Updated Name',
        ])
        ->call('save')
        ->assertNotified();

    expect($user->refresh()->name)->toBe('Updated Name');
});
```

### Action Testing

```php
use Filament\Actions\DeleteAction;
use function Pest\Livewire\livewire;

it('can delete a user', function () {
    $user = User::factory()->create();
    
    livewire(EditUser::class, ['record' => $user->id])
        ->callAction(DeleteAction::class)
        ->assertNotified()
        ->assertRedirect();

    assertModelMissing($user);
});
```

## Datasets

Test the same functionality with different inputs:

```php
it('validates email format', function ($email) {
    $response = $this->post(route('users.store'), [
        'email' => $email,
    ]);
    
    $response->assertHasFormErrors('email');
})->with([
    'invalid',
    'test@',
    '@example.com',
    'test..@example.com',
]);
```

## Mocking

### Mock External Services

```php
use Illuminate\Support\Facades\Http;

it('calls external API', function () {
    Http::fake([
        'api.example.com/*' => Http::response(['status' => 'ok']),
    ]);
    
    $result = ExternalService::call();
    
    expect($result)->toBeArray();
    Http::assertSent(fn ($request) => 
        $request->url() === 'https://api.example.com/endpoint'
    );
});
```

### Mock Models

```php
use Mockery;

it('handles model relations', function () {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('orders')->andReturn([]);
    
    expect($user->orders())->toBeArray();
});
```

## Best Practices

1. **Use Factories**: Always use factories for creating test data
   ```php
   $user = User::factory()->create();
   ```

2. **Test Behavior, Not Implementation**: Focus on what users see/do
   ```php
   // Good
   $this->post(route('posts.store'), [...])
       ->assertRedirect(route('posts.index'));
   
   // Bad
   expect(Post::create([...]))->toBeInstanceOf(Post::class);
   ```

3. **One Assertion Per Test** (or grouped logically):
   ```php
   it('creates a post and notifies user', function () {
       $response = $this->post(route('posts.store'), [...]);
       
       $response->assertRedirect();
       assertDatabaseHas(Post::class, [...]);
   });
   ```

4. **Use Descriptive Test Names**:
   ```php
   // Good
   it('prevents users from deleting other users posts', function () {
   
   // Bad
   it('test delete', function () {
   ```

5. **Seed Test Data Consistently**:
   ```php
   describe('Admin Panel', function () {
       beforeEach(fn () => $this->admin = User::factory()->admin()->create());
       
       it('shows user count', function () {
           // Uses $this->admin
       });
   });
   ```

## Test Directory Structure

```
tests/
├── Feature/
│   ├── UserControllerTest.php
│   ├── PostControllerTest.php
│   └── Api/
│       └── UserEndpointTest.php
├── Unit/
│   ├── UserModelTest.php
│   └── Helpers/
│       └── DateHelperTest.php
└── Pest.php  // Global test setup
```

## Disabling Tests

```php
it('will be implemented later', function () {
    pending();
});

it('is not ready', function () {
    skip();
});
```

## Documentation & Resources

- Use `search-docs` tool for version-specific Pest documentation
- Check existing tests in `tests/` directory for patterns
- Visit [pestphp.com](https://pestphp.com) for detailed docs

## Important Notes

- **Do NOT delete tests** without explicit approval
- **Use RefreshDatabase trait** to ensure test isolation
- **Tests are more important than verification scripts** - write tests instead of tinker
- **Run tests frequently** to catch regressions early

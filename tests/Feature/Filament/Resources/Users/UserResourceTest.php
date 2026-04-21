<?php

namespace Tests\Feature\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use function Pest\Livewire\livewire;
use function Pest\Laravel\actingAs;

// MARK: - Page Rendering Tests

it('can render list users page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    actingAs($admin);

    livewire(ListUsers::class)
        ->assertSuccessful();
});

it('can render create user page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    actingAs($admin);

    livewire(CreateUser::class)
        ->assertSuccessful();
});

it('can render edit user page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getKey()])
        ->assertSuccessful();
});

// MARK: - Create Operation Tests

it('can create a user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'status' => 'active',
            'role' => 'user',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    expect(User::where('email', 'john@example.com')->first())
        ->not->toBeNull()
        ->name->toBe('John Doe')
        ->status->toBe('active')
        ->role->toBe('user');
});

it('validates required fields on create', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'John Doe',
            'email' => '',
            'password' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['email', 'password']);
});

it('validates email format on create', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
        ])
        ->call('create')
        ->assertHasFormErrors(['email']);
});

it('validates unique email on create', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);
    actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
        ])
        ->call('create')
        ->assertHasFormErrors(['email']);
});

// MARK: - Update Operation Tests

it('can update a user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create([
        'name' => 'Old Name',
        'status' => 'active',
        'role' => 'user',
    ]);
    actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getKey()])
        ->fillForm([
            'name' => 'New Name',
            'status' => 'inactive',
            'role' => 'editor',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    expect($user->fresh())
        ->name->toBe('New Name')
        ->status->toBe('inactive')
        ->role->toBe('editor');
});

it('can update user password', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $oldPassword = $user->password;
    actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getKey()])
        ->fillForm([
            'password' => 'newpassword123',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->password)
        ->not->toBe($oldPassword);
});

it('validates email uniqueness on update', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com']);
    actingAs($admin);

    livewire(EditUser::class, ['record' => $user1->getKey()])
        ->fillForm([
            'email' => 'user2@example.com',
        ])
        ->call('save')
        ->assertHasFormErrors(['email']);
});

it('allows keeping same email on update', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['email' => 'test@example.com']);
    actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getKey()])
        ->fillForm([
            'email' => 'test@example.com',
            'name' => 'Updated Name',
        ])
        ->call('save')
        ->assertHasNoFormErrors();
});

it('can update without changing password', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $originalPassword = $user->password;
    actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getKey()])
        ->fillForm([
            'name' => 'Updated Name',
            'password' => '',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh())
        ->name->toBe('Updated Name')
        ->password->toBe($originalPassword);
});

// MARK: - Delete Operation Tests

it('can delete a user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getKey()])
        ->callAction('delete')
        ->assertHasNoErrors();

    expect(User::find($user->id))->toBeNull();
});

// MARK: - Redirect Tests

it('redirects after creating', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
        ])
        ->call('create')
        ->assertRedirect();
});

it('redirects after updating', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getKey()])
        ->fillForm(['name' => 'Updated Name'])
        ->call('save')
        ->assertRedirect();
});

it('redirects after deleting', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getKey()])
        ->callAction('delete')
        ->assertRedirect();
});

// MARK: - Form Field Tests

it('has personal tab fields in form', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    actingAs($admin);

    livewire(CreateUser::class)
        ->assertFormFieldExists('name')
        ->assertFormFieldExists('email');
});

it('has security tab fields in form', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    actingAs($admin);

    livewire(CreateUser::class)
        ->assertFormFieldExists('password')
        ->assertFormFieldExists('status')
        ->assertFormFieldExists('role');
});

it('has verification tab fields in form', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    actingAs($admin);

    livewire(CreateUser::class)
        ->assertFormFieldExists('email_verified_at');
});

it('has two-factor tab fields in form', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    actingAs($admin);

    livewire(CreateUser::class)
        ->assertFormFieldExists('two_factor_confirmed_at')
        ->assertFormFieldExists('two_factor_secret');
});

// MARK: - Database Tests

it('creates user with correct defaults', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ])
        ->call('create');

    $user = User::where('email', 'test@example.com')->first();
    expect($user)
        ->not->toBeNull()
        ->name->toBe('Test User')
        ->email->toBe('test@example.com')
        ->status->toBe('active')
        ->role->toBe('user');
});

it('hashes password on create', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'secretpassword',
        ])
        ->call('create');

    $user = User::where('email', 'test@example.com')->first();
    expect($user->password)->not->toBe('secretpassword');
});

it('updates multiple fields', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create([
        'name' => 'Old Name',
        'role' => 'user',
        'status' => 'active',
    ]);
    actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getKey()])
        ->fillForm([
            'name' => 'New Name',
            'role' => 'admin',
            'status' => 'inactive',
        ])
        ->call('save');

    expect($user->fresh())
        ->name->toBe('New Name')
        ->role->toBe('admin')
        ->status->toBe('inactive');
});

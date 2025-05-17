<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// ✅ Test 1
it('can render page login page', function () {
    auth()->logout();

    $this
        ->get('/admin/login')
        ->assertSuccessful();
})->group('feature');

// ✅ Test 3
test('users can not authenticate with invalid password', function () {
    auth()->logout();

    $user = User::factory()->create();

    $this->post('/login', [
        'email'    => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

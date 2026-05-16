<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertOk()
            ->assertSeeText('Create Your Access')
            ->assertSeeText('Request Access');
    }

    public function test_guest_can_register_and_is_marked_pending_approval(): void
    {
        $response = $this->post('/register', [
            'email' => 'test@example.com',
            'whatsapp' => '081234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));
        $response->assertSessionHas('status', 'Registrasi berhasil. Akun Anda sedang menunggu persetujuan admin.');

        $user = User::query()->where('email', 'test@example.com')->firstOrFail();

        $this->assertSame('test', $user->name);
        $this->assertSame('081234567890', $user->whatsapp);
        $this->assertNull($user->approved_at);
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->hasRole('user'));
    }

    public function test_whatsapp_is_required_for_registration(): void
    {
        $response = $this->from('/register')->post('/register', [
            'email' => 'test@example.com',
            'whatsapp' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('whatsapp');
    }

    public function test_whatsapp_must_be_unique_for_registration(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
            'whatsapp' => '081234567890',
        ]);

        $response = $this->from('/register')->post('/register', [
            'email' => 'test@example.com',
            'whatsapp' => '081234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('whatsapp');
    }

    public function test_email_must_be_unique_for_registration(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->from('/register')->post('/register', [
            'email' => 'existing@example.com',
            'whatsapp' => '081234567891',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');
    }
}

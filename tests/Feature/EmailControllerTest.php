<?php

namespace Tests\Feature;

use App\Models\Email;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Mockery;

class EmailControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->emailService = Mockery::mock(EmailService::class);
        $this->app->instance(EmailService::class, $this->emailService);
    }

    public function test_user_can_get_their_emails()
    {
        $user = User::factory()->create();
        $emails = Email::factory()->count(3)->create(['user_id' => $user->id]);
        
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/emails');

        $response->assertOk()
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'email', 'user_id', 'created_at', 'updated_at']
            ]);
    }

    public function test_user_can_create_email()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $emailData = [
            'email' => 'test@example.com'
        ];

        $response = $this->postJson('/api/emails', $emailData);

        $response->assertStatus(201)
            ->assertJson([
                'email' => 'test@example.com',
                'user_id' => $user->id
            ]);

        $this->assertDatabaseHas('emails', [
            'email' => 'test@example.com',
            'user_id' => $user->id
        ]);
    }

    public function test_user_cannot_create_duplicate_email()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Email::factory()->create([
            'email' => 'test@example.com',
            'user_id' => $user->id
        ]);

        $response = $this->postJson('/api/emails', [
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_view_their_email()
    {
        $user = User::factory()->create();
        $email = Email::factory()->create(['user_id' => $user->id]);
        
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/emails/{$email->id}");

        $response->assertOk()
            ->assertJson([
                'id' => $email->id,
                'email' => $email->email,
                'user_id' => $user->id
            ]);
    }

    public function test_user_cannot_view_others_email()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $email = Email::factory()->create(['user_id' => $otherUser->id]);
        
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/emails/{$email->id}");

        $response->assertForbidden();
    }

    public function test_user_can_delete_their_email()
    {
        $user = User::factory()->create();
        $email = Email::factory()->create(['user_id' => $user->id]);
        
        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/emails/{$email->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('emails', ['id' => $email->id]);
    }

    public function test_user_cannot_delete_others_email()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $email = Email::factory()->create(['user_id' => $otherUser->id]);
        
        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/emails/{$email->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('emails', ['id' => $email->id]);
    }

    public function test_user_can_send_welcome_emails()
    {
        $user = User::factory()->create();
        Email::factory()->count(3)->create(['user_id' => $user->id]);
        
        Sanctum::actingAs($user);

        $this->emailService
            ->shouldReceive('sendWelcomeEmails')
            ->once()
            ->with($user);

        $response = $this->postJson('/api/emails/send-welcome');

        $response->assertOk()
            ->assertJson(['message' => 'Welcome emails sent successfully']);
    }

    public function test_unauthenticated_user_cannot_access_emails()
    {
        $response = $this->getJson('/api/emails');
        $response->assertUnauthorized();

        $response = $this->postJson('/api/emails', ['email' => 'test@example.com']);
        $response->assertUnauthorized();

        $response = $this->getJson('/api/emails/1');
        $response->assertUnauthorized();

        $response = $this->deleteJson('/api/emails/1');
        $response->assertUnauthorized();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
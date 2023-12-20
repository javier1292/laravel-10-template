<?php

use Database\Factories\UserFactory;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_login_with_valid_credentials()
    {
        $password = $this->faker->password;
        $user = UserFactory::new()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'password' => bcrypt($password),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('api/v1/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message',
            'token',
        ]);
    }

    public function test_login_with_invalid_credentials()
    {
        $response = $this->postJson('api/v1/login', [
            'email' => $this->faker->email,
            'password' => $this->faker->password,
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson([
            'message' => 'Invalid credentials',
        ]);
    }

    public function test_login_with_inactive_user()
    {
        $password = $this->faker->password;
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'password' => bcrypt($password),
            'status' => 'inactive',
        ]);

        $response = $this->postJson('api/v1/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_register_returns_201_with_valid_data()
    {
        $email = $this->faker->safeEmail;
        $password = $this->faker->password;

        $this->seed(RolesAndPermissionsSeeder::class);

        $data = [
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
            'device_name' => 'Test Device',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'user_type' => 'administrator',
        ];

        $response = $this->postJson('api/v1/register', $data);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    public function test_logout_deletes_token_and_returns_success_response()
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = UserFactory::new()->create();

        $token = $user->createToken('Test Device')->plainTextToken;
        Sanctum::actingAs($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('api/v1/logout');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(0, PersonalAccessToken::all());
    }

    public function test_change_password_with_valid_data()
    {
        $user = UserFactory::new()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('api/v1/profile/password', [
            'current_password' => 'password',
            'new_password' => 'new-password',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'Password changed successfully',
        ]);
    }
}

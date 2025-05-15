<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Создаем тестового пользователя
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        // Получаем токен для авторизации
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $this->token = $response->json('access_token');
    }

    /** @test */
    public function user_can_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email'
                ],
                'access_token'
            ]);
    }

    /** @test */
    public function user_can_login()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type'
            ]);
    }

    /** @test */
    public function user_can_get_profile()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email'
            ]);
    }

    /** @test */
    public function user_can_create_category()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/categories', [
                'name' => 'Test Category',
                'icon' => 'shopping-cart'
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'icon'
            ]);
    }

    /** @test */
    public function user_can_create_expense()
    {
        $category = Category::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/expenses', [
                'amount' => 100.50,
                'category_id' => $category->id,
                'description' => 'Test expense'
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'amount',
                'category_id',
                'description'
            ]);
    }

    /** @test */
    public function user_can_create_budget()
    {
        $category = Category::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/budgets', [
                'amount' => 1000.00,
                'category_id' => $category->id,
                'period' => 'month'
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'amount',
                'category_id',
                'period'
            ]);
    }

    /** @test */
    public function user_can_create_group()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/groups', [
                'name' => 'Test Group',
                'budget' => 5000.00
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'budget'
            ]);
    }

    /** @test */
    public function user_can_invite_to_group()
    {
        $group = Group::factory()->create(['owner_id' => $this->user->id]);
        $newUser = User::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson("/api/groups/{$group->id}/invite", [
                'email' => $newUser->email
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function user_can_get_analytics()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/analytics/by-category?period=month');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'category' => [
                        'id',
                        'name'
                    ],
                    'total'
                ]
            ]);
    }

    /** @test */
    public function user_can_export_analytics()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->get('/api/export/analytics?period=month');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }
} 
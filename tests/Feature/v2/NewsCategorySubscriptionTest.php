<?php

namespace Tests\Feature\v2;

use App\Models\NewsCategory;
use App\Models\NewsCategorySubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsCategorySubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();

        $this->token = $user->createToken('test')->plainTextToken;
    }

    public function test_get_subscriptions_unauthenticated()
    {
        $this->getJson(
            route('api.news-category-subscriptions.index'),
            ['Api-Version' => 2]
        )->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_get_subscriptions_by_limit()
    {
        $limit = 10;

        NewsCategorySubscription::factory($limit + 5)->create();

        $this->getJson(
            route('api.news-category-subscriptions.index', [
                'limit' => $limit,
            ]), [
                'Authorization' => "Bearer $this->token",
                'Api-Version' => 2,
            ]
        )->assertJsonStructure([
            'data' => [
                '*' => ['id', 'user_email', 'news_category_id'],
            ]
        ])->assertJsonCount($limit, 'data');
    }

    public function test_get_subscriptions_by_email()
    {
        $count = 10;
        $user = User::factory()->create();

        NewsCategorySubscription::factory(10, [
            'user_email' => $user->email,
        ])->create();

        $this->getJson(
            route('api.news-category-subscriptions.index', [
                'user_email' => $user->email,
            ]), [
                'Authorization' => "Bearer $this->token",
                'Api-Version' => 2,
            ]
        )->assertJsonStructure([
            'data' => [
                '*' => ['id', 'user_email', 'news_category_id'],
            ]
        ])->assertJsonCount($count, 'data');
    }

    public function test_get_subscriptions_by_category()
    {
        $count = 10;
        $category = NewsCategory::factory()->has(
            NewsCategorySubscription::factory()->count($count),
            'subscriptions'
        )->create();

        $this->getJson(
            route('api.news-category-subscriptions.index', [
                'news_category_id' => $category->id,
            ]), [
                'Authorization' => "Bearer $this->token",
                'Api-Version' => 2,
            ]
        )->assertJsonStructure([
            'data' => [
                '*' => ['id', 'user_email', 'news_category_id'],
            ]
        ])->assertJsonCount($count, 'data');
    }

    public function test_get_subscriptions_invalid()
    {
        $this->getJson(
            route('api.news-category-subscriptions.index', [
                'user_email' => 'invalid',
                'news_category_id' => 'invalid',
                'offset' => 'invalid',
            ]), [
                'Authorization' => "Bearer $this->token",
                'Api-Version' => 2,
            ]
        )->assertJsonValidationErrors([
            'user_email',
            'news_category_id',
            'offset',
        ]);
    }

    public function test_create_subscription()
    {
        $user = User::factory()->create();
        $category = NewsCategory::factory()->create();

        $this->postJson(
            route('api.news-category-subscriptions.store'),
            [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'news_category_id' => $category->id,
            ],
            ['Api-Version' => 2]
        )->assertCreated()->assertJsonStructure(['unsubscription_key']);

        $this->assertDatabaseHas('news_category_subscriptions', [
            'user_name' => $user->name,
            'user_email' => $user->email,
            'news_category_id' => $category->id,
        ]);
    }

    public function test_create_subscription_invalid()
    {
        $this->postJson(
            route('api.news-category-subscriptions.store'),
            [
                'user_email' => 'invalid',
                'news_category_id' => 'invalid',
            ],
            ['Api-Version' => 2]
        )->assertJsonValidationErrors([
            'user_name',
            'user_email',
            'news_category_id',
        ]);
    }

    public function test_delete_subscription()
    {
        $subscription = NewsCategorySubscription::factory()->create();

        $this->deleteJson(
            route(
                'api.news-category-subscriptions.destroy',
                $subscription,
            ),
            ['unsubscription_key' => $subscription['unsubscription_key']],
            ['Api-Version' => 2]
        )->assertNoContent();

        $this->assertModelMissing($subscription);
    }

    public function test_delete_subscription_invalid()
    {
        $this->deleteJson(
            route('api.news-category-subscriptions.destroy', 'invalid'),
            ['unsubscription_key' => 'invalid'],
            ['Api-Version' => 2]
        )->assertNotFound();
    }

    public function test_delete_multiple_subscriptions()
    {
        NewsCategorySubscription::factory(10, [
            'user_email' => 'test@test.com',
        ])->create();

        $this->deleteJson(
            route('api.news-category-subscriptions.destroy-all'),
            ['user_email' => 'test@test.com'],
            ['Api-Version' => 2]
        )->assertNoContent();

        $this->assertDatabaseMissing('news_category_subscriptions', [
            'user_email' => 'test@test.com',
        ]);
    }

    public function test_delete_multiple_subscriptions_invalid()
    {
        $this->deleteJson(
            route('api.news-category-subscriptions.destroy-all'),
            ['user_email' => 'invalid@invalid.com'],
            ['Api-Version' => 2],
        )->assertJsonValidationErrors(['user_email']);
    }
}

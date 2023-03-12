<?php

namespace Tests\Feature;

use App\Models\NewsCategory;
use App\Models\NewsCategorySubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsCategorySubscriptionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscriptions_index()
    {
        $this->seed();

        $user = User::first();
        $token = $user->createToken('test')->plainTextToken;
        $category = NewsCategory::first();

        $subscriptionsByLimit = NewsCategorySubscription::limit(15)->get();
        $subscriptionsByEmail = NewsCategorySubscription::where([
            'user_email' => $user->email,
        ])->get();
        $subscriptionsByCategory = NewsCategorySubscription::where([
            'news_category_id' => $category->id,
        ])->get();

        $this->getJson(
            route('api.news-category-subscriptions.index'),
        )->assertJsonPath('message', 'Unauthenticated.');

        $this->getJson(
            route('api.news-category-subscriptions.index', [
                'limit' => $subscriptionsByLimit->count(),
            ]),
            ['Authorization' => "Bearer $token"],
        )->assertJsonStructure([
            'data' => [
                '*' => ['id', 'user_email', 'news_category_id'],
            ],
        ])->assertJsonCount($subscriptionsByLimit->count(), 'data');

        $this->getJson(
            route('api.news-category-subscriptions.index', [
                'user_email' => $user->email,
            ]),
            ['Authorization' => "Bearer $token"],
        )->assertJsonStructure([
            'data' => [
                '*' => ['id', 'user_email', 'news_category_id'],
            ],
        ])->assertJsonCount($subscriptionsByEmail->count(), 'data');

        $this->getJson(
            route('api.news-category-subscriptions.index', [
                'news_category_id' => $category->id,
            ]),
            ['Authorization' => "Bearer $token"],
        )->assertJsonStructure([
            'data' => [
                '*' => ['id', 'user_email', 'news_category_id'],
            ],
        ])->assertJsonCount($subscriptionsByCategory->count(), 'data');

        $this->getJson(
            route('api.news-category-subscriptions.index', [
                'user_email' => 'invalid',
                'news_category_id' => 'invalid',
                'offset' => 'invalid',
            ]),
            ['Authorization' => "Bearer $token"],
        )->assertJsonValidationErrors([
            'user_email',
            'news_category_id',
            'offset',
        ]);
    }

    public function test_subscriptions_store()
    {
        $users = User::factory(10)->create();
        $categories = NewsCategory::factory(10)->create();

        $this->postJson(
            route('api.news-category-subscriptions.store'),
            [
                'user_email' => $users[0]->email,
                'news_category_id' => $categories[0]->id,
            ],
        )->assertCreated();

        $this->postJson(
            route('api.news-category-subscriptions.store'),
            [
                'user_name' => $users[1]->name,
                'user_email' => $users[1]->email,
                'news_category_id' => $categories[1]->id,
            ],
            ['Api-Version' => 2],
        )->assertCreated()->assertJsonStructure(['unsubscription_key']);

        $this->postJson(
            route('api.news-category-subscriptions.store'),
            [
                'user_email' => $users[1]->email,
                'news_category_id' => $categories[1]->id,
            ],
            ['Api-Version' => 2],
        )->assertJsonValidationErrors(['user_name', 'user_email']);

        $this->assertDatabaseHas('news_category_subscriptions', [
            'user_email' => $users[0]->email,
            'news_category_id' => $categories[0]->id,
        ]);
        $this->assertDatabaseHas('news_category_subscriptions', [
            'user_name' => $users[1]->name,
            'user_email' => $users[1]->email,
            'news_category_id' => $categories[1]->id,
        ]);
    }

    public function test_subscriptions_destroy()
    {
        $subscriptions = NewsCategorySubscription::factory(10)->create();

        $this->deleteJson(
            route(
                'api.news-category-subscriptions.destroy',
                $subscriptions[0],
            ),
        )->assertNoContent();

        $this->deleteJson(
            route(
                'api.news-category-subscriptions.destroy',
                $subscriptions[1],
            ),
            ['unsubscription_key' => $subscriptions[1]['unsubscription_key']],
            ['Api-Version' => 2],
        )->assertNoContent();

        $this->deleteJson(
            route(
                'api.news-category-subscriptions.destroy',
                $subscriptions[2],
            ),
            ['unsubscription_key' => 'invalid'],
            ['Api-Version' => 2],
        )->assertJsonValidationErrors(['unsubscription_key']);

        $this->deleteJson(
            route('api.news-category-subscriptions.destroy', 'invalid'),
        )->assertNotFound();

        $this->assertModelMissing($subscriptions[0]);
        $this->assertModelMissing($subscriptions[1]);
    }

    public function test_subscriptions_destroy_all()
    {
        NewsCategorySubscription::factory(10, [
            'user_email' => 'test@test.com',
        ])->create();

        $this->deleteJson(
            route('api.news-category-subscriptions.destroy-all'),
            ['user_email' => 'test@test.com'],
        )->assertNoContent();

        $this->deleteJson(
            route('api.news-category-subscriptions.destroy-all'),
            ['user_email' => 'invalid@invalid.com'],
        )->assertJsonValidationErrors(['user_email']);

        $this->assertDatabaseMissing('news_category_subscriptions', [
            'user_email' => 'test@test.com',
        ]);
    }
}

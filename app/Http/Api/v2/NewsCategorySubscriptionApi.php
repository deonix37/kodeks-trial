<?php

namespace App\Http\Api\v2;

use App\Http\Api\NewsCategorySubscriptionApiInterface;
use App\Models\NewsCategorySubscription;
use Illuminate\Support\Str;

class NewsCategorySubscriptionApi implements NewsCategorySubscriptionApiInterface
{
    public function index($request)
    {
        $data = $request->validated();

        $result = NewsCategorySubscription::when(
            $data['news_category_id'] ?? false,
            fn ($query, $id) => $query->where('news_category_id', $id),
        )->when(
            $data['user_email'] ?? false,
            fn ($query, $email) => $query->where('user_email', $email),
        )->when(
            $data['limit'] ?? false,
            fn ($query, $limit) => $query->limit($limit),
        )->when(
            $data['offset'] ?? false,
            fn ($query, $offset) => $query->offset($offset),
        )->get();

        return response()->preferredFormat([
            'data' => $result->toArray(),
        ]);
    }

    public function store($request)
    {
        $data = $request->validated();

        $subscription = NewsCategorySubscription::create(
            array_merge($data, ['unsubscription_key' => Str::random()])
        );

        return response()->preferredFormat($subscription, 201);
    }

    public function destroy($request, $newsCategorySubscription)
    {
        $newsCategorySubscription->delete();

        return response()->noContent();
    }

    public function destroyAll($request)
    {
        $data = $request->validated();

        NewsCategorySubscription::where(
            'user_email',
            $data['user_email'],
        )->delete();

        return response()->noContent();
    }
}

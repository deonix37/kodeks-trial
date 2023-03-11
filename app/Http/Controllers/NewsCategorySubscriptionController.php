<?php

namespace App\Http\Controllers;

use App\Models\NewsCategorySubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class NewsCategorySubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['index']);
    }

    public function index(Request $request)
    {
        try {
            $data = $request->validate([
                'news_category_id' => ['nullable', 'int'],
                'user_email' => ['nullable', 'email'],
                'limit' => ['nullable', 'int'],
                'offset' => ['nullable', 'int', 'prohibited_if:limit,null'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->validator->errors(),
            ], 400);
        }

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

        return response()->json([
            'data' => $result->toArray(),
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'news_category_id' => [
                'required',
                'exists:news_categories,id',
            ],
            'user_email' => [
                'required',
                'email',
                Rule::unique('news_category_subscriptions')->where(
                    fn ($query) => $query->where([
                        'user_email' => $request->user_email,
                        'news_category_id' => $request->news_category_id,
                    ]),
                ),
            ],
        ];

        try {
            $data = $request->validate($rules, [
                'user_email.unique' => 'Subscription already exists.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->validator->errors(),
            ], 400);
        }

        $subscription = NewsCategorySubscription::create($data);

        return response()->json($subscription, 201);
    }

    public function destroy(NewsCategorySubscription $newsCategorySubscription)
    {
        $newsCategorySubscription->delete();

        return response()->noContent();
    }

    public function destroyAll(Request $request)
    {
        try {
            $data = $request->validate([
                'user_email' => [
                    'required',
                    'exists:news_category_subscriptions',
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->validator->errors(),
            ], 400);
        }

        NewsCategorySubscription::where(
            'user_email',
            $data['user_email'],
        )->delete();

        return response()->noContent();
    }
}

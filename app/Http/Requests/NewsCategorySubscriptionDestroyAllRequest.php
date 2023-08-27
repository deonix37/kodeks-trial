<?php

namespace App\Http\Requests;

class NewsCategorySubscriptionDestroyAllRequest extends ApiRequest
{
    protected function rules_v1()
    {
        return [
            'user_email' => [
                'required',
                'exists:news_category_subscriptions',
            ],
        ];
    }

    protected function rules_v2()
    {
        return [
            'user_email' => [
                'required',
                'exists:news_category_subscriptions',
            ],
        ];
    }
}

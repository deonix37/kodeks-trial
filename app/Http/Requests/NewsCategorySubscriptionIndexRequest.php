<?php

namespace App\Http\Requests;

class NewsCategorySubscriptionIndexRequest extends ApiRequest
{
    protected function rules_v1()
    {
        return [
            'news_category_id' => ['nullable', 'int'],
            'user_email' => ['nullable', 'email'],
            'limit' => ['nullable', 'int'],
            'offset' => ['nullable', 'int', 'prohibited_if:limit,null'],
        ];
    }

    protected function rules_v2()
    {
        return [
            'news_category_id' => ['nullable', 'int'],
            'user_email' => ['nullable', 'email'],
            'limit' => ['nullable', 'int'],
            'offset' => ['nullable', 'int', 'prohibited_if:limit,null'],
        ];
    }
}

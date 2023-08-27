<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class NewsCategorySubscriptionDestroyRequest extends ApiRequest
{
    protected function rules_v1()
    {
        return [];
    }

    protected function rules_v2()
    {
        return [
            'unsubscription_key' => ['required', Rule::in([
                $this->news_category_subscription['unsubscription_key'],
            ])],
        ];
    }
}

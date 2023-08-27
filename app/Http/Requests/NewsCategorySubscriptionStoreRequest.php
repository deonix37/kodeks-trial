<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class NewsCategorySubscriptionStoreRequest extends ApiRequest
{
    protected function rules_v1()
    {
        return [
            'news_category_id' => [
                'required',
                'exists:news_categories,id',
            ],
            'user_email' => [
                'required',
                'email',
                Rule::unique('news_category_subscriptions')->where(
                    fn ($query) => $query->where([
                        'user_email' => $this->user_email,
                        'news_category_id' => $this->news_category_id,
                    ]),
                ),
            ],
        ];
    }

    protected function rules_v2()
    {
        return [
            'news_category_id' => [
                'required',
                'exists:news_categories,id',
            ],
            'user_email' => [
                'required',
                'email',
                Rule::unique('news_category_subscriptions')->where(
                    fn ($query) => $query->where([
                        'user_email' => $this->user_email,
                        'news_category_id' => $this->news_category_id,
                    ]),
                ),
            ],
            'user_name' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'user_email.unique' => 'Subscription already exists.',
        ];
    }
}

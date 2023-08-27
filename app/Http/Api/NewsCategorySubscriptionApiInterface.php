<?php

namespace App\Http\Api;

use App\Http\Requests\NewsCategorySubscriptionDestroyAllRequest;
use App\Http\Requests\NewsCategorySubscriptionDestroyRequest;
use App\Http\Requests\NewsCategorySubscriptionIndexRequest;
use App\Http\Requests\NewsCategorySubscriptionStoreRequest;
use App\Models\NewsCategorySubscription;

interface NewsCategorySubscriptionApiInterface
{
    public function index(NewsCategorySubscriptionIndexRequest $request);
    public function store(NewsCategorySubscriptionStoreRequest $request);
    public function destroy(
        NewsCategorySubscriptionDestroyRequest $request,
        NewsCategorySubscription $newsCategorySubscription
    );
    public function destroyAll(
        NewsCategorySubscriptionDestroyAllRequest $request
    );
}

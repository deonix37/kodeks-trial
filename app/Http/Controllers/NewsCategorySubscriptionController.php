<?php

namespace App\Http\Controllers;

use App\Http\Api\NewsCategorySubscriptionApiInterface;
use App\Http\Requests\NewsCategorySubscriptionDestroyAllRequest;
use App\Http\Requests\NewsCategorySubscriptionDestroyRequest;
use App\Http\Requests\NewsCategorySubscriptionIndexRequest;
use App\Http\Requests\NewsCategorySubscriptionStoreRequest;
use App\Models\NewsCategorySubscription;

class NewsCategorySubscriptionController extends Controller
{
    public function __construct(
        protected NewsCategorySubscriptionApiInterface $api
    )
    {
        $this->middleware('auth:sanctum')->only(['index']);
    }

    public function index(NewsCategorySubscriptionIndexRequest $request)
    {
        return $this->api->index($request);
    }

    public function store(NewsCategorySubscriptionStoreRequest $request)
    {
        return $this->api->store($request);
    }

    public function destroy(
        NewsCategorySubscriptionDestroyRequest $request,
        NewsCategorySubscription $newsCategorySubscription
    )
    {
        return $this->api->destroy($request, $newsCategorySubscription);
    }

    public function destroyAll(
        NewsCategorySubscriptionDestroyAllRequest $request
    )
    {
        return $this->api->destroyAll($request);
    }
}

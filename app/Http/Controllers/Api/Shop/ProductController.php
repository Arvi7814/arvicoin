<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Shop\ProductRequest;
use App\Http\Resources\Shop\ProductResource;
use App\Jobs\Shop\ProductViewed;
use App\Models\Shop\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(ProductRequest $request): AnonymousResourceCollection
    {
        $query = Product::query()
            ->with(['tags'])
            ->orderByViews();

        if ($tag = $request->tag) {
            $query->whereHasTags([(int)$tag]);
        }

        if ($search = $request->search) {
            $query->search($search);
        }

        return ProductResource::collection($query->paginate(100));
    }

    public function show(Product $product): JsonResource
    {
        if ($ip = request()->ip()) {
            ProductViewed::dispatch($product, $ip, Auth::user());
        }

        return ProductResource::make($product);
    }
}

<?php

namespace App\Observers\Shop;

use App\Models\Shop\Product;
use Illuminate\Support\Facades\Auth;

class ProductObserver
{
    public function creating(Product $product): void
    {
        if (!$product->isDirty('created_by')) {
            $product->created_by = Auth::id();
        }
    }

    public function deleted(Product $product): void
    {
        $product->cartProducts()->delete();
    }

    /**
     * Handle the Product "restored" event.
     *
     * @param Product $product
     * @return void
     */
    public function restored(Product $product)
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     *
     * @param Product $product
     * @return void
     */
    public function forceDeleted(Product $product)
    {
        //
    }
}

<?php

namespace App\Jobs\Shop;

use App\Models\Shop\Product;
use App\Models\Shop\ProductView;
use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProductViewed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        readonly private Product $product,
        readonly private string  $ip,
        readonly private ?User   $user
    )
    {
    }

    public function handle()
    {
        /** @var ProductView $productView */
        $productView = ProductView::firstOrCreate([
            'product_id' => $this->product->id,
            'ip_address' => $this->ip,
            'user_id' => $this->user?->id,
        ]);

        if ($productView->wasRecentlyCreated) {
            $this->product->viewed += 1;
            $this->product->save();
        }
    }
}

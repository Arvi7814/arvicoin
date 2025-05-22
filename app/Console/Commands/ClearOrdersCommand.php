<?php

namespace App\Console\Commands;

use App\Models\Chat\Chat;
use App\Models\Chat\ChatMember;
use App\Models\Chat\ChatMessage;
use App\Models\Chat\ChatUnreadMessage;
use App\Models\Order\Order;
use App\Models\Order\OrderProduct;
use DB;
use Illuminate\Console\Command;
use Schema;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ClearOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear orders and related chats';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('failed_jobs')->truncate();
        DB::table('jobs')->truncate();
        ChatMember::query()->truncate();
        ChatUnreadMessage::query()->truncate();
        ChatMessage::query()->truncate();
        Chat::query()->truncate();
        OrderProduct::query()->truncate();
        Order::query()->truncate();
        Schema::enableForeignKeyConstraints();

        $images = Media::query()
            ->whereIn('model_type', [
                ChatMessage::class,

            ])
            ->get();

        foreach ($images as $image) {
            $image->delete();
        }

        return 1;
    }
}

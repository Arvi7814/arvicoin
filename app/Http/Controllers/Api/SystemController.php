<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enum\SettingsEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\System\SettingResource;
use App\Models\System\Setting;
use Illuminate\Http\JsonResponse;

class SystemController extends Controller
{
    public function settings(): JsonResponse
    {
        return response()->json(
            SettingResource::collection(
                Setting::query()
                    ->whereIn('type', SettingsEnum::publicSettings())
                    ->get()
            )
        );
    }
}

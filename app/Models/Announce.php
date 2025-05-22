<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property string $title
 * @property string $content
 */
class Announce extends Model
{
    use HasTranslations;

    protected $guarded = [];

    /*** @var string[] $translatable */
    public array $translatable = ['title', 'content'];

    public function delete()
    {
        AnnounceSent::query()->where('announce_id', $this->id)->delete();

        parent::delete();
    }

    /**
     * @return HasMany<AnnounceSent>
     */
    public function users(): HasMany
    {
        return $this->hasMany(AnnounceSent::class, 'announce_id');
    }

    /**
     * @return HasMany<AnnounceSent>
     */
    public function sentUsers(): HasMany
    {
        return $this->users()->where('sent', true);
    }
}

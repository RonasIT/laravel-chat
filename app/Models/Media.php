<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RonasIT\Support\Traits\ModelTrait;

class Media extends Model
{
    use ModelTrait;

    protected $fillable = [
        'link',
        'name',
        'owner_id',
        'is_public',
        'mime',
        'type',
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $hidden = ['pivot'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

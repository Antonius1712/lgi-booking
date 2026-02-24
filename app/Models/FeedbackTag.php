<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackTag extends Model
{
    protected $fillable = [
        'label',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}

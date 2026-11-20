<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    protected $fillable = ['template_id', 'title', 'slug', 'order', 'description', 'is_global'];

    protected $casts = ['is_global' => 'boolean'];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}

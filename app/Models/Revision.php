<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Revision extends Model
{
    protected $fillable = [

        'document_id',

        'content',

        'user_id'

    ];

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }
}
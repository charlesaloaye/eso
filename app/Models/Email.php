<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    /** @use HasFactory<\Database\Factories\EmailFactory> */
    use HasFactory;

    protected $fillable = [
        'email',
        'subject',
        'body',
        'is_read',
        'sent_to',
        'scheduled_at',
    ];
}

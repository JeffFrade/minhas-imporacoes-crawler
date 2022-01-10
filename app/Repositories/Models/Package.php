<?php

namespace App\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'tracking_number',
        'status',
        'date'
    ];
}

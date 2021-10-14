<?php

namespace App\Models;

use App\Traits\PrimaryUuids;
use Illuminate\Database\Eloquent\Model;

class CoreModel extends Model
{
    use PrimaryUuids;
    public $incrementing = false;
}

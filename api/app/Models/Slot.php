<?php


namespace App\Models;


class Slot extends CoreModel
{
    const SMALL = 0;
    const MEDIUM = 1;
    const LARGE = 2;
    protected $fillable = [
        'entry_point_id',
        'size',
        'slot_no',
        'distance',
        'exceeding_hourly_rate',
        'is_vacant',
    ];
}

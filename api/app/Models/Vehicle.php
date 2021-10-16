<?php


namespace App\Models;


class Vehicle extends CoreModel
{
    const SMALL = 0;
    const MEDIUM = 1;
    const LARGE = 2;

    protected $fillable = [
        'plate_no',
        'type',
    ];
}

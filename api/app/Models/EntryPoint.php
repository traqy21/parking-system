<?php


namespace App\Models;


class EntryPoint extends CoreModel
{
    protected $fillable = [
        'name',
    ];

    public function vacantSlots(){
        return $this->hasMany(Slot::class)
            ->where('is_vacant', true)
            ->orderBy('distance');
    }

    public function slots(){
        return $this->hasMany(Slot::class)->orderBy('distance');
    }
}

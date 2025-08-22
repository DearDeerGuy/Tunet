<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
  protected $fillable = [
    'name',
    'description',
    'price',
    'duration_months',
    'image'
  ];

  public function users(): HasMany
  {
    return $this->hasMany(User::class);
  }

}

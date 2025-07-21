<?php

namespace Modules\Development\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DevelopmentModule extends Model
{
    use HasFactory;

    protected $table = 'development_modules';
    protected $fillable = ['name'];
}

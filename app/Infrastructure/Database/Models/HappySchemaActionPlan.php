<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HappySchemaActionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'happy_schema',
        'action_plan',
    ];
}

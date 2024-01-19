<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleVoucher extends Model
{
    use HasFactory;
    protected $table = TABLE_SCHEDULE;
    protected $primaryKey = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    protected $fillable = [
        'serie',
        'number',
        'docnumber',
        'status',
    ];
    
    protected $attributes = [
        'status' => 0
    ];
}

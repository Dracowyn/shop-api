<?php
/**
 * 物流公司模型
 * @author Dracowyn
 * @since 2024-01-03 18:08
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Express extends Model
{
    use HasFactory;

    protected $table = 'expressquery';

    public $timestamps = false;

    protected $guarded = [];
}

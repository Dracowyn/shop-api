<?php
/**
 * @author Dracowyn
 * @since 2023-12-13 14:24
 */

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;

    protected $table = 'business_source';
}

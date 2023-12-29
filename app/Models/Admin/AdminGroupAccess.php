<?php
/**
 * @author Dracowyn
 * @since 2023-12-29 13:56
 */

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminGroupAccess extends Model
{
    use HasFactory;

    protected $table = 'auth_group_access';
}

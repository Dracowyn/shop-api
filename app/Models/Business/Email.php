<?php
/**
 * @author Dracowyn
 * @since 2023-12-15 18:59
 */

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $table = 'ems';

    public $timestamps = true;

    protected $dateFormat = 'U';

    const CREATED_AT = 'createtime';

    const UPDATED_AT = null;

    protected $guarded = [];
}

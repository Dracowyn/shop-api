<?php
/**
 * @author Dracowyn
 * @since 2023-12-22 18:27
 */

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    protected $table = 'business_record';

    public $timestamps = true;

    protected $dateFormat = 'U';

    const CREATED_AT = 'createtime';

    const UPDATED_AT = null;

    protected $guarded = [];

    protected $appends = [
        'createtime_text',
    ];

    public function getCreatetimeTextAttribute()
    {
        return date('Y-m-d H:i:s', strtotime($this->createtime));
    }

}

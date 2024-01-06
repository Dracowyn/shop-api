<?php
/**
 * @author Dracowyn
 * @since 2024-01-06 12:22
 */

namespace App\Models\Business;

use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    use HasFactory;

    protected $table = 'business_visit';

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

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'busid', 'id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'adminid', 'id');
    }

}

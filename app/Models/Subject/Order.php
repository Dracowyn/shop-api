<?php
/**
 * 课程订单模型
 * @author Dracowyn
 * @since 2024-01-06 16:31
 */

namespace App\Models\Subject;

use App\Models\Admin\Admin;
use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'subject_order';

    public $timestamps = true;

    protected $dateFormat = 'U';

    const CREATED_AT = 'create_time';

    const UPDATED_AT = 'update_time';

    const DELETED_AT = 'delete_time';

    protected $guarded = [];

    protected $appends = [
        'create_time_text',
        'update_time_text',
    ];

    public function getCreateTimeTextAttribute(): string
    {
        return date('Y-m-d H:i:s', strtotime($this->create_time));
    }

    public function getUpdateTimeTextAttribute(): string
    {
        return date('Y-m-d H:i:s', strtotime($this->update_time));
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subid', 'id');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'busid', 'id');
    }

}

<?php
/**
 * 客户申领模型
 * @author Dracowyn
 * @since 2024-01-05 11:16
 */

namespace App\Models\Business;

use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receive extends Model
{
    use HasFactory;

    protected $table = 'business_receive';

    public $timestamps = true;

    protected $dateFormat = 'U';

    const CREATED_AT = 'applytime';

    const UPDATED_AT = null;

    const DELETED_AT = null;

    protected $guarded = [];

    protected $appends = [
        'applytime_text',
        'status_text',
    ];

    // 关联客户
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'busid', 'id');
    }

    // 关联管理员
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'applyid', 'id');
    }

    public function getApplytimeTextAttribute()
    {
        return date('Y-m-d H:i:s', strtotime($this->applytime));
    }

    public function getStatusTextAttribute()
    {
        $status = [
            'apply' => '申请',
            'allot' => '分配',
            'recovery' => '回收',
            'reject' => '拒绝',
        ];
        return $status[$this->status];
    }
}

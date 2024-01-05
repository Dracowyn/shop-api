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

    // 关联客户
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'businessid', 'id');
    }

    // 关联管理员
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'adminid', 'id');
    }
}

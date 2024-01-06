<?php
/**
 * 课程模型
 * @author Dracowyn
 * @since 2024-01-06 16:24
 */

namespace App\Models\Subject;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'subject';

    public $timestamps = true;

    protected $dateFormat = 'U';

    const CREATED_AT = 'create_time';

    const UPDATED_AT = 'update_time';

    const DELETED_AT = 'delete_time';

    protected $guarded = [];

    protected $appends = [
        'create_time_text',
        'update_time_text',
//        'thumbs_cdn',
    ];

    public function getCreateTimeTextAttribute(): string
    {
        return date('Y-m-d H:i:s', strtotime($this->create_time));
    }

    public function getUpdateTimeTextAttribute(): string
    {
        return date('Y-m-d H:i:s', strtotime($this->update_time));
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'cateid', 'id');
    }

}

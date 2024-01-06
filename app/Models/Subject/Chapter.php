<?php
/**
 * 课程章节模型
 * @author Dracowyn
 * @since 2024-01-06 16:39
 */

use App\Models\Subject\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chapter extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'subject_chapter';

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
}

<?php
/**
 * 客户模型
 * @author Dracowyn
 * @since 2023-12-12 15:01
 */

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Region as RegionModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory;
    use SoftDeletes;

    // 指定的数据表
    protected $table = 'business';

    // 自动写入时间戳
    public $timestamps = true;

    // 时间类型
    protected $dateFormat = 'U';

    // 自定义创建时间字段
    const CREATED_AT = 'create_time';

    // 自定义更新时间字段
    const UPDATED_AT = 'update_time';

    // 自定义软删除时间字段
    const DELETED_AT = 'delete_time';

    // 追加字段
    protected $appends = [
        'mobile_text',
        'region_text',
        'deal_text',
        'create_time_text'
    ];

    protected $guarded = [];

    // 定义不存在的字段
    // 隐藏手机号
    public function getMobileTextAttribute()
    {
        return substr_replace($this->mobile, '****', 3, 4);
    }

    public function getRegionTextAttribute(): string
    {
        $province = RegionModel::where('code', $this->province)->value('name');
        $city = RegionModel::where('code', $this->city)->value('name');
        $district = RegionModel::where('code', $this->district)->value('name');

        $text = '';

        if ($province) {
            $text = $province . '-';
        }

        if ($city) {
            $text .= $city . '-';
        }

        if ($district) {
            $text .= $district;
        }

        return $text;
    }

    public function getDealTextAttribute(): string
    {
        $text = '';

        switch ($this->deal) {
            case '0':
                $text = '未成交';
                break;
            case '1':
                $text = '已成交';
                break;
        }

        return $text;
    }

    public function getCreateTimeTextAttribute(): string
    {
        return date('Y-m-d H:i:s', strtotime($this->create_time));
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'sourceid', 'id');
    }
}

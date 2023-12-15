<?php
/**
 * 客户收货地址模型
 * @author Dracowyn
 * @since 2023-12-15 11:34
 */

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Region as RegionModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'business_address';
    public $timestamps = true;

    // 时间类型
    protected $dateFormat = 'U';

    // 自定义创建时间字段
    const CREATED_AT = null;

    // 自定义更新时间字段
    const UPDATED_AT = null;
    // 自定义软删除时间字段
    const DELETED_AT = 'deletetime';

    protected $guarded = [];

    // 追加字段
    protected $appends = [
        'region_text'
    ];

    public function getRegionTextAttribute()
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
}

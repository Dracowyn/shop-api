<?php
/**
 * @author Dracowyn
 * @since 2024-01-11 17:03
 */

namespace App\Models;

use App\Models\Business\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Config as ConfigModel;

class Category extends Model
{
    use HasFactory;

    protected $table = 'category';

    public $timestamps = true;

    protected $dateFormat = 'U';

    const CREATED_AT = 'createtime';

    const UPDATED_AT = 'updatetime';

    protected $guarded = [];

    protected $appends = [
        'type_text',
        'flag_text',
        'image_cdn',
        'createtime_text',
        'collection_status',
    ];

    public function getFlagList(): array
    {
        return ['hot' => '热门', 'new' => '新品', 'recommend' => '推荐'];
    }

    public function getFlagTextAttribute(): string
    {
        $value = explode(',', $this->flag);
        $flag = $this->getFlagList();
        return implode(',', array_intersect_key($flag, array_flip($value)));
    }

    public function getTypeTextAttribute()
    {
        $typeJson = ConfigModel::where('name', 'category_type')->value('value');

        $type = json_decode($typeJson, true);

        return $type[$this->type] ?? '';
    }

    public function getImageCdnAttribute(): bool|string
    {
        $url = ConfigModel::where(['name' => 'url'])->value('value');
        return httpRequest($url . '/rent/category/image', ['cateid' => $this->id]);
    }

    public function getCreatetimeTextAttribute(): string
    {
        return date('Y-m-d H:i:s', strtotime($this->createtime));
    }

    public function getCollectionStatusAttribute(): bool
    {
        $busId = request('busid', 0);

        $collection = Collection::where([
            ['busid', '=', $busId],
            ['cateid', '=', $this->id],
        ]);

        if ($collection) {
            return true;
        } else {
            return false;
        }
    }


}

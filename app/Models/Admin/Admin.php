<?php
/**
 * @author Dracowyn
 * @since 2023-12-29 13:53
 */

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\AdminGroupAccess as AuthGroupAccessModel;
use App\Models\Config as ConfigModel;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admin';

    public $timestamps = true;

    protected $dateFormat = 'U';

    const CREATED_AT = 'createtime';

    const UPDATED_AT = 'updatetime';

    public $appends = [
        'group_text',
        'avatar_cdn',
    ];

    public function getGroupTextAttribute()
    {
        // 获取分组id
        $gid = AuthGroupAccessModel::where(['uid' => $this->id])->value('group_id');

        if (empty($gid)) {
            return '暂无角色组';
        }

        $name = AuthGroupAccessModel::where(['id' => $gid])->value('name');

        if (empty($name)) {
            return '暂无角色组名称';
        }

        return $name;
    }

    public function getAvatarCdnAttribute()
    {
        $cdn = ConfigModel::where('name', 'url')->value('value');

        $url = $cdn . '/shop/admin/avatar';

        return httpRequest($url, ['uid' => $this->id]);
    }
}

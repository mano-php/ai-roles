<?php

namespace ManoCode\AiRoles\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * AI角色分类
 */
class AiRolesCate extends Model
{
	use SoftDeletes;

	protected $table = 'ai_roles_cate';
    public function roles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AiRole::class,'cate_id','id')->where('state',1)->select([
            'id',
            'cate_id',
            'role_name',
            'app_id',
            'role_avatar',
            'desc',
            'created_at'
        ]);
    }
}

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
}
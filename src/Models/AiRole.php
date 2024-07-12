<?php

namespace ManoCode\AiRoles\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * AI角色管理
 */
class AiRole extends Model
{
	use SoftDeletes;

	protected $table = 'ai_roles';
    protected $primaryKey = 'id';

    protected $casts = [
        'questions' => 'array',
        'prompt' => 'json',
        'model_id' => 'integer',
        'system_prompt' => 'json',
    ];

    public function cate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AiRolesCate::class, 'cate_id', 'id');
    }
}

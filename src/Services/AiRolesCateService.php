<?php

namespace ManoCode\AiRoles\Services;

use ManoCode\AiRoles\Models\AiRolesCate;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * AI角色分类
 *
 * @method AiRolesCate getModel()
 * @method AiRolesCate|\Illuminate\Database\Query\Builder query()
 */
class AiRolesCateService extends AdminService
{
	protected string $modelName = AiRolesCate::class;

	public function searchable($query)
	{
		parent::searchable($query);

		$query->when($this->request->input('cate_name'), fn($q) => $q->where('cate_name', 'like', '%' . $this->request->input('cate_name') . '%'));

	}
}

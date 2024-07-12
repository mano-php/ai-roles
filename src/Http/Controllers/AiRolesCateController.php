<?php

namespace ManoCode\AiRoles\Http\Controllers;

use ManoCode\AiRoles\Services\AiRolesCateService;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 * AI角色分类
 *
 * @property AiRolesCateService $service
 */
class AiRolesCateController extends AdminController
{
	protected string $serviceName = AiRolesCateService::class;

	public function list()
	{
		$crud = $this->baseCRUD()
			->filterTogglable(false)
			->headerToolbar([
				$this->createButton('dialog'),
				...$this->baseHeaderToolBar()
			])
			->columns([
				amis()->TableColumn('id', 'ID')->sortable(),
				amis()->TableColumn('cate_name', '分类名称'),
				amis()->TableColumn('cate_desc', '分类描述'),
				amis()->TableColumn('cate_icon', '分类图标')->type('image'),
				amis()->SwitchContainer('state', '状态'),
				amis()->TableColumn('sort', '排序')->sortable(),
				amis()->TableColumn('created_at', admin_trans('admin.created_at'))->type('datetime')->sortable(),
				amis()->TableColumn('updated_at', admin_trans('admin.updated_at'))->type('datetime')->sortable(),
				$this->rowActions('dialog')
			]);

		return $this->baseList($crud);
	}

	public function form($isEdit = false)
	{
		return $this->baseForm()->body([
			amis()->TextControl('cate_name', '分类名称'),
			amis()->TextControl('cate_desc', '分类描述'),
			ManoImageControl('cate_icon', '分类图标'),
			amis()->SwitchControl('state', '状态')->onText('正常')->offText('禁用'),
			amis()->TextControl('sort', '排序'),
		]);
	}

	public function detail()
	{
		return $this->baseDetail()->body([
			amis()->TextControl('cate_name', '分类名称')->static(),
			amis()->TextControl('cate_desc', '分类描述')->static(),
			amis()->TextControl('cate_icon', '分类图标')->static(),
			amis()->SwitchContainer('state', '状态'),
			amis()->TextControl('sort', '排序')->static(),
			amis()->TextControl('created_at', admin_trans('admin.created_at'))->static(),
			amis()->TextControl('updated_at', admin_trans('admin.updated_at'))->static(),
		]);
	}
}

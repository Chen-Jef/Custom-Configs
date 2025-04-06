<?php

namespace Dcat\Admin\Jef\CustomConfigs\Http\Forms;

use Dcat\Admin\Jef\CustomConfigs\Services\CustomConfigGroupService;
use Dcat\Admin\Jef\CustomConfigs\Services\CustomConfigService;
use Dcat\Admin\Widgets\Form;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Contracts\LazyRenderable;
use Illuminate\Support\Facades\DB;

class DelMyConfig extends Form implements LazyRenderable
{
    use LazyWidget;

    // 处理请求
    public function handle(array $input)
    {
        try {
            DB::beginTransaction();

            if(!$input['config_tree']){
                return $this->response()->error('请选择要删除的配置');
            }

            CustomConfigService::deleteSpecificConfigs($input['config_tree']);

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return $this->response()->error($e->getMessage());
        }

        return $this->response()->success('删除成功')->refresh();
    }

    public function form()
    {
        // 获取所有配置分组（假设分组模型有group_slug、name、order字段）
        $groups = CustomConfigGroupService::getGroupList();

        // 初始化树节点数据
        $nodes = [];

        foreach ($groups as $group) {
            // 创建分组节点
            $nodes[] = [
                'id' => $group['slug'],
                'title' => $group['name'],
                'parent_id' => 0,
            ];

            // 获取该分组下的所有配置项
            $configs = CustomConfigService::getConfigsByGroup($group['slug']);

            foreach ($configs as $config) {
                // 创建配置项节点
                $nodes[] = [
                    'id' => $config->key,
                    'title' => $config->name,
                    'parent_id' => $config->group_slug,
                ];
            }
        }

        $this->tree('config_tree','配置列表')
            ->nodes($nodes)
            ->expand(false)
            ->setIdColumn('id')
            ->setTitleColumn('title')
            ->setParentColumn('parent_id');
    }
}

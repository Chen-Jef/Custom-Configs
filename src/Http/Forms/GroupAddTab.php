<?php

namespace Dcat\Admin\Jef\CustomConfigs\Http\Forms;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Form\NestedForm;
use Dcat\Admin\Jef\CustomConfigs\CustomConfigsServiceProvider;
use Dcat\Admin\Jef\CustomConfigs\Models\AdminCustomConfigs;
use Dcat\Admin\Jef\CustomConfigs\Models\AdminCustomConfigGroups;
use Dcat\Admin\Jef\CustomConfigs\Services\CustomConfigGroupService;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GroupAddTab extends Form implements LazyRenderable
{
    use LazyWidget;

    public $config_group;

    public function __construct($data = [], $key = null)
    {
        $this->config_group = CustomConfigGroupService::getGroupList();
        parent::__construct($data, $key);
    }

    public function handle(array $input)
    {
        if (!isset($input['jef-config-group']) || !is_array($input['jef-config-group'])) {
            return $this->response()->error('未接收到有效的配置分组数据');
        }

        try {
            DB::beginTransaction();

            $currentSlugs = [];
            foreach ($input['jef-config-group'] as $group) {
                $id = $group['id'];
                $oldSlug = $group['old_slug'];
                $newSlug = $group['slug'];
                $name = $group['name'];
                $order = $group['order'];

                // 数据验证
                $validator = Validator::make($group, [
                    'slug' => 'required|min:1|max:80',
                    'name' => 'required|min:1|max:10',
                    'order' => 'required|integer|min:1|max:9999'
                ]);
                if ($validator->fails()) {
                    return $this->response()->error('输入数据验证失败: ' . $validator->errors()->first());
                }

                if ($id) {
                    // 检查重复
                    $repeatGroup = AdminCustomConfigGroups::query()->where('slug', $newSlug)->where('id','<>',$id)->exists();
                    if($repeatGroup){
                        return $this->response()->error("存在同标识分组，无法更新");
                    }

                    // 更新操作
                    $existingGroup = AdminCustomConfigGroups::query()->where('id', $id)->first();

                    if ($existingGroup->slug !== $newSlug) {
                        // 更新分组slug并同步配置
                        $existingGroup->update([
                            'slug' => $newSlug,
                            'name' => $name,
                            'order' => $order
                        ]);
                        AdminCustomConfigs::query()->where('group_slug', $oldSlug)->update(['group_slug' => $newSlug]);
                    } else {
                        // 仅更新name和order
                        $existingGroup->update([
                            'name' => $name,
                            'order' => $order
                        ]);
                    }
                } else {
                    // 查询包括已删除的分组
                    $group = AdminCustomConfigGroups::withTrashed()->where('slug', $newSlug)->first();
                    if ($group) {
                        if ($group->trashed()) {
                            // 如果分组已删除，则恢复并更新参数
                            $group->restore();
                            $group->update([
                                'name' => $name,
                                'order' => $order
                            ]);
                        } else {
                            // 如果分组未删除，提示已存在
                            return $this->response()->error("分组标识 {$newSlug} 已存在，请更换。");
                        }
                    }else{
                        // 创建新分组
                        AdminCustomConfigGroups::query()->create([
                            'slug' => $newSlug,
                            'name' => $name,
                            'order' => $order
                        ]);
                    }
                }

                $currentSlugs[] = $newSlug;
            }

            // 删除不再存在的分组
            $slugsToDelete = AdminCustomConfigGroups::query()->whereNotIn('slug', $currentSlugs)->pluck('slug')->toArray();
            foreach ($slugsToDelete as $slug) {
                $configCount = AdminCustomConfigs::query()->where('group_slug', $slug)->count();
                if ($configCount > 0) {
                    return $this->response()->error("分组 {$slug} 下存在配置，请先手动删除这些配置再尝试删除分组。")->refresh();
                }
            }
            AdminCustomConfigGroups::query()->whereIn('slug', $slugsToDelete)->delete();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->response()->error(CustomConfigsServiceProvider::trans('custom-config.response.error'));
        }
        return $this->response()->success(CustomConfigsServiceProvider::trans('custom-config.response.success'))->refresh();
    }

    public function form()
    {
        $config_group = Helper::array($this->config_group);
        $this->table('jef-config-group', '配置分组', function (NestedForm $table) {
            $table->hidden('id'); // 新增隐藏字段
            $table->hidden('old_slug'); // 新增隐藏字段
            $table->text('name')
                ->rules(['required', 'min:1', 'max:10'], ['required' => '分组名称不能为空', 'min' => '分组名称长度不能小于1个字符', 'max' => '分组名称长度不能大于10个字符']);
            $table->text('slug')
                ->rules(['required', 'min:1', 'max:80'], ['required' => '分组标识不能为空', 'min' => '分组标识长度不能小于1个字符', 'max' => '分组标识长度不能大于80个字符'])
                ->help('标识必须唯一');
            $table->text('order')
                ->help('排序数字越小越靠前, 范围为1-9999, 默认为100')
                ->rules(['required', 'integer', 'min:1', 'max:9999'], ['required' => '排序不能为空', 'integer' => '排序必须为数字', 'min' => '排序必须大于等于1', 'max' => '排序必须小于等于9999'])
                ->default('100');
        })->customFormat(function () use($config_group){
            if(!empty($config_group)){
                foreach ($config_group as &$item){
                    $item['old_slug'] = $item['slug'];
                }
            }
            return $config_group;
        });
    }
}

<?php

namespace Dcat\Admin\Jef\CustomConfigs\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Jef\CustomConfigs\Http\Forms\AddMyConfig;
use Dcat\Admin\Jef\CustomConfigs\Http\Forms\DelMyConfig;
use Dcat\Admin\Jef\CustomConfigs\Http\Forms\GroupAddTab;
use Dcat\Admin\Jef\CustomConfigs\Http\Forms\GroupTab;
use Dcat\Admin\Jef\CustomConfigs\Services\CustomConfigGroupService;
use Dcat\Admin\Jef\CustomConfigs\Services\TabService;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Lazy;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Widgets\Tab;
use Illuminate\Routing\Controller;
use Dcat\Admin\Jef\CustomConfigs\CustomConfigsServiceProvider;
use Illuminate\Support\Str;

class CustomConfigsController extends Controller
{
    protected $title;
    protected $description;
    protected $tips;
    public function __construct()
    {
        $this->title = CustomConfigsServiceProvider::trans('custom-config.iframe.title');
        $this->description = CustomConfigsServiceProvider::trans('custom-config.iframe.description');
        $this->tips = CustomConfigsServiceProvider::trans('custom-config.iframe.tips');

        Admin::requireAssets('@jef.custom-configs');
    }

    public function index(Content $content)
    {
        $btn_add = CustomConfigsServiceProvider::trans('custom-config.iframe.btn_add');
        $btn_del = CustomConfigsServiceProvider::trans('custom-config.iframe.btn_del');

        return $content
            ->title($this->title)
            ->description($this->description)
            ->prepend(
                Modal::make()
                    ->xl()
                    ->title($btn_add)
                    ->body(AddMyConfig::make())
                    ->button('<button class="btn btn-primary btn-outline" style="margin: 10px 10px 10px 0"><span class="d-none d-sm-inline">&nbsp;&nbsp;' . $btn_add . '</span></button>').
                Modal::make()
                    ->xl()
                    ->title($btn_del)
                    ->body(DelMyConfig::make())
                    ->button('<button class="btn btn-primary btn-outline"><span class="d-none d-sm-inline">&nbsp;&nbsp;' . $btn_del . '</span></button>')
            )
            ->body(function (Row $row) {
                $tab = new Tab();
                $group_list = CustomConfigGroupService::getGroupList();
                $count = count($group_list);
                if($count){
                    for ($i = 0; $i < $count; $i++){
                        $tabPage = GroupTab::make()->payload(['group_slug'=>$group_list[$i]['slug']])->render();
                        $tab->add($group_list[$i]['name'],$tabPage,!$i,$group_list[$i]['slug']);
                    }
                }
                $tab->add('配置分组',GroupAddTab::make()->render(),!$count,'fixed-group-setting');
                $row->column(12, $tab->withCard()->addVariables());

            });
    }
}

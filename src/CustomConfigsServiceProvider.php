<?php

namespace Dcat\Admin\Jef\CustomConfigs;

use Dcat\Admin\Extend\ServiceProvider;

class CustomConfigsServiceProvider extends ServiceProvider
{
	protected $js = [];
	protected $css = [];

    public function register()
    {
        parent::register(); // 必须调用父级方法
    }

    // 定义菜单
    protected $menu = [
        [
            'title' => '自定义配置',
            'uri'   => 'jef/custom-configs',
            'icon'  => '', // 图标可以留空
        ],
    ];

    public function settingForm()
    {
        return new Setting($this);
    }
}

<?php

namespace Dcat\Admin\Jef\CustomConfigs;

use Dcat\Admin\Extend\Setting as Form;
use Dcat\Admin\Jef\CustomConfigs\Services\DiskService;

class Setting extends Form
{
    public function form()
    {
        $this->select('disk','文件存储驱动')
            ->options([
                'public'=>'本地存储:public驱动','qiniu'=>'七牛云存储'
            ])
            ->default(DiskService::DISK_PUBLIC)
            ->help('暂时只支持本地存储和七牛云存储')
            ->required();
    }
}

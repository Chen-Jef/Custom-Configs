<?php

namespace Dcat\Admin\Jef\CustomConfigs\Services;

use Dcat\Admin\Jef\CustomConfigs\Models\AdminCustomConfigGroups;

class CustomConfigGroupService
{
    public static function getGroupList()
    {
        return AdminCustomConfigGroups::query()->select(['id','name','slug','order'])->orderBy('order')->get();
    }
}

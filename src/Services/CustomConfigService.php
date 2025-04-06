<?php

namespace Dcat\Admin\Jef\CustomConfigs\Services;

use Dcat\Admin\Jef\CustomConfigs\Models\AdminCustomConfigs;

class CustomConfigService
{

    /**
     * 获取所有配置
     */
    public static function getAllConfigs()
    {
        return AdminCustomConfigs::query()->orderBy('order')->get();
    }

    /**
     * 获取分组中的所有配置
     *
     * @param string $groupSlug
     */
    public static function getConfigsByGroup($groupSlug)
    {
        return AdminCustomConfigs::query()->where('group_slug', $groupSlug)->orderBy('order')->get();
    }

    /**
     * 获取分组中的某几项配置
     *
     * @param string $groupSlug
     * @param array $configSlugs
     */
    public static function getSpecificConfigsByGroup($groupSlug, array $configSlugs)
    {
        return AdminCustomConfigs::query()->where('group_slug', $groupSlug)
            ->whereIn('key', $configSlugs)
            ->orderBy('order')
            ->get();
    }

    /**
     * 获取指定配置的所有信息
     *
     * @param string $configSlug
     */
    public static function getConfigInfo($configSlug)
    {
        return AdminCustomConfigs::query()->where('key', $configSlug)->first();
    }

    /**
     * 获取指定配置的值
     *
     * @param string $configSlug
     */
    public static function getConfigValue($configSlug)
    {
        return AdminCustomConfigs::query()->where('key', $configSlug)->value('content');
    }

    /**
     * 删除指定配置
     *
     * @param array $configSlugs
     */
    public static function deleteSpecificConfigs(array $configSlugs)
    {
        return AdminCustomConfigs::query()->whereIn('key', $configSlugs)->delete();
    }
}

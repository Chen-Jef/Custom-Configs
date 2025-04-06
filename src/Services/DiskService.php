<?php

namespace Dcat\Admin\Jef\CustomConfigs\Services;

use Dcat\Admin\Jef\CustomConfigs\CustomConfigsServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DiskService
{
    const DISK_PUBLIC = 'public';

    const DISK_QINIU = 'qiniu';

    public static function getCustomConfigFileDisk()
    {
        return CustomConfigsServiceProvider::setting('disk') ?: self::DISK_PUBLIC;
    }

    public static function getCustomConfigFilePath($path)
    {
        if (Str::contains($path, '//')) {
            return $path;
        }
        return url('/') . $path;
    }
}

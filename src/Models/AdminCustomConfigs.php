<?php

namespace Dcat\Admin\Jef\CustomConfigs\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminCustomConfigs extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'admin_custom_configs';

    protected $guarded = [];

    protected $fillable = [
        'group_slug','name','key','tip','type','disk','content','order'
    ];

    public static array $formType = [
        'string' => '字符串',
        'number' => '数字',
        'mobile' => '手机号',
        'url' => '链接',
        'email' => 'Email', //
        'color'  => '颜色',
        'date'   => '日期',
        'time'   => '时间',
        'datetime' => '日期时间',
        'image'    => '单图',
        'multipleImage'   => '多图',
        'file'    => '单文件',
        'multipleFile'   => '多文件',
        'textarea' => '长文本', //
        'editor' => '富文本', //
        'json'     => 'JSON',
    ];

    public function group()
    {
        return $this->belongsTo(AdminCustomConfigGroups::class, 'group_slug','slug');
    }

}

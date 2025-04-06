<?php

namespace Dcat\Admin\Jef\CustomConfigs\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminCustomConfigGroups extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'admin_custom_config_groups';

    protected $guarded = [];

    protected $fillable = [
        'name','slug','order'
    ];

    public function configs()
    {
        return $this->hasMany(AdminCustomConfigs::class, 'group_slug','slug');
    }

}

<?php

namespace Dcat\Admin\Jef\CustomConfigs\Http\Forms;

use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Jef\CustomConfigs\Models\AdminCustomConfigs;
use Dcat\Admin\Jef\CustomConfigs\Services\CustomConfigService;
use Dcat\Admin\Jef\CustomConfigs\Services\DiskService;
use Dcat\Admin\Jef\CustomConfigs\Services\TabService;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Dcat\Admin\Form\NestedForm;

class GroupTab extends Form implements LazyRenderable
{

    use LazyWidget;

//    public function __construct($data = [], $key = null)
//    {
//        Admin::requireAssets('@jef.custom-configs');
//        parent::__construct($data, $key);
//    }

    public function handle(array $input)
    {
        try {
            foreach ($input as $key => $val){
                AdminCustomConfigs::query()->where('key',$key)->update([
                    'content'=>$val
                ]);
            }
        }catch (\Exception $e){
            return $this->response()->error($e->getMessage());
        }
        return $this->response()->success('保存成功')->refresh();
    }

    public function form()
    {
        $group_slug = $this->payload['group_slug'];
        $group_configs = CustomConfigService::getConfigsByGroup($group_slug);
        $disk = DiskService::getCustomConfigFileDisk();

        $group_configs->each(function ($item) use($disk){
            switch($item->type){
                case 'json':
                    $content = $item->content ? json_decode($item->content, true) : [];
                    $this->table($item->key,$item->name,function (NestedForm $table) use($content){
                        if($content){
                            $list = array_keys($content[0]);
                            foreach ($list as $val){
                                $table->text($val);
                            }
                        }else{
                            $table->text('key');
                            $table->text('value');
                        }
                    })->help($item->tip)->default($content)->saving(function ($paths) {
                        $paths = Helper::array($paths);
                        return json_encode($paths,JSON_UNESCAPED_UNICODE);
                    });
                    break;
                case 'string':
                    $this->text($item->key,$item->name)->help($item->tip)->default($item->content);
                    break;
                case 'image':
                    $this->image($item->key,$item->name)
                        ->disk($item->disk)
                        ->removable(false)
                        ->autoUpload()
                        ->accept('jpg,png,jpeg', 'image/*')
                        ->help('图片大小不得超过10M')
                        ->maxSize(10240)
                        ->move('custom-config/images')
                        ->saveFullUrl(config('filesystems.disks.'.$disk.'.driver') !== 'local')
                        ->width(4)
                        ->customFormat(function () use($item){
                            return DiskService::getCustomConfigFilePath($item->content);
                        })
                        ->saveAsString();
                    break;
                case 'url':
                    $this->url($item->key,$item->name)->help($item->tip)->default($item->content)
                        ->rules('url:http,https',[
                            'url'=>'请输入合法的URL地址',
                        ]);
                    break;
                default:
                    $type = $item->type;
                    $this->$type($item->key,$item->name)->help($item->tip)->default($item->content);
            }
        });
    }
}

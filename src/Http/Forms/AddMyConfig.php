<?php

namespace Dcat\Admin\Jef\CustomConfigs\Http\Forms;

use Dcat\Admin\Form\NestedForm;
use Dcat\Admin\Jef\CustomConfigs\CustomConfigsServiceProvider;
use Dcat\Admin\Jef\CustomConfigs\Models\AdminCustomConfigs;
use Dcat\Admin\Jef\CustomConfigs\Services\CustomConfigGroupService;
use Dcat\Admin\Jef\CustomConfigs\Services\DiskService;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Form;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Contracts\LazyRenderable;
use Illuminate\Support\Facades\DB;

class AddMyConfig extends Form implements LazyRenderable
{
    use LazyWidget;

    // 处理请求
    public function handle(array $input)
    {
        // 逻辑操作
        $new_config['name'] = $input['name'];
        $new_config['group_slug'] = $input['group_slug'];
        $new_config['key'] = $input['key'];
        $new_config['tip'] = $input['tip'];
        $new_config['type'] = $input['type'];
        $new_config['content'] = $input['content_'.$input['type']];
        if(in_array($input['type'],['image','multipleImage','file','multipleFile'])){
            $new_config['disk'] = DiskService::getCustomConfigFileDisk();
        }else{
            $new_config['disk'] = '';
        }

        try {
            DB::beginTransaction();

            $exist = AdminCustomConfigs::query()->where('key',$input['key'])->exists();
            if($exist){
                return $this->response()->error(CustomConfigsServiceProvider::trans('custom-config.response.exist'))->refresh();
            }

            AdminCustomConfigs::query()->create($new_config);

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return $this->response()->error(CustomConfigsServiceProvider::trans('custom-config.response.error'));
        }
        return $this->response()->success(CustomConfigsServiceProvider::trans('custom-config.response.success'))->refresh();
    }

    public function form()
    {
        $group_option = CustomConfigGroupService::getGroupList();
        $group_option = array_column(Helper::array($group_option), 'name','slug');
        $disk = DiskService::getCustomConfigFileDisk();

        $formTypeOptions = AdminCustomConfigs::$formType;
        if(!$disk){
            $this->html('<div class="alert alert-danger">'.CustomConfigsServiceProvider::trans('custom-config.response.no-disk').'</div>');
            unset($formTypeOptions['image'], $formTypeOptions['multipleImage'], $formTypeOptions['file'], $formTypeOptions['multipleFile']);
        }

        $this->select('group_slug',CustomConfigsServiceProvider::trans('custom-config.fields.group'))
            ->options($group_option)
            ->required();
        $this->text('key',CustomConfigsServiceProvider::trans('custom-config.fields.key'))->maxLength(30)->required();
        $this->text('name',CustomConfigsServiceProvider::trans('custom-config.fields.name'))->maxLength(50)->required();
        $this->text('tip',CustomConfigsServiceProvider::trans('custom-config.fields.tip'))->maxLength(30);
        $this->select('type',CustomConfigsServiceProvider::trans('custom-config.fields.type'))
            ->options($formTypeOptions)
            ->when('string',function(){
                $this->text('content_string',CustomConfigsServiceProvider::trans('custom-config.fields.content'));
            })
            ->when('number',function(){
                $this->number('content_number',CustomConfigsServiceProvider::trans('custom-config.fields.content'));
            })
            ->when('mobile',function(){
                $this->mobile('content_mobile',CustomConfigsServiceProvider::trans('custom-config.fields.content'));
            })
            ->when('url',function(){
                $this->url('content_url',CustomConfigsServiceProvider::trans('custom-config.fields.content'));
            })
            ->when('email',function(){
                $this->email('content_email',CustomConfigsServiceProvider::trans('custom-config.fields.content'));
            })
            ->when('color',function(){
                $this->color('content_color',CustomConfigsServiceProvider::trans('custom-config.fields.content'));
            })
            ->when('date',function(){
                $this->date('content_date',CustomConfigsServiceProvider::trans('custom-config.fields.content'));
            })
            ->when('time',function(){
                $this->time('content_time',CustomConfigsServiceProvider::trans('custom-config.fields.content'));
            })
            ->when('datetime',function(){
                $this->datetime('content_datetime',CustomConfigsServiceProvider::trans('custom-config.fields.content'));
            })
            ->when('image',function() use($disk){
                $this->image('content_image',CustomConfigsServiceProvider::trans('custom-config.fields.content'))
                    ->disk($disk)
                    ->autoUpload()
                    ->accept('jpg,png,jpeg', 'image/*')
                    ->help('图片大小不得超过10M')
                    ->maxSize(10240)
                    ->move('custom-config/images')
                    ->saveFullUrl($disk !== 'public')
                    ->width(4)
                    ->saveAsString();
            })
            ->when('multipleImage',function() use($disk){
                $this->multipleImage('content_multipleImage',CustomConfigsServiceProvider::trans('custom-config.fields.content'))
                    ->disk($disk)
                    ->autoUpload()
                    ->accept('jpg,png,jpeg', 'image/*')
                    ->help('图片大小不得超过10M')
                    ->maxSize(10240)
                    ->move('custom-config/images')
                    ->saveFullUrl($disk !== 'public')
                    ->width(4)
                    ->saveAsString();
            })
            ->when('file',function() use($disk){
                $this->file('content_file',CustomConfigsServiceProvider::trans('custom-config.fields.content'))
                    ->disk($disk)
                    ->autoUpload()
                    ->help('文件大小不得超过100M')
                    ->maxSize(102400)
                    ->move('custom-config/files')
                    ->saveFullUrl($disk !== 'public')
                    ->width(4)
                    ->saveAsString();
            })
            ->when('multipleFile',function() use($disk){
                $this->multipleFile('content_multipleFile',CustomConfigsServiceProvider::trans('custom-config.fields.content'))
                    ->disk($disk)
                    ->autoUpload()
                    ->help('文件大小不得超过100M')
                    ->maxSize(102400)
                    ->move('custom-config/files')
                    ->saveFullUrl($disk !== 'public')
                    ->width(4)
                    ->saveAsString();
            })
            ->when('textarea',function(){
                $this->textarea('content_textarea',CustomConfigsServiceProvider::trans('custom-config.fields.content'));
            })
            ->when('editor',function(){
                $this->editor('content_editor',CustomConfigsServiceProvider::trans('custom-config.fields.content'));
            })
            ->when('json',function(){
                $this->table('content_json',CustomConfigsServiceProvider::trans('custom-config.fields.content'),function (NestedForm $table){
                    $table->text('key');
                    $table->text('value');
                })->saveAsString();
            })
            ->required();

    }
}

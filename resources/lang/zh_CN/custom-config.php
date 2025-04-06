<?php

return [
    'iframe' => [
        'title'         => '自定义配置',
        'description'   => '模块',
        'error_body'    => '分组未设置,请先去插件页面配置分组',
        'btn_add'       => '新增配置',
        'btn_del'       => '删除配置',
        'tips'          => '请务必保证所有配置中的key是唯一的，所有配置分组中的key也是唯一的',
        'config_exist'  => '这个配置已存在,请重新填写键名'
    ],

    'fields' => [
        'key'          => '唯一键名',
        'name'         => '标题',
        'type'          => '数据类型',
        'group'         => '分组',
        'content'       => '内容',
        'tip'           => '提示',
    ],

    'default_database' => [
        'basic_title'               => '站点名称',
        'basic_tip'                 => '请填写站点名称',
        'beian_title'               => '备案号',
        'beian_tip'                 => '闽ICP备15000000号-1',
        'forbiddenip_title'         => 'IP黑名单',
        'forbiddenip_tip'           => '一行一条记录',
        'configgroup_title'         => '配置分组',
        'configgroup_tip'           => '',
        'configgroup_basic'         => '基础配置',
        'configgroup_dictionary'    => '字典配置',
    ],

    'response' => [
        'success'       => '操作成功',
        'error'         => '操作失败,请联系开发者',
        'dictionary'    => '不允许修改字典配置',
        'no-disk'       => '未设置文件存储驱动，若要上传图片，请先去插件页面配置',
    ],

    'type' => [
        'string'            => '字符串',
        'number'            => '数字',
        'mobile'            => '手机号',
        'url'               => '链接',
        'email'             => 'Email', //
        'color'             => '颜色',
        'date'              => '日期',
        'time'              => '时间',
        'datetime'          => '日期时间',
        'image'             => '单图',
        'multipleImage'     => '多图',
        'file'              => '单文件',
        'multipleFile'      => '多文件',
        'textarea'          => '长文本', //
        'editor'            => '富文本', //
        'json'              => 'JSON',
    ]
];


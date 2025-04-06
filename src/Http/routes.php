<?php

use Dcat\Admin\Jef\CustomConfigs\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::any('jef/custom-configs', Controllers\CustomConfigsController::class.'@index')->name('dcat-admin.jef-custom-configs.index');

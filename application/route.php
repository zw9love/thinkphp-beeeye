<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;
//创建规则[demo/]，后面有二个可选参数，替换掉原来较长的URL地址
Route::rule('demo/[:name]','index/index/demo/');
Route::rule('host/test','host/host/test');

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[demo]'     => [
        ':id'   => ['index/demo', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/demo', ['method' => 'post']],
    ],

];

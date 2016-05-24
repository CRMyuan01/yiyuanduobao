<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

//注册
define('USER_REGEDIT_SUCCESS','0');
define('USER_REGEDIT_ERROR','1');
define('USER_REGEDIT_NAMEEXIST','2');
//登陆
define('USER_LOGIN_SUCCESS','0');
define('USER_LOGIN_NOUSER','1');
define('USER_LOGIN_PWDERROR','2');
//购买商品
define('USER_ADDRECORD_SUCCESS','0');
define('USER_ADDRECORD_PROOVERMAX','1');
define('USER_ADDRECORD_ERROR','2');
//所有商品获取
define('USER_SHOWPRODECT_SUCCESS','0');
define('USER_SHOWPRODECT_ERROR','1');
// 定义应用目录
define('APP_PATH','./Application/');

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单
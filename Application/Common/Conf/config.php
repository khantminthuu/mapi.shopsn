<?php
return [
	// '配置项' => '配置值'
	'DEFAULT_MODULE' => 'Home', // 默认模块
	'URL_MODEL' => '2', // URL模式
	'LOAD_EXT_CONFIG' => 'db', // 加载数据库配置文件
	/* 加载公共函数 */
	'LOAD_EXT_FILE' => 'common',
	// 设置短信有效时间---5分钟
	'send_msg_time' => 3000,
	// 系统默认的变量过滤机制
	// 'DEFAULT_FILTER' => 'strip_sql,htmlspecialchars',
	// 图片域名地址
	'img_url' => 'http://store.shopsn.cn/index.php',
	// 上传头像等图片设置图片大小
	'img_size' => 3145728,
	'img_type' => [
			'jpg',
			'gif',
			'png',
			'jpeg' 
	],
	'page_size' => 10, // 配置分页大小
	
	 'SHOW_PAGE_TRACE'=>true,
	
	'AUTOLOAD_NAMESPACE' => [ 
			'Extend' => 'Extend/',
			'Validate' => 'Extend/Validate'
	],
	'LOG_RECORD' => true, // 开启日志记录
	'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR', // 只记录EMERG ALERT CRIT ERR 错误
	'unlink_image_no_thumb' => 'http://center.shopsn.cn/upload.php/DeleteImage/deleteImageByNoThumb',
	
	'COOKIE_DOMAIN' => '.shopsn.cn',
	
//	'SESSION_TYPE' => 'Redis',

//	'SESSION_OPTIONS' => [
//		'auto_start' => 0,
//		'port' => 6380,
//		'domain' => '.shopsn.cn',
//		'expire' => 2400,
//		'select' => 1,
//		'type' => 'Redis',
//		'use_lock' => true
//	],
//	'DATA_CACHE_TYPE'   => 'Redis',
	'REDIS_PORT' => 6380,
	'origin' => [
		'http://m.shopsn.cn',
		'http://mobile.local.com:8087',
		'https://api.mch.weixin.qq.com',
		'http://localhost:8087',
		'https://openapi.alipay.com',
	],
    //客服域名
    'CHAT_URL' => 'http://chat.shopsn.cn',
    'Ak'    => 'PYEFj8H9YGackhMQOzUqtAT8had1sYgm', //您的百度地图Ak
];
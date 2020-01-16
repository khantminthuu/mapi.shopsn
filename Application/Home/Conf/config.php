<?php
return [
    //'配置项'=>'配置值'
    'MOBILE_DOMAIN'       => 'http://m.shopsn.cn',//手机端域名
    //图片上传配置
    'waybill_image_save_config' => '/uploadNum/1/input/textfield/path/waybill/callBack/checkImage/config/waybill_image_config',
    'create_thumb_file'         => 'http://center.shopsn.cn/upload.php/CreateImageThumb/createThumb',
    'unlink_image'              => 'http://center.shopsn.cn/upload.php/DeleteImage/deleteImageArray',
    'create_enter_image'       => 'http://center.shopsn.cn/upload.php/EnterUpload/uploadImage',
	'auth_error_url' => 'http://m.shopsn.cn/authError',
	'add_user_info'  => 'http://m.shopsn.cn/authLogin',
	'mobile' => 'http://m.shopsn.cn/person',
	'qq_redirect_url' => 'http://mapi.shopsn.cn/QQThirdPartyLogin/qqLoginCallBack',
];
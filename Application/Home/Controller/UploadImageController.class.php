<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\UploadImageLogic;
use Common\Tool\Extend\CURL;

class UploadImageController
{
    use InitControllerTrait;

    /**
     * 架构方法
     * @param array
     * $args   传入的参数数组
     */
    public function __construct(array $args = [])
    {
        $this->init();

        $this->args = $args;

        $this->logic = new UploadImageLogic($args);

     

    }
    /**
     * 上传头像
     */
    public function uploadImage()
    {
         
        $this->objController->promptPjax(!empty($_FILES['adv_content']), '请上传图片');
        
        $checkObj = new CheckParam($this->logic->getMessageByPic(), $_FILES['adv_content']);
        
        $this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
        
        $this->objController->promptPjax($this->logic->checkImageWidthAndHeight(), $this->logic->getErrorMessage());
        
        $curlFile = new CURL($_FILES['adv_content'], C('create_enter_image'));
        
        $file = $curlFile->uploadFile();
        
        echo $file;die; 
    }
    
    /**
     * 删除广告图片
     */
    public function delPic() 
    {
        $curlFile = new CURL($this->args, C('unlink_image_no_thumb'));
        
        echo $curlFile->deleteFile();die;
    }

}
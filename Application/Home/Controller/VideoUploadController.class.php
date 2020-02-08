<?php
declare(strict_types=1);
namespace Home\Controller;
use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\VideoUploadLogic;

class VideoUploadController{
    use InitControllerTrait;
    /*
     * Get http request array
     * */
    public function __construct(array $args=[])
    {
        $this->data = $args;
        
        $this->init();
        
        $this->logic = new VideoUploadLogic($args);
    }
   ##get all video upload
    public function getVideoUpload()
    {
        $ret = $this->logic->getVideoUpload();
        
        $this->objController->promptPjax($ret , $this->logic->getErrorMessage());
        
        $this->objController->ajaxReturnData($ret);
        
    }
    ##when click like icons
    public function addLike()
    {
        $checkObj = new checkParam( $this->logic->getUserId() , $this->data);
    
        $status = $checkObj->checkParam();
    
        $this->objController->promptPjax( $status, $checkObj->getErrorMessage());
        
        $ret = $this->logic->addLike();
    
        $this->objController->ajaxReturnData($ret['status'],$ret['message'],$ret['data']);
    }
}

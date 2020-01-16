<?php
namespace Common\Logic;
use Common\Model\UserModel;

/**
 * 逻辑处理层
 *
 */
class UploadImageLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
      
    }

    /**
     * 获取结果
     */
    public function getResult()
    {
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName() :string
    {

    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
     */
    public function hideenComment() :array
    {
        return [

        ];
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
     */
    public function likeSerachArray() :array
    {

    }

    /***
     * 上传图片
     */
      public function upload_image(){
        if (!empty($_FILES)) {
            $info = upload_image("images");

            if ($info['status'] == 0) {// 上传错误提示错误信息
                return $info['msg'];
            } else {// 上传成功
//              $info['info']['file']['url'] = $info['info']['file']['savepath'].$info['info']['file']['savename'];
//              $info['msg']['file']['url'] = $info['msg']['file']['savepath'].$info['msg']['file']['savename'];
              $image_info['info']['name'] = $info['msg']['file']['name'];
              $image_info['info']['url'] = $info['msg']['file']['savepath'].$info['msg']['file']['savename'];
              return $image_info;
            }
        }
    }
     /**
     * 上传图片验证
     * @return []
     */
    public function getMessageByPic()
    {   
        $message = [ 
            'tmp_name' => [
                'required' => '请上传图片',
            ],
        ];
        return $message;
    }
    /**
     * 验证图片宽高度
     * @return bool
     */
    public function checkImageWidthAndHeight()
    {
        
        $field = "key,config_value";
        
        $header_min_width = M("system_config")->field($field)->where(['key'=>"enter_min_width"])->getField("config_value");
        $header_max_width = M("system_config")->field($field)->where(['key'=>"enter_max_width"])->getField("config_value");
        $header_max_height = M("system_config")->field($field)->where(['key'=>"enter_max_height"])->getField("config_value");
        $header_min_height = M("system_config")->field($field)->where(['key'=>"enter_min_height"])->getField("config_value");
        if (empty($header_min_height)) {
            $this->errorMessage = '不存在 广告位配置';
            return false;
        }
        
        $imageInfo = getimagesize($_FILES['adv_content']['tmp_name']);
        
        $width = $imageInfo[0];
        
        $height = $imageInfo[1];
        
        if ($width > $header_max_width|| $width < $header_min_width) {
           
            $this->errorMessage = '宽度必须介于'.$header_min_width.'~'.$header_max_width.'之间，此图宽度'.$width;
            
            return false;
        }
        
        if ($height > $header_max_height || $width < $header_min_height) {
            
            $this->errorMessage = '高度必须介于'.$header_min_height.'~'.$header_max_height.'之间，此图高度'.$height;
            
            return false;
        }
        return true;
    } 
}

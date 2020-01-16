<?php
namespace Validate\Children;

use Validate\Common\CommonAttribute;

/**
 * 验证是否是图片
 * @author 王强
 */
class CheckIsPicture 
{
    use CommonAttribute;
    /**
     * 架构方法
     */
    public function __construct($data, $message = '')
    {
        $this->data = $data;
        
        $this->message = $message;
    }
    
    /**
     * 非空验证长度
     * {@inheritDoc}
     * @see \Validate\Validate::check()
     */
    public function check()
    {
        if (!empty($this->data)) {
            return getimagesize($this->data);
        }
        return true;
    }
}
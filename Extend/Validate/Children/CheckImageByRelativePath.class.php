<?php
declare(strict_types = 1);
namespace Validate\Children;

use Validate\Common\CommonAttribute;

/**
 * 相对路径验证图片
 * @author Administrator
 */
class CheckImageByRelativePath
{
    use CommonAttribute;
    /**
     * 架构方法
     */
    public function __construct($data, $message = '')
    {
        $this->data = '.'.$data;
        
        $this->message = $message;
    }
    
    /**
     * 非空验证长度
     * {@inheritDoc}
     * @see \Validate\Validate::check()
     */
    public function check() :bool
    {
        if (!empty($this->data)) {
            return getimagesize($this->data) !== false;
        }
        return true;
    }
}
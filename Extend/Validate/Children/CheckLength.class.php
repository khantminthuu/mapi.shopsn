<?php
declare(strict_types = 1);
namespace Validate\Children;

use Validate\Validate;
use Validate\Common\CommonAttribute;

/**
 * 验证规则是否是正确的电话号码
 * @author Administrator
 */
class CheckLength implements Validate
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
    public function check() :bool
    {
        if (!empty($this->data)) {
            
            $length = strlen($this->data);
            
            return $length <= 6 ? false : true;
        }
        return true;
    }
}
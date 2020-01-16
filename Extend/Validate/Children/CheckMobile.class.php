<?php
declare(strict_types = 1);
namespace Validate\Children;

use Validate\Validate;
use Validate\Common\CommonAttribute;

/**
 * 验证规则是否是正确的电话号码
 * @author Administrator
 */
class CheckTelphone implements Validate
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
     * {@inheritDoc}
     * @see \Validate\Validate::check()
     */
    public function check() :bool
    {
        return (preg_match("/^1[34578]{1}\d{9}$/",$this->data)) === 1;
    }
}
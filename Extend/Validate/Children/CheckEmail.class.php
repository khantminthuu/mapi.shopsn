<?php
declare(strict_types = 1);
namespace Validate\Children;

use Validate\Validate;
use Validate\Common\CommonAttribute;

/**
 * 验证规则是否是正确的电话号码
 * @author Administrator
 */
class CheckEmail implements Validate
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
    	$status = false;
    	
        if (!empty($this->data)) {
            $status = filter_var($this->data, FILTER_VALIDATE_EMAIL);
        }
        
        if ($status === false) {
            return false;
        }
        return true;
    }
}
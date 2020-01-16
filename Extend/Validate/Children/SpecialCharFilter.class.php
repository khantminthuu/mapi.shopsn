<?php
declare(strict_types = 1);
namespace Validate\Children;

use Validate\Validate;
use Validate\Common\CommonAttribute;

/**
 * 验证规则是否有特殊字符
 * @author Administrator
 */
class SpecialCharFilter implements Validate
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
    	if (empty($this->data)) {
    		return true;
    	}
    	//中英文及数字下划线空格
    	$preg='/^[\x{4e00}-\x{9fa5}0-9a-zA-Z-_\s]+$/u';
    	
    	$isTrue = preg_match($preg, $this->data);
    	
    	return $isTrue === 1;
    }
    
}
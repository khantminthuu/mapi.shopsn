<?php
declare(strict_types = 1);
namespace Validate\Children;

use Validate\Validate;
use Validate\Common\CommonAttribute;

/**
 * 验证规则是否是数字
 * @author Administrator
 */
class Number implements Validate
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
    	if (!is_numeric($this->data)) {
    		return false;
    	}
    	
    	$str = '';
    	if (false !== ($length = strpos($this->message, '${'))) {
    		$str = str_replace(['${', '}'], ['', ''], substr($this->message, $length));
    	}
    	
    	if (empty($str)) {
    		return true;
    	}
    	
    	list($first, $second) = explode('-', $str);
    	
    	if ( $first > $this->data || $this->data > $second) {
    		return false;
    	}
    	return true;
    }
       
}
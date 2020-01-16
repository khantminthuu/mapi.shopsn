<?php
declare(strict_types = 1);
namespace Validate\Children;

use Validate\Validate;
use Validate\Common\CommonAttribute;

/**
 * 验证规则是否有特殊字符
 * @author Administrator
 */
class Required implements Validate
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
        if ($this->data === 0  || $this->data === '0') {
            return true;
        }
        return !empty($this->data);
    }
    
}
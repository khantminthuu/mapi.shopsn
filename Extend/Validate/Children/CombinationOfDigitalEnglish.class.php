<?php
declare(strict_types = 1);
namespace Validate\Children;

use Validate\Validate;
use Validate\Common\CommonAttribute;

/**
 * 验证规则数字英文的组合
 * @author Administrator
 */
class CombinationOfDigitalEnglish implements Validate
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
     * 数字英文的组合
     * {@inheritDoc}
     * @see \Validate\Validate::check()
     */
    public function check() :bool
    {
        if (empty($this->data)) {
        	return true;
        }
        return preg_match('/^[a-zA-Z\d]*$/i', $this->data) !== false;
    }
}
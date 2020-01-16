<?php
declare(strict_types = 1);
namespace Validate\Common;

/**
 * 公共属性
 * @author 王强
 */
trait CommonAttribute 
{
    /**
     * 待验证数据
     * @var T
     */
    private $data;
    
    /**
     * 消息
     * @var string
     */
    private $message = '';
    
    /**
     * @return the $data
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * @return the $message
     */
    public function getMessage():string
    {
        return $this->message;
    }
    
    /**
     * @param \Validate\Children\T $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
    
    /**
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }
}


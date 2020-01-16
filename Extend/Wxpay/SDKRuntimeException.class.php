<?php
namespace Extend\Wxpay;


/**
 * 错误 
 */
class  SDKRuntimeException extends \Exception 
{
	public function errorMessage()
	{
		return $this->getMessage();
	}

}
<?php
namespace Common\Strategy\Attribute;

trait Attribute
{
	private $receive = [];
	
	private $freightData = 0;
	
	public function setReceive(array $receive) {
		$this->receive = $receive;
	}
	
	public function setFreightData($freightData) {
		
		$this->freightData = $freightData;
	}
	
}
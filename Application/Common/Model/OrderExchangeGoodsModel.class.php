<?php
namespace Common\Model;
use Think\Model;
/**
 * Class OrderModel
 * @package Common\Model
 */
class OrderExchangeGoodsModel extends BaseModel
{

    private static $obj;
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
}
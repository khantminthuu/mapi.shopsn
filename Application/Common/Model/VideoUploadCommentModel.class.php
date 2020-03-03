<?php
namespace Common\Model;
class VideoUploadCommentModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//id

	public static $userId_d;	//User Id

	public static $fId_d;	//parent id

	public static $comment_d;	//Comment

    public static function getInitnation()
    {
        $class = __CLASS__;
        return  self::$obj = !(self::$obj instanceof $class)? new self(): self::$obj;
    }
    
}

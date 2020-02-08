<?php
namespace Common\Logic;
use Common\Model\UserModel;
use Common\Model\FootPrintModel;
use Common\Model\GoodsModel;
use Think\SessionGet;
/**
 * 逻辑处理层
 *
 */
class FootPrintLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new FootPrintModel();
        $this->goodsModel = new GoodsModel();
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'page' => [
                'required' => '必须输入分页信息',
            ],
        ];
        return $message;
    }
    /**
     * 获取结果
     */
    public function getResult()
    {
    
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName() :string
    {
        return FootPrintModel::class;
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
     */
    public function hideenComment() :array
    {
        return [

        ];
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
     */
    public function likeSerachArray() :array
    {
        return [
            UserModel::$userName_d,
        ];
    }

    /**
     * 猜你喜欢逻辑处理
     *
     */
    public function guessLove(){
        $goods = $this->modelObj->getLoveGoods(session('user_id'));
        if (empty($goods)) {
            $maybe_love = $this->goodsModel->getMaybeLoveGoods();
        }else{
            $maybe_love = $goods;
        }
        return $maybe_love;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAdd()
     */
    protected function getParseResultByAdd() :array
    {
    	$data = $this->data;
    	
    	$temp = [];
    	
    	$temp[FootPrintModel::$gid_d] = $data['id'];
    	
    	$temp[FootPrintModel::$uid_d] = SessionGet::getInstance('user_id')->get();
    	
    	$temp[FootPrintModel::$createTime_d] = time();
    	
    	return $temp;
    }
}

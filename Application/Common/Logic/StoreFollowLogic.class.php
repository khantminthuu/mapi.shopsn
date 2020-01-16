<?php
namespace Common\Logic;
use Common\Model\UserModel;
use Common\Model\StoreFollowModel;
use Common\Model\CommonModel;
/**
 * 逻辑处理层
 *
 */
class StoreFollowLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new StoreFollowModel();
      
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'store_id' => [
                'required' => '必须输入店铺ID',
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
        return StoreFollowModel::class;
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
     * 关注店铺
     *
     */
    public function attenStore(){
        //判断是否关注过这个店铺
        $ifExict = $this->modelObj->ifAttenStore(session('user_id'),$this->data['store_id']);
        if ($ifExict === true){
            $this->errorMessage = '已经关注过，请勿重复关注';
            return;
        }
        $data['user_id'] = session('user_id');
        $data['store_id'] = $this->data['store_id'];
        $data['create_time'] = time();
        $data['update_time'] = time();
        $result = $this->modelObj->add($data);
        if ($result){
            // 添加表成功以后在店铺表中  店铺的关注量增加1
            $where['id'] = $this->data['store_id'];
            $collect = CommonModel::store();
            $cresult = $collect->where($where)->setInc("store_collect");
            if ($cresult){
                return $result;
            }
            $this->errorMessage = "操作失败le！";
            return;
        }
        $this->errorMessage = "操作失败！";
        return;
    }

    public function cancelAttenStore(){
        $ifExict = $this->modelObj->ifAttenStore(session('user_id'),$this->data['store_id']);
        if ($ifExict === false){
            $this->errorMessage = '还未关注店铺，不能进行取消操作';
            return;
        }
        $where['user_id'] = session("user_id");
        $where['store_id'] = $this->data['store_id'];
        $result = $this->modelObj->where($where)->delete();
        $wheres['id'] = $this->data['store_id'];

        if ($result){
            $collect = CommonModel::store()->where($wheres)->setDec("store_collect");
            if ($collect){
                return $result;
            }
            $this->errorMessage = "操作失败！";
        }
        $this->errorMessage = "操作失败！";
        return;
    }

   
}

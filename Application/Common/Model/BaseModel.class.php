<?php
declare(strict_types=1);
namespace Common\Model;

use Think\Model;
use Common\TraitClass\MethodModel;
use Common\TraitClass\ModelToolTrait;
use Common\Tool\Event;
use Common\TraitClass\AddFieldTrait;

/**
 * 数据操作 控制
 * @author 王强
 * @version 1.0.2
 */
abstract class BaseModel extends Model
{
    use MethodModel;
    
    use ModelToolTrait;
    
    use AddFieldTrait;
    
    // 数据库字段显示页面 操作【添加、编辑】
    private static $colums = array();

    private static $obj = array();
    

    protected $isOpenTranstion = false;
    
    // 不检测搜索的键
    protected $noValidate;

    protected static $find = 'public static function getInitnation()';

    const DESC = ' DESC ';

    const ASC = ' ASC ';

    const desc = 'desc';
    
    const BETWEEN = ' between ';
    
    const asc = 'asc';

    const DBAS = ' as ';

    const SUFFIX = '_d';
    
    // 是否提交事务
    protected $isCommit = FALSE;

    protected $split;
    
    // where条件
    protected $where;
    // 排序
    protected $order;
    
    // 适用于模糊搜索的键
    protected $buildWhereByKey;
    
    // 搜索时日期的键
    protected $searchCreateTimeKey;


    /**
     * 获取搜索日期的字段
     * @return the $searchCreateTimeKey
     */
    public function getSearchCreateTimeKey()
    {
        return $this->searchCreateTimeKey;
    }

    /**
     * 设置搜索日期的字段
     * @param string $searchCreateTimeKey
     */
    public function setSearchCreateTimeKey($searchCreateTimeKey)
    {
        $this->searchCreateTimeKey = $searchCreateTimeKey;
    }

    /**
     * 获取 模糊搜索的字段
     * @return the $buildWhereByKey
     */
    public function getBuildWhereByKey()
    {
        return $this->buildWhereByKey;
    }

    /**
     * 设置模糊搜索的字段
     * @param field_type $buildWhereByKey
     */
    public function setBuildWhereByKey($buildWhereByKey)
    {
        $this->buildWhereByKey = $buildWhereByKey;
    }

    /**
     * 获取提交事务状态
     * @return the $isCommit
     */
    public function getIsCommit()
    {
        return $this->isCommit;
    }

    /**
     * 设置提交事务状态
     * @param boolean $isCommit
     */
    public function setIsCommit($isCommit)
    {
        $this->isCommit = $isCommit;
    }

    /**
     * @return the $where
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     *
     * @return the $order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * 取得子类的实例【用父类实例化子类】
     */
    public static function getInstance($className)
    {
        if (empty(self::$obj[$className])) {
            self::$obj[$className] = $className::getInitnation();
        }
        return self::$obj[$className];
    }

    /**
     * 去除不查询的字段
     *
     * @param array $fields
     *            要去除查询的字段
     * @return array;
     */
    public function deleteFields(array $fields)
    {
        $fieldsDb = $this->getDbFields();
        if (empty($fields)) {
            return array();
        }
        foreach ($fieldsDb as $key => $name) {
            if (in_array($name, $fields)) {
                unset($fieldsDb[$key]);
            }
        }
        return $fieldsDb;
    }

    /**
     * 重写添加
     * {@inheritDoc}
     * @see \Think\Model::add()
     */
    public function add($data = '', $options = array(), $replace = false)
    {
        if (empty($data)) {
            return false;
        }
        $data = $this->create($data);

        $insertId = parent::add($data, $options, $replace);
        
        return $insertId;
    }

    /**
     * save 保存 更新
     * {@inheritDoc}
     * @see \Think\Model::save()
     */
    public function save($data = '', $options = array()) :bool
    {
        if (empty($data)) {
            return false;
        }
        $data = $this->create($data);
        
        // 更新数据
        $status = parent::save($data, $options);
        
        return $status;
    }

    /**
     * 查看事务
     * @return array
     * @author 王强<2272597637@qq.com>
     */
    protected function currentTranstation()
    {
        return $this->query('SHOW ENGINE INNODB STATUS');
    }

    /**
     * 查看是否有事务
     * @return boolean [false 没有 TRUE 有]
     */
    public function isHaveTranstation()
    {
        $data = $this->currentTranstation();
        return empty($data) ? false : true;
    }

    /**
     * 获取数据表全部表注释 并缓存
     * @param string $key 表名
     * @return array|string
     */
    public function getAllTableNotes($key = null)
    {
        $notes = S('Table_NOTES');
        
        if (! empty($notes)) {
            return empty($key) ? $notes : $notes[$key];
        }
        
        $data = $this->query('SELECT TABLE_COMMENT,TABLE_NAME FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = "' . C('DB_NAME') . '"');
        
        if (empty($data)) {
            return null;
        }
        $notes = array();
        foreach ($data as $name => $value) {
            $notes[$value['table_name']] = $value['table_comment'];
        }
        
        S('Table_NOTES', $notes, 800);
        
        return empty($key) ? $notes : $notes[$key];
    }


    /**
     * 事务添加
     */
    public function addTranstaion(array $post)
    {
        if (! $this->isEmpty($post)) {
            return false;
        }
        
        $this->startTrans();
        
        $status = $this->add($post);
        
        if (! $this->traceStation($status)) {
            return false;
        }
        
        if ($this->isCommit) {
            $this->commit();
        }
        
        return $status;
    }

    /**
     * 事务消息
     */
    public function traceStation($status, $message = '更新失败') :bool
    {
        if ($status === false) {
            $this->rollback();
            $this->error = $message;
            return false;
        }
        return true;
    }

    /**
     * 获取表 字段信息
     */
    public function getColum() :array
    {
        $table = $this->getTableName();
        if (! empty(self::$colums[$table])) {
            return self::$colums[$table];
        }
        
        $filed = 'COLUMN_NAME, DATA_TYPE, COLUMN_COMMENT';
        Event::listen('colum_info', $filed); // 扩展事件
        
        self::$colums[$table] = $this->query('select ' . $filed . ' from information_schema.`COLUMNS` where TABLE_SCHEMA="' . C('DB_NAME') . '"  and TABLE_NAME="' . $table . '"');
        
        return self::$colums[$table];
    }


    /**
     * 重组字段信息
     */
    public function buildColumArray(array $hidden)
    {
        if (! $this->isEmpty($hidden)) {
            return array();
        }
        
        $colum = $this->getColum();
        
        if (! $this->isEmpty($colum)) {
            return array();
        }
        $parseArray = array();
        
        foreach ($colum as $key => &$value) {
            if (in_array($value['column_name'], $hidden, true) || $value['data_type'] === 'tinyint') {
                unset($colum[$key]);
            }
            if (false !== ($start = mb_strpos($value['column_comment'], '【'))) {
                $start = mb_strpos($value['column_comment'], '【');
                $value['column_comment'] = mb_substr($value['column_comment'], 0, $start);
            }
        }
        return $colum;
    }

    /**
     * 获取统计数据
     */
    public function getAnalysis(array $data, $field)
    {
        if (! $this->isEmpty($data) || ! in_array($field, $this->getDbFields(), true)) {
            return array();
        }
        return $this->where(static::$id_d . ' in (' . implode(',', array_values($data)) . ')')->getField(static::$id_d . ',' . $field);
    }

    /**
     * 重写 构造方法
     */
    public function __construct($name = '', $tablePrefix = '', $connection = '')
    {
        parent::__construct($name, $tablePrefix, $connection);
        
        // 实现自动添加代码[静态属性]
        $this->autoAddProp();
        // 数据字段赋值 【用父类 实例化子类】$this 代指 子类的实例
        $this->setDbFileds();
    }

    /**
     * 获取当天 操作数据量
     */
    public function getTodayDataNumber ()
    {
        $today = date('Y-m-d', time());
    
        $start = $today.' 00:00:00';
    
        $end  = $today.' 23:59:59';
    
        $count = $this->where( static::$createTime_d.self::BETWEEN.' UNIX_TIMESTAMP("'.$start.'") and UNIX_TIMESTAMP("'.$end.'")')->count();
    
        return $count;
    }
    

    /**
     * 生成单号
     */
    protected function toGUID()
    {   //订购日期
        //订单号码主体（YYYYMMDDHHIISSNNNNNNNN）
        $orderIdMain = date('YmdHis') . rand(10000000, 99999999);
        //订单号码主体长度
        $orderIdLen = strlen($orderIdMain);
        $orderIdSum = 0;
        for ($i = 0; $i < $orderIdLen; $i++) {
            $orderIdSum += (int)(substr($orderIdMain, $i, 1));
        }
        //唯一订单号码（YYYYMMDDHHIISSNNNNNNNNCC）
        $orderId = $orderIdMain . str_pad((100 - $orderIdSum % 100) % 100, 2, '0', STR_PAD_LEFT);
        return $orderId;
    }
}
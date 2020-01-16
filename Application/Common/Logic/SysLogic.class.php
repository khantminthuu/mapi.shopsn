<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------
declare(strict_types = 1);
namespace Common\Logic;

use Common\Logic\AbstractGetDataLogic;
use Common\Model\SystemConfigModel;
use Common\Tool\Tool;
use Common\Tool\Extend\UnlinkPicture;
use Think\Cache;

/**
 * 系统配置
 * @author Administrator
 */
class SysLogic extends AbstractGetDataLogic
{
    private $initURL = null;
    
    private $logoPath  = null;
    /**
     * 架构方法
     */
    public function __construct(array $data = [], $splitKey = null)
    {
        $this->data = $data;
        
        
        $this->modelObj = SystemConfigModel::getInitnation();
        
        $this->splitKey = $splitKey;
    }
    
    /**
     * @param 设置网站url $initURL
     */
    public function setInitURL($initURL)
    {
        $this->initURL = $initURL;
    }
    
    /**
     * @param field_type $qrPath
     */
    public function setLogoPath($qrPath)
    {
        $this->logoPath = $qrPath;
    }
    
    
    /**
     * 获取商品分类
     *
     * {@inheritdoc}
     *
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
    }
    
    
    /**
     * 返回模型类名
     *
     * @return string
     */
    public function getModelClassName() :string
    {
        return SystemConfigModel::class;
    }
    
    
    public function getAllConfig()
    {
        $data = $this->modelObj->field('create_time,update_time', true)->select();
        
        if (empty($data))
        {
           return [];
        }
        
        foreach ($data as $key => &$value)
        {
            if (!empty($value['config_value']))
            {
                $unData = unserialize($value['config_value']);
                unset($data[$key]['config_value']);
                $value = array_merge($value, $unData);
            }
        }
        
        return $data;
    }
    
    /**
     * 获取无缓存组配置
     * @return mixed|NULL|unknown|string[]|unknown[]|object
     */
    public function getNoCacheConfigByGroup()
    {
        $field = SystemConfigModel::$id_d.','.SystemConfigModel::$configValue_d.','.SystemConfigModel::$classId_d.','.SystemConfigModel::$key_d.','.SystemConfigModel::$currentId_d;
        
        $data = $this->modelObj->where(SystemConfigModel::$parentKey_d .' = "%s"', $this->data['key'])->getField($field);
        
        return $data;
    }
    
    /**
     * 获取无缓存具体配置
     * @return mixed|NULL|unknown|string[]|unknown[]|object
     */
    public function getDetailCacheConfig()
    {
        $field = SystemConfigModel::$configValue_d;
      
        $data = $this->modelObj->where('`'.SystemConfigModel::$key_d .'` = "%s"', $this->data['key'])->getField($field);
        return $data;
    }
    
    
    
    /**
     * @desc 获取具体的有缓存的配置
     * @return array
     */
    public function getConfigByDetailKey()
    {
        if (empty($this->data['key'])) {
            return [];
        }
        
        $cacheKey = 'eef'.$this->data['key'];
        
        $cacheObj = Cache::getInstance('', ['expire' => 200]);
        
        $data = $cacheObj->get($cacheKey);
        
        if (empty($data)) {
            $data = $this->getDetailCacheConfig();
        } else {
            return $data;
        }
        
        if (empty($data)) {
            return array();
        }
        
        $cacheObj->set($cacheKey, $data);
        
        return $data;
    }
    
    
    /**
     * 转换配置(有缓存)
     */
    public function covertMapByNoCacheConfig()
    {
        $data = $this->getDataByKey();
        
        if (empty($data)) {
            return [];
        }
        
        $tmp = [];
        
        foreach ($data as $key => $value) {
            $tmp[$value[SystemConfigModel::$key_d]] = $value[SystemConfigModel::$configValue_d];
        }
        return $tmp;
    }
    
    
    
    /**
     * @desc 依据某个键 获取 子集
     * @return array
     */
    public function getDataByKey()
    {
        if (empty($this->data['key'])) {
            return [];
        }
        
        $cacheKey = 'ssd'.$this->data['key'];
    
        $cacheObj = Cache::getInstance('', ['expire' => 200]);
        
        $data = $cacheObj->get($cacheKey);
    
        if (empty($data)) {
            $data = $this->getNoCacheConfigByGroup();
       } else {
           return $data;
       }
       
       if (empty($data)) {
           return array();
       }
       
       $cacheObj->set($cacheKey, $data);
       
       return $data;
    }
    
    
    /**
     * 转换配置(有缓存)
     */
    public function covertMapByConfig()
    {
        $data = $this->getDataByKey();
        
        if (empty($data)) {
            return [];
        }
        
        $tmp = [];
        
        foreach ($data as $key => $value) {
            $tmp[$value[SystemConfigModel::$key_d]] = $value[SystemConfigModel::$configValue_d];
        }
        return $tmp;
    }
    
    /**
     * 保存配置
     * @param array $data
     * @return boolean
     */
    public function saveData()
    {
        $data = $this->data;
      
        if (empty($data[SystemConfigModel::$classId_d]))
        {
            return false;
        }
    
        if (!empty($data['logo_name']) && $this->logoPath !== $data['logo_name']) {
            Tool::partten($this->logoPath, UnlinkPicture::class);
        }
    
        //生成二维码图片
        $data = $this->buildQrCode();
    
        $classId = $data[SystemConfigModel::$classId_d];
    
        $parentKey =  $data[SystemConfigModel::$parentKey_d];
        
        $sysId = $data['sc_id'];
        $currentId = $data['current_id'];
        
        unset($data['sc_id'], $data[SystemConfigModel::$classId_d], $data['current_id'], $data[SystemConfigModel::$parentKey_d]);
    
        try {
            
            $isHave = $this->modelObj
                ->field([SystemConfigModel::$createTime_d, SystemConfigModel::$updateTime_d], true)
                ->where(SystemConfigModel::$id_d.' in (%s)', implode(',', $sysId))
                ->select();
            if (empty($isHave)) {
                
                $tempData = [];
                
                $i = 0;
                
                $time = time();
                
                foreach ($data as $key => & $value) {
                    
                    if (date('Y-m-d H:i:s', strtotime($value)) === $value) {
                        $value = strtotime($value);
                    }
                    
                    $tempData[$i] = [];
                    
                    $tempData[$i][SystemConfigModel::$key_d] = $key;
                    
                    $tempData[$i][SystemConfigModel::$configValue_d] = $value;
                    
                    $tempData[$i][SystemConfigModel::$classId_d] = $classId;
                    
                    $tempData[$i][SystemConfigModel::$currentId_d] = $currentId[$i];
                    
                    $tempData[$i][SystemConfigModel::$createTime_d] = $time;
                    $tempData[$i][SystemConfigModel::$updateTime_d] = $time;
                    
                    $tempData[$i][SystemConfigModel::$parentKey_d] = $parentKey;
                    
                    $i++;
                }
                $isSuccess = $this->modelObj->addAll($tempData);
            } else {
                
                $sql = $this->buildUpdateSql();
                $isSuccess =  $this->modelObj->execute($sql);
            }
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            
            return false;
        }
        return $isSuccess;
    }
    
    /**
     * 要更新的字段
     * @return array
     */
    protected function getColumToBeUpdated() :array
    {
        return [
            SystemConfigModel::$configValue_d,
            SystemConfigModel::$classId_d,
            SystemConfigModel::$currentId_d
        ];
    }
    
    /**
     * 要更新的数据【已经解析好的】
     * @return array
     */
    protected function getDataToBeUpdated() :array
    {
        $data = $this->data;
        
        $classId = $data[SystemConfigModel::$classId_d];
        
        $parentKey =  $data[SystemConfigModel::$parentKey_d];
        
        $sysId = $data['sc_id'];
        $currentId = $data['current_id'];
        
        unset($data['sc_id'], $data[SystemConfigModel::$classId_d], $data['current_id'], $data[SystemConfigModel::$parentKey_d]);
        
        $tmp = [];
        
        $i = 0;
        
        foreach ($data as $key => $value) {
            
            $tmp[$sysId[$i]][] = $value;
            
            $tmp[$sysId[$i]][] = $classId;
            
            $tmp[$sysId[$i]][] = $currentId[$i];
            
            $i++;
        }
        
        return $tmp;
    }
    
    /**
     * 生成二维码图片
     */
    protected function buildQrCode()
    {
    
        $post = $this->data;
        
        if (empty($post['internet_url'])) {
            return $post;
        }
    
        if ( $post['internet_url'] === $this->initURL) {
            return $post;
        }
    
        $url = false !== strpos($post['internet_url'], 'http://') ? $post['internet_url'] : 'http://'.$post['internet_url'];
        include_once  COMMON_PATH.'Tool/QRcode.class.php';
        $path = C('qr_image').time().rand(0, 100000).'.png';
    
        $status = \QRcode::png($url, $path, QR_ECLEVEL_H, 4);
        
        Tool::partten($post['init_qr_code'], UnlinkPicture::class);
    
        $this->addWater($path);
        $post['init_qr_code'] = substr($path, 1);
        return $post;
    }
    /**
     * 添加水印
     * @param unknown $path
     */
    private function addWater ($path)
    {
        $QR = imagecreatefromstring(file_get_contents($path));
        $logo = imagecreatefromstring(file_get_contents(C('water')));
        $QR_width = imagesx($QR);//二维码图片宽度
        $QR_height = imagesy($QR);//二维码图片高度
        $logo_width = imagesx($logo);//logo图片宽度
        $logo_height = imagesy($logo);//logo图片高度
        $logo_qr_width = $QR_width / 5;
        $scale = $logo_width/$logo_qr_width;
        $logo_qr_height = $logo_height/$scale;
        $from_width = ($QR_width - $logo_qr_width) / 2;
        //重新组合图片并调整大小
        imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
            $logo_qr_height, $logo_width, $logo_height);
    
        //输出图片
        imagepng($QR, $path);
    }
    
    
    /**
     * 获取配置值
     * @param array $options 条件
     * @return array
     */
    public function getValue()
    {
        $field = [
            SystemConfigModel::$id_d.' as c_id',
            SystemConfigModel::$classId_d,
            SystemConfigModel::$configValue_d,
            SystemConfigModel::$key_d,
            SystemConfigModel::$parentKey_d,
            SystemConfigModel::$currentId_d
        ];
        
        $data = $this->modelObj->field($field)->select();
        
        return $data;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice()
     */
    public function getMessageNotice() :array
    {
        return [
            SystemConfigModel::$classId_d => [
                'number' => '分类必须是数字'
            ]
        ];
    }
}
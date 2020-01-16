<?php
declare(strict_types = 1);
namespace Common\TraitClass;

use Common\Logic\SysLogic;

/**
 * 获取系统配置组件
 * @author Administrator
 */
trait GETConfigTrait
{
    protected $key = null;
    /**
     * 获取系统配置
     */
    public function getConfig($key) :array
    {
        $data = (new SysLogic(['key' => $key]))->getConfigByDetailKey();
        return $data;
    }
    
    /**
     * 获取无缓存具体配置
     * @return array
     */
    protected  function getNoCacheConfig ($key)
    {
        $data = (new SysLogic(['key' => $key]))->getDetailCacheConfig();
        
        return $data;
    }
    
    /**
     * 获取组数据 配置
     */
    protected function getGroupConfig () :array
    {
        if (empty($this->key)) {
            return array();
        }
        $groupConfig = (new SysLogic(['key' => $this->key]))->covertMapByConfig();
      
        
        return $groupConfig;
    }
    
    /**
     * 获取网站信息
     */
    public function getIntnetInformation()
    {
        //获取组配置
        $this->key = 'information_by_intnet';
        
        $information = $this->getGroupConfig();
        
        return $information;
    }
    
    public function get_intnetConfig()
    {
        //获取组配置
        $this->key = 'intnetConfig';
        
        $information = $this->getGroupConfig();
        
        return $information;
    }
}
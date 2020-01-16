<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <13052079525>
// +----------------------------------------------------------------------
// |简单与丰富！让外表简单一点，内涵就会更丰富一点。
// +----------------------------------------------------------------------
// |让需求简单一点，心灵就会更丰富一点。
// +----------------------------------------------------------------------
// |让言语简单一点，沟通就会更丰富一点。
// +----------------------------------------------------------------------
// |让私心简单一点，友情就会更丰富一点。
// +----------------------------------------------------------------------
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Common\TraitClass;

trait RechargeCommonTrait 
{
    private $checkURL;
    
    private $info = []; //商品信息数组
    
    private $nofityURL = '';
    
    /**
     * 类型【0 商品支付 1 余额充值】
     * @var int
     */
    private $type;
    
    /**
     * @return the $info
     */
    public function getInfo()
    {
        return $this->info;
    }
    
    
    /**
     * @return the $type
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * @return the $checkURL
     */
    public function getCheckURL()
    {
        return $this->checkURL;
    }
    
    /**
     * @return the $nofityURL
     */
    public function getNofityURL()
    {
        return $this->nofityURL;
    }
    
    /**
     * @param string $checkURL
     */
    public function setCheckURL($checkURL)
    {
        $this->checkURL = $checkURL;
    }
    
    /**
     * @param string $nofityURL
     */
    public function setNofityURL($nofityURL)
    {
        $this->nofityURL = $nofityURL;
    }
}
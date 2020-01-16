<?php
declare(strict_types = 1);
namespace Validate;

/**
 * 检查参数接口
 * @author Administrator
 *
 */
interface Validate 
{
    /**
     * 检测参数
     */
    public function check() :bool;
}


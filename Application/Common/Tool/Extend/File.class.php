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

namespace Common\Tool\Extend;

use Common\Tool\Tool;

/**
 * 文件操作
 * @author 王强
 * @version 1.0.1
 */
class File extends Tool
{
    /**
     * 处理图片上传错误
     * @param array $files
     * @param string $setKey
     * @return array
     */
    public function parseFile(array $files, $setKey = 'tmp_name')
    {
        if (empty($files)) {
            return false;
        }

        /* 一个图片时 */
        foreach ($files as $key => &$value) {
            if (empty($value[$setKey])) {
                continue;
            }
            $value[$setKey] = stripcslashes($value[$setKey]);
        }

        return $files;
    }
    
    // 读一级目录
    public function readOne($path)
    {
        if (! is_dir($path) || ! ($dh = opendir($path))) {
            return array();
        }
        $fileArray = array();
        
        while (($file = readdir($dh)) !== false) {
            $fileArray[$file] = $file;
        }
        
        closedir($dh);
        
        return $fileArray;
    }

    function readAveryWhere($path, &$data)
    {
        if (is_dir($path)) {
            $dp = dir($path);
            while ($file = $dp->read()) {
                if ($file != '.' && $file != '..') {
                    self::readAveryWhere($path . '/' . $file, $data);
                }
            }
            $dp->close();
        }
        if (is_file($path)) {
            $data[] = $path;
        }
        
        return $data;
    }

    /**
     * 功能：循环检测并创建文件夹
     * 
     * @param string $path
     *            文件夹路径
     *            返回：
     */
    public function createDir($path)
    {
        if (file_exists($path)) {
            return false;
        }
        
        $this->createDir(dirname($path));
        return mkdir($path, 0777);
    }
}
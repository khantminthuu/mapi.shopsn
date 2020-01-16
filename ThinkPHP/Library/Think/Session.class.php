<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Think;

class Session
{
    /**
     * 前缀
     * @var string
     */
    protected $prefix = '';

    /**
     * 是否初始化
     * @var bool
     */
    protected $init = null;

    
    /**
     * @var array
     */
    private $config = [];
    
    /**
     * 设置或者获取session作用域（前缀）
     * @access public
     * @param  string $prefix
     * @return string|void
     */
    public function prefix($prefix = '')
    {
        empty($this->init) && $this->boot();

        if (empty($prefix) && null !== $prefix) {
            return $this->prefix;
        } else {
            $this->prefix = $prefix;
        }
    }

    /**
     * 架构方法
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($config, $this->config);
    }
    

    /**
     * session初始化
     * @access public
     * @param  array $config
     * @return void
     */
    public function init()
    {
        $config = $this->config;
        
        // 记录初始化信息
        $isDoStart = false;
        if (isset($config['use_trans_sid'])) {
            ini_set('session.use_trans_sid', $config['use_trans_sid'] ? 1 : 0);
        }
        
        // 启动session
        if (!empty($config['auto_start']) && PHP_SESSION_ACTIVE != session_status()) {
            ini_set('session.auto_start', 0);
            $isDoStart = true;
        }
        
        if (isset($config['prefix'])) {
            $this->prefix = $config['prefix'];
        }
        
        if (isset($config['var_session_id']) && isset($_REQUEST[$config['var_session_id']])) {
            session_id($_REQUEST[$config['var_session_id']]);
        } elseif (isset($config['id']) && !empty($config['id'])) {
            session_id($config['id']);
        }
        
        if (isset($config['name'])) {
            session_name($config['name']);
        }
        
        if (isset($config['path'])) {
            session_save_path($config['path']);
        }
        
        if (isset($config['domain'])) {
        	
            ini_set('session.cookie_domain', $config['domain']);
        }
        
        if (isset($config['expire'])) {
            ini_set('session.gc_maxlifetime', $config['expire']);
            ini_set('session.cookie_lifetime', $config['expire']);
        }
        
        if (isset($config['secure'])) {
            ini_set('session.cookie_secure', $config['secure']);
        }
        
        if (isset($config['httponly'])) {
            ini_set('session.cookie_httponly', $config['httponly']);
        }
        
        if (isset($config['use_cookies'])) {
            ini_set('session.use_cookies', $config['use_cookies'] ? 1 : 0);
        }
        
        if (isset($config['cache_limiter'])) {
            session_cache_limiter($config['cache_limiter']);
        }
        
        if (isset($config['cache_expire'])) {
            session_cache_expire($config['cache_expire']);
        }
        
        $type = C('SESSION_TYPE');
        
        if (!empty($type)) {
            // 读取session驱动
            $class = false !== strpos($type, '\\') ? $config['type'] : '\\Think\\Session\\Driver\\' . ucwords($type);
        
            // 检查驱动类
            if (!class_exists($class) || !session_set_save_handler(new $class($config))) {
                throw new \Exception('error session handler:' . $class);
            }
        }
        if ($isDoStart) {
            session_start();
            $this->init = true;
        } else {
            $this->init = false;
        }
    }
    
   
}

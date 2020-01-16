<?php
declare(strict_types=1);
namespace Think;

class SessionGet
{
    /**
     * 键名
     * @var string
     */
    private $name;
    
    /**
     * 键值
     * @var mixed
     */
    private $value;
    
    /**
     * 前缀
     * @var string
     */
    private $prefix = '';
    
    /**
     * 是否开启锁机制
     * @var string
     */
    private $lock = false;
    
    /**
     * 锁驱动
     * @var object
     */
    protected $lockDriver = null;
    
    /**
     * 锁key
     * @var string
     */
    protected $sessKey = 'PHPSESSID';
    
    /**
     * 锁超时时间
     * @var integer
     */
    protected $lockTimeout = 3;
    
    /**
     * 
     * @var SessionGet
     */
    private static $sessionObj;
    
    
    /**
     * 设置名称
     */
    public function setName($name) {
    	$this->name = $name;
    }
    
    public function getName() {
    	return $this->name;
    }
    
    /**
     * 设置值
     */
    public function setValue($value) {
    	$this->value = $value;
    }
    
    public function getValue() {
    	return $this->value;
    }
    
    /**
     * 设置前缀
     */
    public function setPrefix($prefix) {
    	$this->prefix = $prefix;
    }
    
    public function getPrefix() {
    	return $this->prefix;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @param string $prefix
     */
    private function __construct(string $name, $value = null, $prefix = NULL)
    {
        $this->name = $name;
        
        $this->value = $value;
        
        $this->prefix = $prefix;
        
        $sessionOptions = C('SESSION_OPTIONS');
        
        $this->lock = $sessionOptions['use_lock'];
        
        $sessKey = $sessionOptions['name'];
       
        $this->sessKey = $sessKey === null ? $this->sessKey : $sessKey;
    }
    
    /**
     * 实例对象
     * @return $this
     */
    public static function getInstance(string $name, $value = null, $prefix = NULL): self
    {
    	if (!(static::$sessionObj instanceof SessionGet)) {
    		static::$sessionObj = new static($name, $value, $prefix);
    	} else {
    		static::$sessionObj->setName($name);
    		static::$sessionObj->setValue($value);
    		static::$sessionObj->setPrefix($prefix);
    	}
    	
    	return static::$sessionObj;
    }
    
    /**
     * session自动启动或者初始化
     * @access public
     * @return void
     */
    public function boot() :void
    {
        if (PHP_SESSION_ACTIVE != session_status()) {
            session_start();
        }
    }
    
    /**
     * session设置
     * @access public
     * @return void
     */
    public function set() :void
    {
        $this->lock(); // lock必须先于 $this->boot()
    
        $this->boot();
    
        $name = $this->name;
        $value = $this->value;
        $prefix = $this->prefix;
        if (strpos($name, '.')) {
            // 二维数组赋值
            list($name1, $name2) = explode('.', $name);
            if ($prefix) {
                $_SESSION[$prefix][$name1][$name2] = $value;
            } else {
                $_SESSION[$name1][$name2] = $value;
            }
        } elseif ($prefix) {
           
            $_SESSION[$prefix][$name] = $value;
            
        } else {
          
            $_SESSION[$name] = $value;
        }
    
        
        $this->unlock();
    }
    
    /**
     * session获取
     * @access public
     * @param  string        $name session名称
     * @param  string|null   $prefix 作用域（前缀）
     * @return mixed
     */
    public function get() 
    {
        $this->lock(); // lock必须先于 $this->boot()
    
        $this->boot();
        
        $name = $this->name;
        
        $prefix = $this->prefix;
        if ('' == $name) {
            // 获取全部的session
            $value = $prefix ? (!empty($_SESSION[$prefix]) ? $_SESSION[$prefix] : []) : $_SESSION;
        } elseif ($prefix) {
            
            // 获取session
            if (strpos($name, '.')) {
                list($name1, $name2) = explode('.', $name);
                
                $value               = isset($_SESSION[$prefix][$name1][$name2]) ? $_SESSION[$prefix][$name1][$name2] : null;
            } else {
                $value = isset($_SESSION[$prefix][$name]) ? $_SESSION[$prefix][$name] : null;
            }
        } else {
            if (strpos($name, '.')) {
                list($name1, $name2) = explode('.', $name);
                $value               = isset($_SESSION[$name1][$name2]) ? $_SESSION[$name1][$name2] : null;
            } else {
                $value = isset($_SESSION[$name]) ? $_SESSION[$name] : null;
            }
        }
        $this->unlock();
        return $value;
    }
    
    /**
     * session 读写锁驱动实例化
     */
    protected function initDriver() :void
    {
        // 不在 init 方法中实例化lockDriver，是因为 init 方法不一定先于 set 或 get 方法调用
        $config = C('SESSION_OPTIONS');
        if (!empty($config['type']) && isset($config['use_lock']) && $config['use_lock']) {
            // 读取session驱动
            $class = false !== strpos($config['type'], '\\') ? $config['type'] : '\\Think\\Session\\Driver\\' . ucwords($config['type']);
    
            // 检查驱动类及类中是否存在 lock 和 unlock 函数
            if (class_exists($class) && method_exists($class, 'lock') && method_exists($class, 'unlock')) {
                $this->lockDriver = new $class($config);
            }
        }
        // 通过cookie获得session_id
        if (isset($config['name']) && $config['name']) {
            $this->sessKey = $config['name'];
        }
        if (isset($config['lock_timeout']) && $config['lock_timeout'] > 0) {
            $this->lockTimeout = $config['lock_timeout'];
        }
    }
    
    /**
     * session 读写加锁
     * @access protected
     * @return void
     */
    protected function lock() :void
    {
    	
    	if ($this->lock === false) {
    		return;
    	}
    	
        $this->initDriver();
    
        if (null !== $this->lockDriver && method_exists($this->lockDriver, 'lock')) {
            $t = time();
            // 使用 session_id 作为互斥条件，即只对同一 session_id 的会话互斥。第一次请求没有 session_id
            $sessID = isset($_COOKIE[$this->sessKey]) ? $_COOKIE[$this->sessKey] : '';
            do {
                if (time() - $t > $this->lockTimeout) {
                    $this->unlock();
                }
            } while (!$this->lockDriver->lock($sessID, $this->lockTimeout));
        }
    }
    
    /**
     * session 读写解锁
     * @access protected
     * @return void
     */
    protected function unlock() :void
    {
    	if ($this->lock === false) {
    		return;
    	}
    	
        if (empty($this->lockDriver) ||  !method_exists($this->lockDriver, 'unlock')) {
           return ;
        }
        
        $this->pause();
        
        $sessID = isset($_COOKIE[$this->sessKey]) ? $_COOKIE[$this->sessKey] : '';
        
        $this->lockDriver->unlock($sessID);
    }
    
    /**
     * session获取并删除
     * @access public
     * @param  string        $name session名称
     * @param  string|null   $prefix 作用域（前缀）
     * @return mixed
     */
    public function pull(string $name, $prefix = null) 
    {
        $result = $this->get($name, $prefix);
    
        if ($result) {
            $this->delete($name, $prefix);
            return $result;
        } else {
            return;
        }
    }
    
    /**
     * session设置 下一次请求有效
     * @access public
     * @param  string        $name session名称
     * @param  mixed         $value session值
     * @param  string|null   $prefix 作用域（前缀）
     * @return void
     */
    public function flash(string $name, $value) :void
    {
        $this->set($name, $value);
    
        if (!$this->has('__flash__.__time__')) {
            $this->set('__flash__.__time__', $_SERVER['REQUEST_TIME_FLOAT']);
        }
    
        $this->push('__flash__', $name);
    }
    
    /**
     * 清空当前请求的session数据
     * @access public
     * @return void
     */
    public function flush() :void
    {
            
        $this->boot();
        
        $item = $this->get('__flash__');

        if (!empty($item)) {
            $time = $item['__time__'];

            if ($_SERVER['REQUEST_TIME_FLOAT'] > $time) {
                unset($item['__time__']);
                $this->delete($item);
                $this->set('__flash__', []);
            }
        }
    }
    
    /**
     * 删除session数据
     * @access public
     * @param  string|array  $name session名称
     * @return void
     */
    public function delete() :void
    {
        $this->boot();
        
        $prefix = $this->prefix;
        
        $name = $this->name;
        
        if (is_array($name)) {
            foreach ($name as $key) {
                $this->delete($key, $prefix);
            }
        } elseif (strpos($name, '.')) {
            list($name1, $name2) = explode('.', $name);
            if ($prefix) {
                unset($_SESSION[$prefix][$name1][$name2]);
            } else {
                unset($_SESSION[$name1][$name2]);
            }
        } else {
            if ($prefix) {
                unset($_SESSION[$prefix][$name]);
            } else {
                unset($_SESSION[$name]);
            }
        }
    }
    
    /**
     * 清空session数据
     * @access public
     * @param  string|null   $prefix 作用域（前缀）
     * @return void
     */
    public function clear() :void
    {
        $this->boot();
        $prefix = $this->prefix;
        if ($prefix) {
            unset($_SESSION[$prefix]);
        } elseif (isset($_SESSION[$this->name])) {
        	unset($_SESSION[$this->name]);
        } 
    }
    
    /**
     * 清空session数据
     * @access public
     * @param  string|null   $prefix 作用域（前缀）
     * @return void
     */
    public function clearAll() :void
    {
    	$this->boot();
    	
    	$_SESSION = [];
    }
    
    /**
     * 判断session数据
     * @access public
     * @param  string        $name session名称
     * @return bool
     */
    public function has(string $name) :bool
    {
        $this->boot();
        $prefix = $this->prefix;
    
        if (strpos($name, '.')) {
            // 支持数组
            list($name1, $name2) = explode('.', $name);
    
            return $prefix ? isset($_SESSION[$prefix][$name1][$name2]) : isset($_SESSION[$name1][$name2]);
        } else {
            return $prefix ? isset($_SESSION[$prefix][$name]) : isset($_SESSION[$name]);
        }
    }
    
    /**
     * 添加数据到一个session数组
     * @access public
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function push(string $key, $value) :void
    {
        $array = $this->get($key);
    
        if (is_null($array)) {
            $array = [];
        }
    
        $array[] = $value;
    
        $this->set($key, $array);
    }
    
    /**
     * 销毁session
     * @access public
     * @return void
     */
    public function destroy() :void
    {
    	$this->boot();
        if (!empty($_SESSION)) {
            $_SESSION = [];
        }
    	
        
        session_unset();
        session_destroy();
    
        $this->lockDriver = null;
    }
    
    /**
     * 重新生成session_id
     * @access public
     * @param  bool $delete 是否删除关联会话文件
     * @return void
     */
    public function regenerate(bool $delete = false) :void
    {
        session_regenerate_id($delete);
    }
    
    /**
     * 暂停session
     * @access public
     * @return void
     */
    private function pause() :void
    {
        // 暂停session
        session_write_close();
    
        $this->init = false;
    }
}
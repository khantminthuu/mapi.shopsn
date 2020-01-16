<?php
namespace Common\TraitClass;
/**
 *
 * CURL工具
 */

trait CurlTrait {
    private static $_ch;
    private static $_header;
    private static $_body;

    private static $_cookie = array();
    private static $_options = array();
    private static $_url = array ();
    private static $_referer = array ();

    private $method = [
        'get'  => '_httpGet',
        'post' => '_httpPost'
    ];
    /**
     * 调用外部url
     * @param $queryUrl
     * @param $param 参数
     * @param string $method
     * @return bool|mixed
     */
    public function requestWeb($queryUrl, $param='', $is_json=true, $is_urlcode=true) {
        if (empty($queryUrl)) {
            return false;
        }
        $ret = '';
        $param = empty($param) ? array() : $param;
        self::_init();
        $param = strtolower($_SERVER['REQUEST_METHOD']);
        $fun = $this->method[$param];
        $ret = self::$fun($queryUrl, $param, $is_urlcode);
        if($is_json){
            $data =  json_decode($ret, true);
            return $data;
        }

        return $ret;
    }

    private static function _init() {
        self::$_ch = curl_init();

        curl_setopt(self::$_ch, CURLOPT_HEADER, true);
        curl_setopt(self::$_ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt(self::$_ch, CURLOPT_FRESH_CONNECT, true);
    }

    public static function setOption($optArray=array()) {
        foreach($optArray as $opt) {
            curl_setopt(self::$_ch, $opt['key'], $opt['value']);
        }
    }

    private static function _close() {
        if (is_resource(self::$_ch)) {
            curl_close(self::$_ch);
        }

        return true;
    }

    private static function _httpGet($url, $query=array()) {

        if (!empty($query)) {
            $url .= (strpos($url, '?') === false) ? '?' : '&';
            $url .= is_array($query) ? http_build_query($query) : $query;
        }

        curl_setopt(self::$_ch, CURLOPT_URL, $url);
        curl_setopt(self::$_ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(self::$_ch, CURLOPT_HEADER, 0);
        curl_setopt(self::$_ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt(self::$_ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt(self::$_ch, CURLOPT_SSLVERSION, 1);

        $ret = self::_execute();
        self::_close();
        return $ret;
    }

    private static function _httpPost($url, $query=array(), $is_urlcode=true) {
        if (is_array($query)) {
            foreach ($query as $key => $val) {
                if($is_urlcode){
                    $encode_key = urlencode($key);
                }else{
                    $encode_key = $key;
                }
                if ($encode_key != $key) {
                    unset($query[$key]);
                }
                if($is_urlcode){
                    $query[$encode_key] = urlencode($val);
                }else{
                    $query[$encode_key] = $val;
                }

            }
        }
        curl_setopt(self::$_ch, CURLOPT_URL, $url);
        curl_setopt(self::$_ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(self::$_ch, CURLOPT_HEADER, 0);
        curl_setopt(self::$_ch, CURLOPT_POST, true );
        curl_setopt(self::$_ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt(self::$_ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt(self::$_ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt(self::$_ch, CURLOPT_SSLVERSION, 1);


        $ret = self::_execute();
        self::_close();
        return $ret;
    }

    private static function _put($url, $query = array()) {
        curl_setopt(self::$_ch, CURLOPT_CUSTOMREQUEST, 'PUT');

        return self::_httpPost($url, $query);
    }

    private static function _delete($url, $query = array()) {
        curl_setopt(self::$_ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        return self::_httpPost($url, $query);
    }

    private static function _head($url, $query = array()) {
        curl_setopt(self::$_ch, CURLOPT_CUSTOMREQUEST, 'HEAD');

        return self::_httpPost($url, $query);
    }

    private static function _execute() {
        $response = curl_exec(self::$_ch);
        $errno = curl_errno(self::$_ch);

        if ($errno > 0) {
            throw new \Exception(curl_error(self::$_ch), $errno);
        }
        return  $response;
    }
}

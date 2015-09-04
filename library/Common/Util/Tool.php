<?php
/**
 * 便利工具
 *
 * @package comm
 * @author  fengqiang
 */

namespace Common\Util;

class Tool {

    /**
     * 判断php宿主环境是否是64bit
     *
     * ps: 在64bit下，php有诸多行为与32bit不一致，诸如mod、integer、json_encode/decode等，具体请自行google。
     *
     * @return bool
     */
    public static function is64bit() {
        return ( int ) 0xFFFFFFFF !== -1;
    }

    /**
     * 修正过的ip2long
     *
     * 可去除ip地址中的前导0。32位php兼容，若超出127.255.255.255，则会返回一个float
     *
     * for example: 02.168.010.010 => 2.168.10.10
     *
     * 处理方法有很多种，目前先采用这种分段取绝对值取整的方法吧……
     * @param string $ip
     * @return float 使用unsigned int表示的ip。如果ip地址转换失败，则会返回0
     */
    public static function ip2long($ip) {
        $ip_chunks = explode('.', $ip, 4);
        foreach ($ip_chunks as $i => $v) {
            $ip_chunks[$i] = abs(intval($v));
        }
        return sprintf('%u', ip2long(implode('.', $ip_chunks)));
    }

    /**
     * 判断是否是内网ip
     * @param string $ip
     * @return boolean
     */
    public static function isPrivateIp($ip) {
        $ip_value = self::ip2long($ip);
        return ($ip_value & 0xFF000000) === 0x0A000000 ||         //10.0.0.0-10.255.255.255
        ($ip_value & 0xFFF00000) === 0xAC100000 ||         //172.16.0.0-172.31.255.255
        ($ip_value & 0xFFFF0000) === 0xC0A80000;        //192.168.0.0-192.168.255.255

    }


    /**
     * To get ip belonged region according to ip
     * @param <string> $ip ip address, heard that can be ip strings, split by "," ,but i found it not used
     * @param <int> $type 地域名及ISP的显示格式  0 默认文本格式；
    1 regions.xml中的id；
    2 regions.xml中的code，即ISO-3166的地区代码；
    3 regions.xml中的fips，即FIPS的地区代码。
     * @param <string> $encoding  编码类, gbk或utf-8, 默认为gbk
     * @return <int or array>
     */
    static function getIpSource($ip, $type = 1, $encoding = 'utf-8') {
        if (!function_exists('lookup_ip_source'))
            return 0;
        $code = lookup_ip_source($ip, $type, $encoding);
        switch ($code) {
            case "-1" :
            case "-2" :
            case "-3" :
                return 0;
                break;
            default :
                return $code;
                break;
        }

    }

    /**
     * 获取真实的客户端ip地址
     *
     * This function is copied from login.sina.com.cn/module/libmisc.php/get_ip()
     *
     * @param boolean $to_long	可选。是否返回一个unsigned int表示的ip地址
     * @return string|float		客户端ip。如果to_long为真，则返回一个unsigned int表示的ip地址；否则，返回字符串表示。
     */
    public static function getRealClientIp($to_long = false) {
        $forwarded = self::getServer('HTTP_X_FORWARDED_FOR');
        if ($forwarded) {
            $ip_chains = explode(',', $forwarded);
            $proxied_client_ip = $ip_chains ? trim(array_pop($ip_chains)) : '';
        }

        if (static::isPrivateIp(self::getServer('REMOTE_ADDR')) && isset($proxied_client_ip)) {
            $real_ip = $proxied_client_ip;
        } else {
            $real_ip = self::getServer('REMOTE_ADDR');
        }

        return $to_long ? self::ip2long($real_ip) : $real_ip;
    }

    /**
     * 根据实际场景，获取客户端IP
     * @param	boolean		$to_long	是否变为整型IP
     * @return	string
     */
    public static function getClientIp($to_long = false) {
        $ip = self::getRealClientIp();
        return $to_long ? self::ip2long($ip) : $ip;
    }

    /**
     * 获取当前Referer
     *
     * @return string
     */
    public static function getReferer() {
        return self::getServer('HTTP_REFERER');
    }

    /**
     * 获取当前域名
     *
     * @return string
     */
    public static function getDomain() {
        return self::getServer('SERVER_NAME');
    }

    /**
     * 得到当前请求的环境变量
     *
     * @param string $name
     * @return string|null 当$name指定的环境变量不存在时，返回null
     */
    public static function getServer($name) {
        return isset($_SERVER[$name]) ? $_SERVER[$name] : null;
    }

    /**
     * 返回当前url
     *
     * @param bool $urlencode 是否urlencode后返回，默认true
     */
    public static function getCurrentUrl($urlencode = true) {
        $req_uri = self::getServer('REQUEST_URI');
        if (null === $req_uri) {
            $req_uri = self::getServer('PHP_SELF');
        }

        $https = self::getServer('HTTPS');
        $s = null === $https ? '' : ('on' == $https ? 's' : '');

        $protocol = self::getServer('SERVER_PROTOCOL');
        $protocol = strtolower(substr($protocol, 0, strpos($protocol, '/'))) . $s;

        $port = self::getServer('SERVER_PORT');
        $port = ($port == '80') ? '' : (':' . $port);

        $server_name = self::getServer('SERVER_NAME');
        $current_url = $protocol . '://' . $server_name . $port . $req_uri;

        return $urlencode ? rawurlencode($current_url) : $current_url;
    }

    /*
	 * 获得服务器本地ip
	 */
    static public function getServerIp() {
        if(isset($_SERVER['SERVER_ADDR'])) {
            return $_SERVER['SERVER_ADDR'];
        }

        $exec = "/sbin/ifconfig | grep 'inet addr' | awk '{ print $2 }' | awk -F ':' '{ print $2}' | head -1";
        $ip = trim(Comm_Util::execute($exec));
        if (preg_match('/^[0-9\.]+$/', $ip)) {
            return $ip;
        } else {
            return '60.28.175.24';
        }
    }

    /**
     * 执行系统shell脚本，并返回输出
     * @param $cmd
     * @return string
     */
    static public function execute($cmd) {
        return shell_exec($cmd);
    }

    /**
     * 循环写入网络包
     *
     * @param resource $fp      网络资源
     * @param string   $content 内容
     *
     * @return int
     *
     * @author chengxuan <chengxuan@staff.sina.com.cn>
     */
    static public function netWrite($fp, $content) {
        $length = strlen($content);
        $write_length = fwrite($fp, $content);
        if($write_length < $length) {
            for($i = 0; $write_length < $length && $i < 10; ++$i) {
                $write_length += fwrite($fp, substr($content, $write_length));
            }
        }
        return $write_length;
    }

    /**
     * 判断是否为cli方式运行
     * @return bool
     */
    static public function isCli() {
        return php_sapi_name()=='cli';
    }

    /**
     * 判断一个给定的UA是否为主流的搜索引擎 http://www.useragentstring.com/pages/Crawlerlist/
     *
     * @param \Common\Util\user|string $ua user agent
     * @return bool
     */
    static  function isSpiderUA($ua='') {
        if(!$ua) {
            $ua =  $_SERVER["HTTP_USER_AGENT"];
        }
        //搜狗： 'Sogou web spider'
        //搜搜； 'Sosospider'
        $ua_list = array('Baiduspider', 'bingbot', 'Googlebot', 'msnbot', 'YoudaoBot', 'spider', 'Sosospider', 'Yahoo! Slurp');
        foreach ($ua_list as $item) {
            if(strpos($ua,  $item) !== false)	 {
                return true;
            }
        }
        return false;
    }

    static function getLocalIp() {
        $sys = 	php_uname("s");
        if (substr($sys, 0, 3) == 'WIN') {
            $preg = "/\A((([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\.){3}(([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\Z/";
            //获取操作系统为win2000/xp、win7的本机IP真实地址  
            self::execCmd("ipconfig");
            if (!empty($out)) {
                foreach ($out AS $row) {
                    if (strstr($row, "IP") && strstr($row, ":") && !strstr($row, "IPv6")) {
                        $tmpIp = explode(":", $row);
                        if (preg_match($preg, trim($tmpIp[1]))) {
                            return trim($tmpIp[1]);
                        }
                    }
                }
            }
        }else if (substr($sys, 6)=='Darwin') { //max os
            //获取操作系统为linux类型的本机IP真实地址  
            $out = self::execCmd("ifconfig|grep inet|grep -v 'inet6\\|127' | awk '{print $2}'");
            if (!empty($out)) {
                return $out[0];
            }
        }else{
            $out = self::execCmd("ifconfig|grep inet|grep -v 'inet6\\|127' | awk '{print $2}' | awk -F: '{print $2}' | grep -v '^$'");
            if (!empty($out)) {
                return $out[0];
            }
        }

        return '127.0.0.1';
    }
    
    public static function execCmd($cmd) {
        return shell_exec($cmd);
    }
}
<?php
namespace Common\Http;

abstract class Response {
    //响应体类型（JSON）

    const TYPE_JSON = 'json';

    //响应体类型（JS）
    const TYPE_JS = 'js';

    //响应体类型（HTML）
    const TYPE_HTML = 'html';

    static public function writeHeader($type) {
        if (headers_sent()) {
            return false;
        }
        switch ($type) {
            case 'json' :
                header('Content-type: application/json; charset=utf-8');
                break;
            case 'html' :
                header('Content-type: text/html; charset=utf-8');
                break;
            case 'js' :
                header('text/javascript; charset=utf-8');
                break;
            case 'jpg' :
                header('Content-Type: image/jpeg');
                break;
        }
        return true;
    }

    /**
     * 直接输出一段Header头和JSON
     *
     * @param mixed   $data          要编码的数据
     * @param boolean $output_header 是否输出JSONHEADER（默认是）
     *
     * @return string
     */
    static public function json($data, $output_header = true) {
        $output_header && self::writeHeader(self::TYPE_JSON);
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param      $data
     * @param null $callback
     * @return string
     */
    static public function jsonp($data, $callback=null) {
        if ($callback == null){
            $callback = 'callback';
        }
     
        $response = "window.{$callback} && {$callback}(".json_encode($data, JSON_UNESCAPED_UNICODE).");";
        return $response;
    }

}

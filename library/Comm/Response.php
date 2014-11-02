<?php
/**
 * 输出结构
 *
 * @package comm
 * @author Chengxuan <chengxuan@staff.sina.com.cn>
 */
abstract class Comm_Response {
    //响应体类型（JSON）

    const TYPE_JSON = 'json';

    //响应体类型（JS）
    const TYPE_JS = 'js';

    //响应体类型（HTML）
    const TYPE_HTML = 'html';

    /**
     * 输出响应类型
     * @param type $type
     */
    static public function contentType($type) {
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
     * 输出一段JSON
     * @param type $code
     * @param type $msg
     * @param type $data
     * @param type $return
     * @return boolean
     */
    static public function json($code, $msg, $data = null, $return = true) {
        $result = json_encode(array('code' => $code, 'msg' => $msg, 'data' => $data), JSON_UNESCAPED_UNICODE);
        if ($return) {
            return $result;
        } else {
            echo $result;
            return true;
        }
    }

    /**
     * 直接输出一段Header头和JSON
     *
     * @param mixed   $data          要编码的数据
     * @param boolean $output_header 是否输出JSONHEADER（默认是）
     *
     * @return void
     */
    static public function outputJson($data, $output_header = true) {
        $output_header && self::contentType(self::TYPE_JSON);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 返回一段JSONP数据
     * @param     int       $code
     * @param     string    $msg
     * @param     mixed     $data
     * @return    string
     */
    static public function jsonp($code, $msg, $data = null) {
        $result = array(
            'code'   => $code,
            'msg'    => $msg,
            'data'   => $data,
        );

        $r = Yaf_Dispatcher::getInstance()->getRequest();
        $result['key'] = Comm_Argchecker::string($r->getQuery('_k'), 'alnumu', 2, 3, null);
        $callback = Comm_Argchecker::string($r->getQuery('_v'), 'alnumu', 2, 3, 'callback');

        $response = "window.{$callback} && {$callback}(".json_encode($result, JSON_UNESCAPED_UNICODE).");";
        return $response;
    }

}

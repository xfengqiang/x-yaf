<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-23 15:31
 */

namespace Http\Controller;

class Web extends Base{
    public function init(){
        parent::init();
        $r = $this->getRequest();
        $page_js = strtolower('app/'.$r->getControllerName().'/'.$r->getActionName());
        $this->getView()->assign('page_js', $page_js);
        $js_debug = $this->getRequest()->get('js_debug', '0');
        $this->getView()->assign('js_debug', $js_debug);
    }
    
    public function renderView($data=[]){
        
        if($data) {
            foreach($data as $k=>$v) {
                $this->getView()->assign($k, $v);
            }
        }
    }
} 
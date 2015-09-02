<?php

/**
 * 
 * @Autor: frank
 * @Date: 2015-05-23 14:57
 */

namespace Http\Controller;

class Cli extends Base{
    public function init() {
        parent::init();
        \Yaf_Dispatcher::getInstance()->autoRender(false)->disableView();
    }
} 
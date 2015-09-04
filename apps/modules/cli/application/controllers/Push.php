<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-24 15:34
 */
class PushController extends \Http\Controller\Cli{
    public function init(){
        parent::init();
    }

    public function sendAction(){
        var_dump($this->configs);
        $cnt = 0;
        while(true) {
            for($i=0; $i< $this->stepCount; $i++){
                //do sth
            }
            $cnt +=$this->stepCount;
            $this->logger->info("Count:$cnt");
            sleep($this->sleepTime);
        }
    }

    
    public function sendEmailAction(){
        echo "Hello\n";
        $v = $this->getRequest()->get('key1');
        echo "[".$v."]\n";
        //        \Common\Mysql\Query::runTest();
    }
} 
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
        $cnt = 0;
        while(true) {
            for($i=0; $i< $this->stepCount; $i++){
                //do sth
            }
            $cnt +=$this->stepCount;
            $this->logger->info("Count:$cnt");
            sleep(0.5);
        }
    }

    public function lockAction() {
//        \Common\Mysql\DbCache::RegisterDb('lockdb', ['host'=>'127.0.0.1', 'port'=>3600, 'dbname'=>'xwk', 'user'=>'root', 'password'=>'z']);
        $db = new \Common\Mysql\Db('lockdb');
        $lock = new Common\Lock\DbLock($db);
        $lock_key = $this->taskName;
        if($lock->lock($lock_key)) {
            echo "lock ok\n";;
        }else{
            echo "lock failed\n";
            exit;
        }
        
            sleep(10);
        if ($lock->unlock($lock_key)) {
            echo "Unlocked\n";
        }else{
            echo "Unlock failed\n";
        }
        
    }
    
    public function sendEmailAction(){
        echo "Hello\n";
        $v = $this->getRequest()->get('key1');
        echo "[".$v."]\n";
        //        \Common\Mysql\Query::runTest();
    }
} 
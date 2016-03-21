<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-08-12 20:55
 */
class SearchController extends \Http\Controller\Base {
    public function actorAction() {
        $this->disableView();
       
        $searchClient = new \Sphinx\SphinxClient();
        $searchClient->SetServer('172.16.147.110', 9312);
        $searchClient->SetConnectTimeout(1);
        $searchClient->SetArrayResult ( true );
        $searchClient->SetWeights ( array ( 100, 1 ) );
        $searchClient->SetMatchMode ( SPH_MATCH_EXTENDED );
        $searchClient->SetLimits(0, 20);
        $searchClient->SetArrayResult(true);
//        $searchClient->SetFieldWeights()
//        $ret = $searchClient->Query('王琳', '*');
        $ret = $searchClient->Query('@fuser_name "王" @fuser_gender "f"', 'actor');
        
        echo json_encode($ret);
//        if ($ret=== false) {
//            echo "Query failed";
//            echo "LastError:".$searchClient->GetLastError();
//        }
//        
//        echo "Last Warnning:".$searchClient->GetLastWarning();
//        
//        var_dump($ret);

    }

    public function projectAction() {
        $this->disableView();

        $searchClient = new \Sphinx\SphinxClient();
        $searchClient->SetServer('172.16.147.110', 9312);
        $searchClient->SetConnectTimeout(1);
        $searchClient->SetArrayResult ( true );
        $searchClient->SetWeights ( array ( 100, 1 ) );
        $searchClient->SetMatchMode ( SPH_MATCH_EXTENDED );
        $searchClient->SetLimits(0, 20);
        $searchClient->SetArrayResult(true);
        
        $searchClient->SetFilter('ftype', [crc32('tv')]);
        $searchClient->SetSortMode ( SPH_SORT_ATTR_DESC, "fcreate_time" );
        $ret = $searchClient->Query('@fname "的" @fuser_gender "f"', 'project');

                if ($ret=== false) {
                    echo "Query failed";
                    echo "LastError:".$searchClient->GetLastError();
                }
        echo json_encode($ret);

    }
} 
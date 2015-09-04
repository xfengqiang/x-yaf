/**
 * Created by Administrator on 15-9-2.
 */
define(function (require, exports) {
    var $ = require("jquery");
    console.log('111111111');
    $("[action-type=login]").click(function(){
        console.log('this is submit action');
        return false;
    });
});
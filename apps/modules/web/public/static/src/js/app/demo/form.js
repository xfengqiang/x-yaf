/**
 * Created by Administrator on 15-9-2.
 */
define(function (require, exports, module) {
//   var $ = require("jquery");
    var $ = require('jquery.validate.zh');
    console.log(js_config.rules);
    console.log(js_config.messages);
    
    var validator = $('#register_form').validate({
        errorPlacement: function(error, element) {
            console.log('error~~~~~', element.name);
            error.appendTo(element.parent("div"));
        },
        success: function(label) {
            console.log('ok~~~~~', label.name);
            label.text("ok!").addClass("success");
        },
       rules : js_config.rules,
       messages : js_config.messages
    });

    console.log('this is ret:'+validator.valid());
    if(validator.valid()) {
        
    }else{
        
    }
    
//    $("[action-type=register]").click(function(){
//        console.log('this is submit action');
//        return false;
//    });
});
/**
 * Created by Administrator on 15-9-2.
 */
define(function (require, exports, module) {
//   var $ = require("jquery");
    var $ = require('jquery.validate.zh');
    console.log($);
    
    var ret = $('#register_form').validate({
        errorPlacement: function(error, element) {
            console.log('error~~~~~', element.name);
            error.appendTo(element.parent("div").next("td"));
        },
        success: function(label) {
            console.log('error~~~~~', element.name);
            label.text("ok!").addClass("success");
        },
       rules : {
           username: {
               required: true,
               rangelength : [6, 20]
           },
           email : {
               required: true,
               email: true
           },
           password : {
               required: true,
               minlength:6
           },
           password_confirm : {
               required: true,
               equalTo: '[name=password]'
           }
       } ,
        messages : {
            username: {
                required: '请填写姓名'
//                rangelength: '姓名的长度需要在'
            }
        }
    });
    
    console.log('this is ret:'+ret);
//    $("[action-type=register]").click(function(){
//        console.log('this is submit action');
//        return false;
//    });
});
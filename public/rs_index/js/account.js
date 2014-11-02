var Account = {
    init : function(){
        console.log("Hello~~~");
        $('[action-type="login"]').click(Account.doLogin);
        $('#vcode_image').click(Account.refreshCode);
    },   
    
    doLogin : function() {
        var form = $('#form_login');
        var username = $('[name="username"]').val();
        var password = $('[name="password"]').val();
        
        if(!username || !password){
            Account.showError('用户名和密码均不能为空');
            return false;
        }
        
        var vcode = $('[name="vcode"]').val();
        if(!vcode){
            Account.showError('请输入验证码');
            return false;
        }
        
        var data = form.serialize();
        $.post('/aj_account/login', data, function(ret){
            console.log(ret);
            if(ret.errno == 0){
                window.location.href="/";
            }else{
                Account.refreshCode();
                Account.showError(ret.msg);
            }
        }, "json");
        
        return false;
    },
    showError : function(msg){
        $("#error").show();
        $("#error").html(msg);    
    },
    refreshCode : function(){
        $('#vcode_image').attr('src', '/account/vcode?ver='+Math.random());
    }
}

$(function(){
    Account.init();
});
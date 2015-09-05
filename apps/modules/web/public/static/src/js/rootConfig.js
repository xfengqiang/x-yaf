seajs.config({
    base : "../static/src/js/",
    vars: {
        'env': 'prod'
    },
    alias:{
        "jquery":"lib/jquery/jquery/1.8.2/jquery",
        "jquery.validate":"lib/jquery/validate/jquery.validate",
        "jquery.validate.zh" : "lib/jquery/validate/localization/messages_zh"
    },
    debug:1
});

var appConfig = {
    env : "prod", //dev|prod
    pkgType : "aio" //types: page|comb|aio
};
/*TMODJS:{"version":1,"md5":"49998c6ecae88a6c726794354d37db18"}*/
define(function(require) {
    return require("../copyright"), require("../template")("common/footer", function($data, $filename) {
        "use strict";
        var $utils = this, time = ($utils.$helpers, $data.time), $escape = $utils.$escape, include = function(filename, data) {
            data = data || $data;
            var text = $utils.$include(filename, data, $filename);
            return $out += text;
        }, $out = "";
        return $out += '<div id="footer"> ', time && ($out += " <p class='time'>", $out += $escape(time), 
        $out += "</p> "), $out += " ", include("../copyright"), $out += " </div>", new String($out);
    });
});
/*TMODJS:{"version":1,"md5":"b7b2e5053b8f65a9a004e1e61d59d9c1"}*/
define(function(require) {
    return require("../common/header"), require("../common/footer"), require("../template")("index/index", function($data, $filename) {
        "use strict";
        var $utils = this, include = ($utils.$helpers, function(filename, data) {
            data = data || $data;
            var text = $utils.$include(filename, data, $filename);
            return $out += text;
        }), $escape = $utils.$escape, title = $data.title, $each = $utils.$each, list = $data.list, $out = ($data.$value, 
        $data.$index, "");
        return $out += "<!DOCTYPE html> <html> <head> <title></title> </head> <body> ", 
        include("../common/header"), $out += ' <div id="main"> <h3>', $out += $escape(title), 
        $out += "</h3> <ul> ", $each(list, function($value) {
            $out += ' <li><a href="', $out += $escape($value.url), $out += '">', $out += $escape($value.title), 
            $out += "</a></li> ";
        }), $out += " </ul> </div> ", include("../common/footer"), $out += " </body> </html>", 
        new String($out);
    });
});
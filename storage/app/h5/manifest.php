<?php
// CACHE MANIFEST 必须在第一行，跨行浏览器不解析
ob_end_clean();
ob_start(function ($buffer, $mode) {
    if (extension_loaded('zlib') and function_exists('ob_gzhandler')) {
        return ob_gzhandler($buffer, $mode);
    }

    return $buffer;
});
header('Content-type:text/cache-manifest; charset=utf-8'); ?>
CACHE MANIFEST

# v0.0.1
# time: 2016-01-19 16:27

# JavaScript
apps/h5/_static/js/jquery-2.2.0.js
apps/h5/_static/js/material.min.js
apps/h5/_static/js/fastclick.js
apps/h5/_static/js/jquery.pjax.js

# css
apps/h5/_static/css/material.min.css
apps/h5/_static/css/animate.css

# image
apps/h5/_static/images/blur.jpg

# font icon
apps/h5/_static/fonts/roboto/Roboto-Bold.eot
apps/h5/_static/fonts/roboto/Roboto-Bold.ttf
apps/h5/_static/fonts/roboto/Roboto-Bold.woff
apps/h5/_static/fonts/roboto/Roboto-Bold.woff2
apps/h5/_static/fonts/roboto/Roboto-Light.eot
apps/h5/_static/fonts/roboto/Roboto-Light.ttf
apps/h5/_static/fonts/roboto/Roboto-Light.woff
apps/h5/_static/fonts/roboto/Roboto-Light.woff2
apps/h5/_static/fonts/roboto/Roboto-Medium.eot
apps/h5/_static/fonts/roboto/Roboto-Medium.ttf
apps/h5/_static/fonts/roboto/Roboto-Medium.woff
apps/h5/_static/fonts/roboto/Roboto-Medium.woff2
apps/h5/_static/fonts/roboto/Roboto-Regular.eot
apps/h5/_static/fonts/roboto/Roboto-Regular.ttf
apps/h5/_static/fonts/roboto/Roboto-Regular.woff
apps/h5/_static/fonts/roboto/Roboto-Regular.woff2
apps/h5/_static/fonts/roboto/Roboto-Thin.eot
apps/h5/_static/fonts/roboto/Roboto-Thin.ttf
apps/h5/_static/fonts/roboto/Roboto-Thin.woff
apps/h5/_static/fonts/roboto/Roboto-Thin.woff2

# vendor 
https://fonts.googleapis.com/icon?family=Material+Icons

NETWORK:
*
<?php ob_end_flush(); exit; ?>
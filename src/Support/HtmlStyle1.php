<?php

class HtmlStyle1
{
    /**
     * Use for container centralizing, just passed in center, left or right
     * e.g <div style="<?php HtmlStyle1::getMarginCenter('right') ?>"> Hello world</div>
     * @param string $align
     * @param bool $userPercentage
     * @return string
     */
    static function getMarginCenter($align = 'center', $userPercentage = false)
    {
        if (is_numeric($align)) return "margin-left:$align% !important;";
        switch (trim(strtolower($align))) {
            case 'center':
                return $userPercentage ? 'margin-left:40% !important; ' : "margin:0  !important;";
            case 'left':
                return $userPercentage ? 'margin-left:0% !important; ' : "margin-left: 0 !important; margin-right: auto  !important;";
            case 'right':
                return $userPercentage ? 'margin-left:80% !important; ' : "margin-left: auto  !important; margin-right: 0  !important;";
            default :
                return static::getMarginCenter('left', true);
        }
    }

    public static function enablePreWrap($selector = '', $initStyle = 'outline: 1px solid #e2e2e2;  padding: 10px;  margin: 5px')
    {
        return <<< EOSTRING
        <style> $selector pre{ $initStyle; white-space: pre-wrap;  white-space: -moz-pre-wrap;  white-space:-pre-wrap;  white-space: -o-pre-wrap;  word-wrap: break-word;} </style>
EOSTRING;
    }


    public static function enableCenter($selector = '.middle')
    {
        return <<< EOSTRING
        <style> $selector {  height: 100%;  display: flex;  align-items: center;  justify-content: center;  } </style>
EOSTRING;
    }

    public static function enable3DButton($selector = '.btn')
    {
        return <<< EOSTRING
        <style> $selector{position:relative;top:-6px;border:0;transition:all 40ms linear;margin-top:10px;margin-bottom:10px;margin-left:2px;margin-right:2px}$selector:active:focus,$selector:focus:hover,$selector:focus{-moz-outline-style:none;outline:medium none}$selector:active,$selector.active{top:2px}$selector.btn-white{color:#666;box-shadow:0 0 0 1px #ebebeb inset,0 0 0 2px rgba(255,255,255,0.10) inset,0 8px 0 0 #f5f5f5,0 8px 8px 1px rgba(0,0,0,.2);background-color:#fff}$selector.btn-white:active,$selector.btn-white.active{color:#666;box-shadow:0 0 0 1px #ebebeb inset,0 0 0 1px rgba(255,255,255,0.15) inset,0 1px 3px 1px rgba(0,0,0,.1);background-color:#fff}$selector.btn-default{color:#666;box-shadow:0 0 0 1px #ebebeb inset,0 0 0 2px rgba(255,255,255,0.10) inset,0 8px 0 0 #BEBEBE,0 8px 8px 1px rgba(0,0,0,.2);background-color:#f9f9f9}$selector.btn-default:active,$selector.btn-default.active{color:#666;box-shadow:0 0 0 1px #ebebeb inset,0 0 0 1px rgba(255,255,255,0.15) inset,0 1px 3px 1px rgba(0,0,0,.1);background-color:#f9f9f9}$selector.btn-primary{box-shadow:0 0 0 1px #417fbd inset,0 0 0 2px rgba(255,255,255,0.15) inset,0 8px 0 0 #4D5BBE,0 8px 8px 1px rgba(0,0,0,0.5);background-color:#4274d7}$selector.btn-primary:active,$selector.btn-primary.active{box-shadow:0 0 0 1px #417fbd inset,0 0 0 1px rgba(255,255,255,0.15) inset,0 1px 3px 1px rgba(0,0,0,0.3);background-color:#4274d7}$selector.btn-success{box-shadow:0 0 0 1px #31c300 inset,0 0 0 2px rgba(255,255,255,0.15) inset,0 8px 0 0 #5eb924,0 8px 8px 1px rgba(0,0,0,0.5);background-color:#78d739}$selector.btn-success:active,$selector.btn-success.active{box-shadow:0 0 0 1px #30cd00 inset,0 0 0 1px rgba(255,255,255,0.15) inset,0 1px 3px 1px rgba(0,0,0,0.3);background-color:#78d739}$selector.btn-info{box-shadow:0 0 0 1px #00a5c3 inset,0 0 0 2px rgba(255,255,255,0.15) inset,0 8px 0 0 #348FD2,0 8px 8px 1px rgba(0,0,0,0.5);background-color:#39b3d7}$selector.btn-info:active,$selector.btn-info.active{box-shadow:0 0 0 1px #00a5c3 inset,0 0 0 1px rgba(255,255,255,0.15) inset,0 1px 3px 1px rgba(0,0,0,0.3);background-color:#39b3d7}$selector.btn-warning{box-shadow:0 0 0 1px #d79a47 inset,0 0 0 2px rgba(255,255,255,0.15) inset,0 8px 0 0 #D79A34,0 8px 8px 1px rgba(0,0,0,0.5);background-color:#feaf20}$selector.btn-warning:active,$selector.btn-warning.active{box-shadow:0 0 0 1px #d79a47 inset,0 0 0 1px rgba(255,255,255,0.15) inset,0 1px 3px 1px rgba(0,0,0,0.3);background-color:#feaf20}$selector.btn-danger{box-shadow:0 0 0 1px #b93802 inset,0 0 0 2px rgba(255,255,255,0.15) inset,0 8px 0 0 #AA0000,0 8px 8px 1px rgba(0,0,0,0.5);background-color:#d73814}$selector.btn-danger:active,$selector.btn-danger.active{box-shadow:0 0 0 1px #b93802 inset,0 0 0 1px rgba(255,255,255,0.15) inset,0 1px 3px 1px rgba(0,0,0,0.3);background-color:#d73814}$selector.btn-magick{color:#fff;box-shadow:0 0 0 1px #9a00cd inset,0 0 0 2px rgba(255,255,255,0.15) inset,0 8px 0 0 #9823d5,0 8px 8px 1px rgba(0,0,0,0.5);background-color:#bb39d7}$selector.btn-magick:active,$selector.btn-magick.active{box-shadow:0 0 0 1px #9a00cd inset,0 0 0 1px rgba(255,255,255,0.15) inset,0 1px 3px 1px rgba(0,0,0,0.3);background-color:#bb39d7} </style>
EOSTRING;
    }

    public static function getFixBackgroundAttr()
    {
        return " no-repeat center center fixed; background-size: cover; ";
    }

    public static function getShadow2x()
    {
        return "box-shadow: 0 2px 2px 0 rgba(0, 0, 0, .14), 0 3px 1px -2px rgba(0, 0, 0, .2), 0 1px 5px 0 rgba(0, 0, 0, .12);";
    }

    public static function zoomOut($className = 'img-zoom')
    {
        Page1::printOnce("<style>.$className{margin:10px 10px 10px 10px;-webkit-transform:scale(1,1);-ms-transform:scale(1,1);transform:scale(1,1);transition-duration:.3s;-webkit-transition-duration:.3s}.$className:hover{cursor:pointer;-webkit-transform:scale(2,2);-ms-transform:scale(2,2);transform:scale(2,2);transition-duration:.3s;-webkit-transition-duration:.3s;box-shadow:10px 10px 5px #888;z-index:100;position:absolute}</style>");
    }
}

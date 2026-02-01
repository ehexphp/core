<?php

class HtmlAsset1
{
    static function getImageAvatar()
    {
        return asset('default/images/icons/avatar.png');
    }

    static function getImageThumb()
    {
        return asset('default/images/icons/thumb.png');
    }

    static function getSuccessIcon()
    {
        return asset('default/images/icons/success.png');
    }

    static function getCloseIcon()
    {
        return asset('default/images/icons/close.png');
    }

    static function getError404()
    {
        return asset('default/images/error/error404.jpg');
    }
}

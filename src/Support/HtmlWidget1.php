<?php

class HtmlWidget1
{

    /**
     * Widget to delete file
     * @param int $model1FileLocatorId
     * @param string $filePath
     * @param null $previewUrl
     * @param string $style
     * @param string $labelName
     * @return string
     */
    public static function fileDeleteBox($model1FileLocatorId = -1, $filePath = '/public_html/image.jpg', $previewUrl = null, $style = 'height:150px;width:150px;', $labelName = 'Delete Image')
    {
        $uniqueImageId = 'image_preview_' . Math1::getUniqueId();
        $fileName = FileManager1::getFileName($filePath);
        $ajaxLink = Form1::callApi(exApiController1::class, "deleteFile()?_token=" . token() . "&file_locator_id=$model1FileLocatorId&file_path=" . urlencode($filePath));
        $previewUrl = $previewUrl ? $previewUrl : HtmlAsset1::getImageThumb();
        return <<< EOSTRING
            <!-- Call delete api-->
            <script>
                function $uniqueImageId() {
                    Popup1.confirmAjax('Delete File?', "This action cannot be undo, Press yes to continue. <br/><hr><strong>Filename : $fileName</strong> <hr><a target='_blank' href='$previewUrl'><img style='height:100px' src='$previewUrl' alt='delete image' /></a><hr>", "$ajaxLink", function(data){
                        if(data == 'true') {
                            Popup1.alert('Action Successful!', '', 'success');
                            $("#container-$uniqueImageId").remove();
                        }
                        else  Popup1.alert('Action failed', 'error ['+data+']', 'error');
                    })
                }
            </script>
            
            <!-- Delete Interface-->
            <label title="$fileName" class="btn btn-danger" id="container-$uniqueImageId" onclick="$uniqueImageId()">
                <img style="$style"  src="$previewUrl">
                <div style="clear:both"></div>
                <div style="margin-top:5px;c"><i class="fa fa-trash"></i> $labelName </div>
            </label>
EOSTRING;
    }

    /**
     * File Upload Widget. with url field. if $imageButtonName is feature_image, then input box would be feature_image_url
     * so as for feature_images[] would be feature_images_url[]
     * // HtmlWidget1::imageUploadBox('feature_image',  ($model->id > 0) ? $model->feature_image_url: null, 'height:150px;width:100% !important', '')
     * @param string $fileInputName
     * @param null $placeholderImageOrDefault
     * @param string $imageStyle
     * @param string $labelName
     * @return string
     * @see HtmlWidget1::fileUploadBox()
     */
    public static function imageUploadBox($fileInputName = 'feature_image', $placeholderImage= null, $imageStyle = 'height:150px;max-width:100%;', $labelName = 'Upload Image')
    {

        // delete button
        $existingImagePath = Url1::urlToPath($placeholderImage);

        // init field
        $placeholderImageOrDefault = ($placeholderImage) ? $placeholderImage : HtmlAsset1::getImageThumb(); //layout_asset('/image/thumb.png');
        $demoPreviewImage = $placeholderImageOrDefault !== HtmlAsset1::getImageThumb() ? Url1::getFileImagePreview($placeholderImageOrDefault, HtmlAsset1::getSuccessIcon()) : $placeholderImageOrDefault; //layout_asset('/image/thumb.png');
        $fileUrl_filterOutDemoImage = (($placeholderImageOrDefault !== HtmlAsset1::getImageThumb()) && ($placeholderImageOrDefault !== HtmlAsset1::getImageAvatar())) ? $placeholderImageOrDefault : null; //layout_asset('/image/thumb.png');
        $defaultImageForNotImageFile = asset('default/images/icons/success.png');

        //dd($existingImagePath);
        $deleteButton = ($fileUrl_filterOutDemoImage && $existingImagePath && !String1::contains('/shared/', $existingImagePath)) ? "<button type='button' onclick='Popup1.confirmLink(`Delete File`, `Will you like to delete file and refresh page?`, `" . Form1::callController(exApiController1::class, "deleteFile()?_token=" . token() . "&file_path=" . urlencode($existingImagePath)) . "`)' class='btn btn-danger  file_action' style='font-weight:800;'><!-- display: none -->X</button>" : '';
        $fileUrlInputName = String1::endsWith('[]', trim($fileInputName)) ? String1::replaceEnd(trim($fileInputName), '[]', '') . '_url[]' : trim($fileInputName) . '_url';
        $labelName = !empty($labelName) ? '<div style="margin-top:3px;"><i class="fa fa-upload"></i> ' . $labelName . '</div>' : '';
        $noHeightInStyle = String1::replace($imageStyle, 'height', 'hgt');


        return <<< HTML
            <label class="btn btn-default btn-sm" style="border:1px dotted #aaa;border-radius: 20px; overflow: auto; $noHeightInStyle"> <!--  onmousemove="$(this).find('.file_action').show()" onmouseout="$(this).find('.file_action').hide()" -->
                <input style="display: none; width:99%;" onchange="Picture1.uploadPreview(this, null, '$defaultImageForNotImageFile')" type="file" name="$fileInputName" value="" />
                <img style="$imageStyle"  src="$demoPreviewImage" id="$placeholderImageOrDefault" />
                <div> <div class="input-group"><input name="$fileUrlInputName" class="form-control field_url" placeholder="or paste file url" value="$fileUrl_filterOutDemoImage" />$deleteButton</div> $labelName </div>
            </label>
HTML;
    }


    /**
     * File Upload Widget. with url field. if $imageButtonName is feature_image, then input box would be feature_image_url
     * // HtmlWidget1::fileUploadBox('feature_image',  ($model->id > 0) ? $model->feature_image_url: null, 'height:150px;width:100% !important', '')
     * @param string $buttonName
     * @param null $demoImage
     * @param string $image_style
     * @param string $labelName
     * @return string
     * @see HtmlWidget1::imageUploadBox()
     *
     */
    public static function fileUploadBox($buttonName = 'download_file', $demoImage = null, $image_style = 'height:150px;width:150px;', $labelName = 'Upload Image')
    {
        return static::imageUploadBox($buttonName, $demoImage, $image_style, $labelName);
    }

    /**
     * Multiple upload box. i.e you have add more button that could be used to add more files
     * @param string $imageButtonName
     * @param null $modelFilePath
     * @param string $box_style
     * @param string $labelName
     * @param array $hideByFilePath
     * @return string
     */
    public static function imagesUploadBox($imageButtonName = 'uploadImages[]', $modelFilePath = null, $box_style = 'height:150px;width:150px;', $labelName = 'Upload Image', $hideByFilePath = [])
    {
        $loadedImages = '';
        foreach (FileManager1::getDirectoriesFiles($modelFilePath) as $imagePath) $loadedImages .= (!in_array($imagePath, $hideByFilePath) && !in_array(exUrl1::convertPathToUrl($imagePath), $hideByFilePath)) ? HtmlWidget1::fileDeleteBox(-1, $imagePath, exUrl1::convertPathToUrl($imagePath), $box_style) : '';
        $widget = HtmlWidget1::imageUploadBox($imageButtonName, null, $box_style, $labelName);
        return <<< HTML
            <div style="margin:10px;">
                <div id="all_images">
                    $loadedImages
                    <span id="main_image" style="display:none">$widget</span>
                    <span>$widget</span>
                </div>
                <button type="button" onclick="Html1.cloneInnerElement('main_image', 'all_images')" class="btn btn-lg btn-success" style="margin-top:10px; padding: 3px 18px 6px 5px; border-radius:50px;"><span class="fa fa-plus img-circle text-success" style="padding:8px; background:#ffffff; margin-right:4px; border-radius:50%;"></span> Add More </button>
            </div>
HTML;
    }


    /**
     * Add wrapper Panel box around your text
     * @param $title
     * @param string $description
     * @param string $type
     * @param string $boxPanelContentStyle
     * @return string
     */
    public static function panelBox($title, $description = '', $type = 'primary', $boxPanelContentStyle = 'padding:10px;')
    {
        $uniqueId = 'box_' . Math1::getUniqueId();
        $bg = Color1::get($type) or "#2980b9";
        return <<< HTML
        <style> div#$uniqueId {margin-top: 15px; border:1px solid $bg; }  div#$uniqueId .box-top { color: #fff; text-shadow: 0 1px #000;font-weight: 300; background: $bg;padding: 5px; padding-left: 15px; } </style>
        <div id="$uniqueId"> <div class="box-top"> $title </div> <div class="box-panel" style="$boxPanelContentStyle"> $description </div> </div>
HTML;
    }

    /**
     * Visit https://loading.io/spinner/ for custom spin
     * @param int $styleType
     * @param string $color
     * @return string
     */
    static function loader($color = 'gray')
    {
        Page1::printOnce("<style>.lds-dual-ring{display:inline-block;width:64px;height:64px}.lds-dual-ring:after{content:\" \";display:block;width:46px;height:46px;margin:1px;border-radius:50%;border:5px solid $color;border-color:$color transparent $color transparent;animation:lds-dual-ring 1.2s linear infinite}@keyframes lds-dual-ring{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}</style>", 'ex_loader_style');
        return '<span class="lds-dual-ring"></span>';
    }

    /**
     *
     *  Create a css tab in easy way.
     * echo HtmlWidget1::createTabs([
     * 'Home'=> HtmlForm1::addInput('User Name'),
     * 'Church'=> HtmlForm1::addTextArea('User Address'),
     * ]);
     * @param array $taName_equal_tabContent
     * @param string $styleTabItem
     * @param int $selectedIndex
     * @return string
     */
    static function createTabs($taName_equal_tabContent = [], $styleTabItem = '', $selectedIndex = 0)
    {
        Page1::printOnce('<style>.ex_css_tabs{width:650px;float:none;list-style:none;position:relative;margin:80px 0 0 10px}.ex_css_tabs li.ex_tab_item{float:left;display:block; $styleTabItem}.ex_css_tabs input[type="radio"].ex_tab_radio{position:absolute;top:-9999px;left:-9999px}.ex_css_tabs label.ex_tab_label{display:block;padding:14px 21px;border-radius:2px 2px 0 0;font-size:20px;background:#ccc;cursor:pointer;position:relative;top:4px;-moz-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;-webkit-transition:all .2s ease-in-out;transition:all .2s ease-in-out}.ex_css_tabs [id^="tab"]:checked+p{color:black;-moz-transition:all 2.2s ease-in-out;-o-transition:all 2.2s ease-in-out;-webkit-transition:all 2.2s ease-in-out;transition:all 2.2s ease-in-out}.ex_css_tabs label.ex_tab_label:hover{background:#fff}.ex_css_tabs .tab-content{z-index:2;display:none;overflow:hidden;width:100%;font-size:17px;line-height:25px;padding:25px;position:absolute;top:53px;left:0;background:#fff}.ex_css_tabs [id^="tab"]:checked+label.ex_tab_label{background:#fff}.ex_css_tabs [id^="tab"]:checked ~ [id^="tab-content"]{display:block}</style>', '__ex_css_tabs');
        $buff = '';
        $indexCount = 0;
        foreach ($taName_equal_tabContent as $key => $value) {
            $tab_id = Math1::getUniqueId();
            $checked = ($indexCount === $selectedIndex) ? 'checked' : '';
            $buff .= "<li class='ex_tab_item'><input type=radio class='ex_tab_radio' name='ex_css_tabs' id='tab_$tab_id' $checked><label class='ex_tab_label' for='tab_$tab_id'> $key </label><div id='tab-content_$tab_id' class=tab-content>$value</div></li>";
            $indexCount++;
        }
        return "<ul class=ex_css_tabs> $buff </ul>";
    }


    /**
     * use to Make Component active based on   Active Url when is the current Url
     * @param null $urlLink
     * @param string $innerHtmlDataHyperLinkWithUrl
     * @param string $onActiveAddClassName
     * @param string $otherClassNames
     * @param array $tagAttribute
     * @param string $tagName
     * @param bool $isUrlAbsolute e.g if url is home, like url('/'). that is absolute and should not be active with other url
     * @return string
     */
    static function urlActiveTag($urlLink = null, $innerHtmlDataHyperLinkWithUrl = '<a> hello world </a>', $onActiveAddClassName = "active", $otherClassNames = '', $tagAttribute = [], $tagName = 'li', $isUrlAbsolute = false)
    {
        if (isset($tagAttribute['class'])) return Console1::println(['Error<hr/> HtmlWidget::activeUrl() $attribute cannot contain class again', $tagAttribute], true);
        $classList = ltrim($otherClassNames . ' ' . Url1::ifExistInUrl($urlLink, $onActiveAddClassName, '', $isUrlAbsolute));
        return "<$tagName " . Array1::toHtmlAttribute(array_merge($tagAttribute, ['class' => $classList])) . ">$innerHtmlDataHyperLinkWithUrl</$tagName>";
    }


    static function dropMenu($linkNameLocation = ['Dashboard' => '#'], $buttonName = 'Menu', $buttonAttribute = [], $tagName = 'button')
    {
        Page1::printOnce('<style>.ex_dropdown{position:relative;display:inline-block}.ex_dropdown-content{display:none;position:absolute;background-color:#f1f1f1;min-width:160px;box-shadow:0 8px 16px 0 rgba(0,0,0,0.2);z-index:202939}.ex_dropdown-content a{color:black;padding:12px 16px;text-decoration:none;display:block}.ex_dropdown-content a:hover{background-color:#ddd}.ex_dropdown:hover .ex_dropdown-content{display:block}</style>', 'ex_dropbtn_123_widget')
        ?>
        <div class="ex_dropdown"><<?= $tagName . ' ' . Array1::toHtmlAttribute($buttonAttribute) ?>
        ><?= $buttonName ?></<?= $tagName ?>>
        <div class="ex_dropdown-content"><?php foreach ($linkNameLocation as $key => $value) echo "<a href='$key'>$value</a>"; ?></div></div>
        <?php return '';
    }

    public static function listAndMarkActiveLink($linkNameLocation = ['home' => '#'], $selectedListStyle = 'font-weight: bolder;', $normalListStyle = '', callable $callBack = null)
    {
        $buffer = '';
        $currentPath = Url1::getPageFullUrl();
        foreach ($linkNameLocation as $key => $value) {
            $selectedStyle = Url1::existInUrl($value, $currentPath) ? $selectedListStyle : $normalListStyle;
            $buffer .= "<li style='$selectedStyle'><a href='$value'>" . String1::convertToCamelCase($callBack ? $callBack($key) : $key, ' ') . "</a></li>";
        }
        return $buffer;
    }

    public static function menuHorizontalBar($title = 'App Name', $linkNameLocation = ['Home Page' => '#'], $selectedMenuStyle = 'color: #14a3ff;font-weight: bolder;', $menuStyle = '', $navClass = '')
    {
        $buffer = self::listAndMarkActiveLink($linkNameLocation, $selectedMenuStyle, $menuStyle);
        return <<<HTML
            <div class="row"><div class="col-md-12"><nav class="navbar $navClass" role="navigation"><div class="col-md-12"><div class="navbar-header"><a class="navbar-brand" href="#">$title</a></div><ul class="nav navbar-nav navbar-right">$buffer</ul></div></nav></div></div>
HTML;
    }

    public static function menuHorizontalBarAdmin($title = 'App Name', $linkNameLocation = ['Dashboard' => '#'], $selectedMenuStyle = 'color: #14a3ff;font-weight: bolder;', $menuStyle = '', $navClass = 'navbar-inverse navbar-fixed-top', $seachAction = '#')
    {
        $buffer = self::listAndMarkActiveLink($linkNameLocation, $selectedMenuStyle, $menuStyle);
        $userInfo = User::getLogin();
        return <<<HTML
            <nav class="navbar $navClass">
                <div class="container-fluid">
                    <div class="navbar-header"><button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button><a class="navbar-brand" href="#">$title ( Welcome, $userInfo->user_name )</a></div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">$buffer</ul>
                        <form class="navbar-form navbar-right" method="get" action="$seachAction"><input name="q" type="text" class="form-control" placeholder="Search..."></form>
                    </div>
                </div>
            </nav>
HTML;
    }

    public static function menuOverlay($linkNameLocation = ['Dashboard' => '#'], $selectedMenuStyle = 'color: tomato;font-weight: bolder;', $menuStyle = '')
    {
        $buffer = self::listAndMarkActiveLink($linkNameLocation, $selectedMenuStyle, $menuStyle);
        $script = '<script> $(function(){ var $icon=$(".ex_overlay_menu_con .icon");var $menu=$(".ex_overlay_menu");$icon.on("click",function(){if(!$menu.hasClass("active")){$menu.fadeIn().toggleClass("active")}else{$menu.fadeOut().removeClass("active")}}); });</script>';
        $style = '<style> @import url(https://fonts.googleapis.com/css?family=Droid+Sans);.ex_overlay_menu{display:none; z-index:9999; width:100%;height:100%;position:absolute;top:0;left:0;background:#34495e}.ex_overlay_menu ul{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%)}.ex_overlay_menu ul li{list-style-type:none;margin:20px 0;font-size:26px;text-transform:uppercase;transition:all .2s ease;cursor:pointer;}.ex_overlay_menu ul li:hover{transform:translateX(-10px)}.ex_overlay_menu_con .icon *{position:absolute;top:20px;right:20px;width:50px;z-index:99999;cursor:pointer}</style>';
        return <<<HTML
            <div class="ex_overlay_menu_con">
                $style
                  <div class="icon"><i style="font-size:45px; $selectedMenuStyle;" class="fa">&#xf0c9;</i></div>
                  <div class="ex_overlay_menu"> <div class="text"> <ul>  $buffer </ul>  </div>  </div>
                $script
            </div>
HTML;
    }


    public static function rating($ratingName = 'rating')
    {
        $uniqueImageId = 'starrating_' . Math1::getUniqueId();
        $codeStyle = '<style>.' . $uniqueImageId . '>input{display:none}.' . $uniqueImageId . '>label:before{content:"\\f005";margin:2px;font-size:8em;font-family:FontAwesome;display:inline-block}.' . $uniqueImageId . '>label{color:#222}.' . $uniqueImageId . '>input:checked ~ label{color:#ffca08}.' . $uniqueImageId . '>input:hover ~ label{color:#ffca08}</style>';
        $code = '<div class="' . $uniqueImageId . ' risingstar d-flex justify-content-center flex-row-reverse">' . "\n" . '<input type="radio" id="' . $ratingName . '5" name="' . $ratingName . '" value="5" /><label for="' . $ratingName . '5" title="5 star">5</label>' . "\n" . '<input type="radio" id="' . $ratingName . '4" name="' . $ratingName . '" value="4" /><label for="' . $ratingName . '4" title="4 star">4</label>' . "\n" . '<input type="radio" id="' . $ratingName . '3" name="' . $ratingName . '" value="3" /><label for="' . $ratingName . '3" title="3 star">3</label>' . "\n" . '<input type="radio" id="' . $ratingName . '2" name="' . $ratingName . '" value="2" /><label for="' . $ratingName . '2" title="2 star">2</label>' . "\n" . '<input type="radio" id="' . $ratingName . '1" name="' . $ratingName . '" value="1" /><label for="' . $ratingName . '1" title="1 star">1</label>' . "\n" . '</div>';
        return $codeStyle . $code;
    }


    public static function toast($title, $description = '', $type = 'warning')
    {
        $uniqueId = "snack_bar_" . Math1::getUniqueId();
        $bg = Color1::get($type);
        return <<< EOSTRING
        <style>
            #$uniqueId {visibility: hidden;min-width: 250px;margin-left: -125px;background-color:$bg;color:white !important; text-align: center;border-radius: 2px;padding: 16px;position: fixed;z-index: 10099986765;left: 50%;bottom: 30px;font-size: 17px;}
            #$uniqueId.show { visibility: visible; -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s; animation: fadein 0.5s, fadeout 0.5s 2.5s;}
            @-webkit-keyframes fadein {from {bottom: 0; opacity: 0;} to {bottom: 30px; opacity: 1;}}
            @keyframes fadein {from {bottom: 0; opacity: 0;}to {bottom: 30px; opacity: 1;}}
            @-webkit-keyframes fadeout {from {bottom: 30px; opacity: 1;} to {bottom: 0; opacity: 0;}}
            @keyframes fadeout {from {bottom: 30px; opacity: 1;}to {bottom: 0; opacity: 0;}}
        </style>
         <!--<i class="fa fa-$ type" style="border:2px solid white;border-radius:50%;height:50px;width:50px;padding:8px;"></i>&nbsp;&nbsp;  -->
        <div id="$uniqueId"><h2 style="color:#f7f7f7">$title</h2> <p style="color:#f5f5f5">$description</p></div>
        <script> function myFunction() { var x = document.getElementById("$uniqueId"); x.className = "show"; setTimeout(function(){ x.className = x.className.replace("show", ""); }, 5000); }; myFunction();</script>
EOSTRING;
    }

    public static function box1($title = 'Add New Category', $body = 'Create new Category Now', $number = '', $actionLink = '#', $buttonName = 'Click Here', $class = 'col-md-3')
    {
        $shadow = HtmlStyle1::getShadow2x();
        return <<<HTML
            <div class="$class" style="$shadow; box-shadow:inset 0px 0px 15px rgba(0, 0, 0, .14); padding:10px;color: #636c71 !important;">
                <h3 style="">$title</h3>
                <div> <p>$body</p> <div class="clearfix"></div> </div>
                <div style="border-top:1px solid #eeeeee;"> <span class="badge badge-danger pull-left" style="margin-top:10px;">$number</span> <div class="text-right"><strong><a href="$actionLink" class="btn" style="font-weight:800;font-size: larger">$buttonName <i class="fa fa-chevron-circle-right fa-lg" aria-hidden="true"></i></a></strong></div> </div>
            </div>
HTML;
    }


    public static function box2($title = 'Create new Category Now', $number = '26', $panelStyleType = 'panel-primary', $panelIcon = 'fa fa-comments', $actionLink = '#', $buttonName = 'View Details', $colAndClass = 'col-md-3')
    {
        return <<<HTML
            <div class="$colAndClass">
                <div class="panel $panelStyleType">
                    <div class="panel-heading"> <div class="row">  <div class="col-xs-3"> <i class="$panelIcon  fa-5x"></i> </div> <div class="col-xs-9 text-right"> <div class="huge">$number</div> <div>$title</div> </div> </div> </div>
                    <a href="$actionLink"> <div class="panel-footer"> <span class="pull-left">$buttonName</span>  <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span> <div class="clearfix"></div> </div> </a>
                </div>
            </div>
HTML;
    }


    /**
     * example ob_start(); $userInfo->form()->render(); $dd = ob_get_clean();
     * echo footerPopup($dd, 'click me 1', '0'); echo footerPopup($dd, 'click me 1', '25');
     * @param string $content
     * @param string $name
     * @param string $align
     * @param string $contentStyle
     * @param string $widgetStyle
     * @param string $onClickScript
     * @return string
     */
    static function footerPopup($content = '', $name = 'Click Me!', $align = 'center', $contentStyle = '', $widgetStyle = '', $onClickScript = 'console.dir("dialog clicked");')
    {
        $uniqueId = 'footer_popup_' . Math1::getUniqueId();
        $script = '<script>$(function(){    $("#' . $uniqueId . ' .body").hide(); $("#' . $uniqueId . ' .button").click(function () { $(this).next("#' . $uniqueId . ' div").slideToggle(400); $(this).toggleClass("expanded"); ' . $onClickScript . '  });  });</script>';
        $style = "<style>#$uniqueId{z-index:200000;position:fixed; bottom:0;left:3%;width:94%; " . HtmlStyle1::getMarginCenter($align, true) . "; $widgetStyle}#$uniqueId .button:before{content:'+ '}#$uniqueId .expanded:before{content:'- '}#$uniqueId .button{font-size:1.1em;cursor:pointer;margin-left:auto;margin-right:auto;border:2px solid #e25454;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px 5px 0 0;padding:5px 20px 5px 20px;background-color:#e25454;color:#fff;display:inline-block;text-align:center;text-decoration:none;-webkit-box-shadow:4px 0 5px 0 rgba(0,0,0,0.3);-moz-box-shadow:4px 0 5px 0 rgba(0,0,0,0.3);box-shadow:4px 0 5px 0 rgba(0,0,0,0.3)}#$uniqueId .body{background-color:#fff;border-radius:5px;border:2px solid #e25454;margin-bottom:16px;padding:10px;-webkit-box-shadow:4px 4px 5px 0 rgba(0,0,0,0.3);-moz-box-shadow:4px 4px 5px 0 rgba(0,0,0,0.3);box-shadow:4px 4px 5px 0 rgba(0,0,0,0.3)}@media only screen and (min-width:768px){#$uniqueId .button{margin:0}#$uniqueId{left:20px;width:390px;text-align:left}#$uniqueId .body{overflow: auto !important; padding:30px;border-radius:0 5px 5px 5px; max-height:700px;$contentStyle}}</style>";
        return <<<HTML
                        <section id="$uniqueId"> $style.$script<div class="button">$name</div> <div class="body">$content</div></section>
HTML;
    }


    public static function listData($title = 'My List', $valueArray = [])
    {
        $class = "list_" . Math1::getUniqueId();
        $buffer = '<style>.' . $class . '{border-bottom:2px inset whitesmoke; list-style-type:none; padding:10px; padding-left:-20px !important; text-decoration: none !important; }' . '</style>
                    <ul class="list-group" style="' . HtmlStyle1::getShadow2x() . ';border-radius:10px; "> 
                        <li style="font-weight:800;font-size:larger; " class="' . $class . '">' . $title . '</li>';
        foreach ($valueArray as $value) $buffer .= "<li class='$class'>$value</li>";
        $buffer .= '</ul>';
        return $buffer;
    }


    public static function listDataKeyValue($title = 'My List', $keyValueArray = [])
    {
        $class = "list_" . Math1::getUniqueId();
        $buffer = '<style>.' . $class . '{border-bottom:2px inset whitesmoke; list-style-type:none; padding:10px; padding-left:-20px !important;}' . '</style>
                    <ul class="list-group" style="display: table;' . HtmlStyle1::getShadow2x() . ';box-shadow:inset 0px 0px 15px rgba(0, 0, 0, .14);border-radius:10px;"> 
                        <li style="font-weight:800;font-size:larger;" class="' . $class . '">' . $title . '</li>';
        foreach ($keyValueArray as $key => $value) $buffer .= "<li style='display: table-row' class='$class'><span class='display: table-cell'>$key</span>    <span class='display: table-cell'>$value</span></li>";
        $buffer .= '</ul>';
        return $buffer;
    }


    public static function listLink($title = 'My List', $listItem_menuNameEqualsMenuLink = [])
    {
        $currentPath = Url1::getPageFullUrl();
        $itemBuffer = [];
        foreach ($listItem_menuNameEqualsMenuLink as $item => $value) {
            if (is_numeric($item)) $itemBuffer[] = $value;
            else {
                $isSelectedStyle = Url1::existInUrl($value, $currentPath) ? 'font-weight:800;' : null;
                $itemBuffer[] = "<a class='btn btn-link' href='$value' style='$isSelectedStyle'>$item</a>";
            }
        }
        return self::listData($title, $itemBuffer);
    }

    public static function listCheckBox($title = 'My List', $name = '', $listItem = [])
    {
        $itemBuffer = [];
        foreach ($listItem as $item) $itemBuffer[] = "<label><input type='checkbox' name='" . $name . "[]' value='$item' /> &nbsp;$item</label>";
        return self::listData($title, $itemBuffer);
    }

    public static function listRadioButton($title = 'My List', $name = '', $listItem = [])
    {
        $itemBuffer = [];
        foreach ($listItem as $item) $itemBuffer[] = "<label><input type='radio' name='" . $name . "' value='$item' /> &nbsp;$item</label>";
        return self::listData($title, $itemBuffer);
    }

    static function textHeader($title = '', $description = '')
    {
        echo '<pre style="' . HtmlStyle1::getShadow2x() . ';direction: ltr; max-width: 90%; margin: 30px auto;overflow:auto; font-family: Monaco, Consolas, \'Lucida Console\',monospace;font-size: 16px;padding: 20px;
                       border-left:20px solid #2295bc;border-right:20px solid #2295bc; border-radius:20px; height:auto !important; 
                       white-space: pre-wrap;  white-space: -moz-pre-wrap;  white-space: -pre-wrap;  white-space: -o-pre-wrap;  word-wrap: break-word;
                       clear:both;top:0;background:#e4e7e7;color:#2295bc">' . (($title !== '') ? "<h2 align='left'>" . $title . '</h2><hr/>' : '') . print_r($description, true) . '</pre>';
    }

    static function flipperWidget($frontContent = '', $backContent = '')
    {

    }


    function articlePage($title = '', $body = '', $footer = '')
    {
        $code = '<style>body{text-align:center;padding:150px}h1{font-size:50px}body{font:20px Helvetica,sans-serif;color:#636363}article{display:block;text-align:left;width:650px;margin:0 auto}a{color:#dc8100;text-decoration:none}a:hover{color:#333;text-decoration:none}</style>';
        return "$code<article> <h1>$title</h1> <div>  <p>$body</p> <p>&mdash; $footer</p> </div> </article>";
    }
}

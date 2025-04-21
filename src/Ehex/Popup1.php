<?php


class Popup1
{
    // plugins
    // pnotify
    // swal

    static $TYPE_ERROR = "error";
    static $TYPE_WARNING = "warning";
    static $TYPE_SUCCESS = "success";
    static $TYPE_INFORMATION = "info";


    // variable
    public $title = '';
    public $body = [];
    public $type = '';


    // init data
    function __construct($title = '', $body = '', $type = 'info')
    {
        $this->setType($type);
        $this->setTitle($title);
        $this->setBody($body);

        return $this;
    }

    // set data
    function setDataFromArray($data = [])
    {
        if (!empty($data) && isset($data['title'])) return new self(@$data['title'], @$data['body'], @$data['info']);
    }

    function setData($title = '', $body = '', $type = 'info')
    {
        return new self($title, $body, $type);
    }

    function setTitle($title = '')
    {
        $this->title = $title;
        return $this;
    }

    function setBody($body = '')
    {
        if (String1::is_empty($body)) return '';
        $this->body[] = $body;
        return $this;
    }

    function setType($type = 'info')
    {
        $this->type = $type;
        return $this;
    }

    function addBody($body = '')
    {
        if (String1::is_empty($body)) return '';
        $this->body[] = $body;
        return $this;
    }


    // get data
    function issetData()
    {
        return (String1::is_empty($this->title) && (count($this->body) < 1)) ? false : true;
    }

    function getBody($listItemOpeningTag = '<li>', $listItemClosingTag = '</li>')
    {
        $itemList = '';
        foreach ($this->body as $item) {
            //if(is_array($item)) $itemList .=  $listItemOpeningTag.implode(' : ', $item).$listItemClosingTag;
            if (is_array($item) && (count($item) > 1)) {
                $itemListBuffer = '';
                for ($ii = 0; $ii < count($item); $ii++) {
                    $startCount = ($listItemOpeningTag == '') ? '(' . ($ii + 1) . ') ' : '';
                    $itemListBuffer .= $startCount . $listItemOpeningTag . String1::escapeQuotes(@$item[$ii]) . ' ' . $listItemClosingTag;
                }
                $itemList = $itemListBuffer;
            } else $itemList .= String1::toString(Array1::toArray(String1::escapeQuotes($item)), ' ');
        }
        return $itemList;
    }

    function getTitle()
    {
        return $this->title;
    }

    function getType()
    {
        return $this->type;
    }


    // dialog
    function toWindowsAlert()
    {
        if ($this->issetData()) Console1::popup($this->getTitle() . '\n' . $this->getBody('', ''));
    }

    function toToast($listItemOpeningTag = '', $listItemClosingTag = '')
    {
        if ($this->issetData()) echo HtmlWidget1::toast($this->getTitle(), $this->getBody($listItemOpeningTag, $listItemClosingTag), $this->getType());
    }

    function toHtmlList($listItemOpeningTag = '<li>', $listItemClosingTag = '</li>')
    {
        if ($this->issetData()) return "<div class='alert alert-" . $this->getType() . "> <h4><strong><i class='fa fa-$this->type'></i> $this->title</strong></h4><ol>" . $this->getBody($listItemOpeningTag, $listItemClosingTag) . "</ol> </div>";
        return null;
    }

    function toText($titleBreak = '<hr/>', $listItemOpeningTag = '', $listItemClosingTag = '<br/>')
    {
        if ($this->issetData()) return "$this->title $titleBreak" . $this->getBody($listItemOpeningTag, $listItemClosingTag);
        return null;
    }

    function toPanel($listItemOpeningTag = '<p>', $listItemClosingTag = '</p>')
    {
        /**if (!$this->issetData()) return;
         * ?>
         * <div class="panel panel-default panel-< ?php echo $this->getType() ?>">
         * <div class="panel-heading">< ?php echo $this->getTitle() ?></div>
         * <div class="panel-body"> < ?php echo $this->getBody($listItemOpeningTag, $listItemClosingTag ) ?> </div>
         * </div>
         * < ?php*/
    }


    /**
     *  Display Swal Alert with instance data
     * @param bool $wrapJQueryReadyScript
     * @param string $itemListOpenTag
     * @param string $itemListCloseTag
     * @return string
     */
    function toSwalAlert($wrapJQueryReadyScript = true, $itemListOpenTag = '<div style=\"padding:6px;border-bottom:1px solid #eeeeee\">', $itemListCloseTag = '</div>')
    {
        if (!$this->issetData()) return '';
        $response = sprintf('
                <script>
                    (function(){
                          swal({title:"%s", html:"%s", type:"%s"})
                    })($);
                </script>',
            $this->getTitle(),
            $this->getBody($itemListOpenTag, $itemListCloseTag),
            $this->getType()
        );
        echo $response;
    }


    /**
     * @param $title
     * @param string $data
     * @param string $type
     * @return string
     */
    static function swalAlert($title, $data = '', $type = 'info')
    {
        return sprintf('<script> (function(){ swal("%s", "%s", "%s") })($);</script>', $title, $data, $type);
    }
}

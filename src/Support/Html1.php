<?php

/**
 * Alter Html Content
 * Remove Tag, Filter Form, Encode and decode
 * Class Html1
 */
class Html1
{
    private $dataTorender = [];

    /**
     * Enclose Html for render() method to render it.
     * Use mostly for ehex manage render();
     * Html1 constructor.
     * @param callable|null $dataTorender
     */
    public function __construct(callable $dataTorender = null)
    {
        return $this->append($dataTorender);
    }

    /**
     * Render constructor input
     * @return null|string
     */
    public function render(...$param)
    {
        $result_list = '';
        foreach ($this->dataTorender as $callableMethod)
            if (is_callable($callableMethod))
                $result_list .= String1::toString(call_user_func($callableMethod, ...$param));
        return $result_list;
    }


    /**
     * Enclose Html for render() method to render it.
     * Use mostly for ehex manage render();
     * @param callable|null $dataTorender
     */
    public function append(callable $dataTorender = null)
    {
        $this->dataTorender[] = $dataTorender;
        return $this;
    }


    /**
     * Util class for removing Html tag
     * @param $htmlContent
     * @param array $ignoreTag
     * @return null|string|string[]
     */
    static function removeTag($htmlContent, $ignoreTag = array())
    {
        $ignoreTag = array_map('strtolower', $ignoreTag);
        $rhtml = preg_replace_callback('/<\/?([^>\s]+)[^>]*>/i', function ($matches) use (&$ignoreTag) {
            return in_array(strtolower($matches[1]), $ignoreTag) ? $matches[0] : '';
        }, $htmlContent);
        return $rhtml;
    }


    /**
     * set/change attribute for tag
     * @param string $htmlContent
     * @param string $tagName
     * @param string $attribute
     * @return null|string|string[]
     */
    static function setTagAttribute($htmlContent = '<a href="https://www.xamtax.com" class="cw-link" title="xamtax">Visit xamtax</a>', $tagName = 'a', $attribute = 'style="color:red"')
    {
        return preg_replace("/(<$tagName\b[^><]*)>/i", "$1 $attribute>", $htmlContent);
    }

    /**
     * Render content in an iframe
     * @param $htmlContent
     * @param $style
     * @return string
     */
    public static function renderAsIframe($htmlContent, $style)
    {
        $htmlContent = htmlspecialchars($htmlContent);
        return <<<HTML
            <iframe srcdoc="$htmlContent" allowtransparency="true" onload="this.style.height=(this.contentDocument.body.scrollHeight+45) +'px';" style="$style; width:100%;border:none;overflow-y:hidden;overflow-x:hidden;"></iframe>
        HTML;
    }

}

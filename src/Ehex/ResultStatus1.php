<?php


/**
 * Class ResultStatus1
 *  ResultStatus1  could be use as Result for method [just to return text, number and boolean], could be boolean by defauit and any of its method could be accessible as well
 *      $result = ResultStatus1::make(true, 'data loading...', ['some data']);
 * if($result) echo 'Working...';
 * else echo $result->message();
 * Because it does not work well with Object returning by default, Therefore, do Not Use with Api, Use ResultObject1 Instead
 * @see ResultMethod1
 */
class ResultStatus1 extends \SimpleXMLElement
{
    /**
     * @return mixed|null
     */
    // The SimpleXMLElement Hack Secrete to return many value
    private function getParams()
    {
        preg_match("#<!\-\-(.+?)\-\->#", $this->asXML(), $matches);
        if (!$matches) return null;
        return unserialize(html_entity_decode(String1::toString($matches[1])));
    }

    /**
     * @param $status
     * @param $data
     * @return ResultStatus1
     */
    private static function setParams($status, $data)
    {
        $xml = '<!--' . htmlentities(serialize($data)) . "-->" . (($status) ? '<true>1</true>' : '<false/>');
        return new self($xml);
    }


    /**
     * @param bool $status
     * @param string $message
     * @param null $data
     * @param array $tag
     * @return ResultStatus1
     */
    static function make($status = true, $message = "", $data = null, $code = 0, $tag = [])
    {
        $newS = self::setParams($status, ['message' => $message, 'data' => $data, 'code' => $code, 'tag' => $tag,]);
        return $newS;
    }

    // Get Result
    function getStatus()
    {
        return ($this != false);
    }

    function getMessage()
    {
        return ($this->getParams()['message']);
    }

    function getData()
    {
        return ($this->getParams()['data']);
    }

    function getTag()
    {
        return ($this->getParams()['tag']);
    }

    function getCode()
    {
        return ($this->getParams()['code']);
    }

    /**
     * @return Popup1
     */
    function toPopup()
    {
        return (new Popup1(($this->getStatus() ? 'Action Successful' : 'Action Failed'), ($this->getStatus() ? '' : $this->getMessage()), ($this->getStatus() ? 'success' : 'error')));
    }

    // Quick Make
    static function falseMessage($message = '', $code = 400)
    {
        return self::make(false, $message, $message, $code);
    }

    static function trueData($data = null)
    {
        return self::make(true, is_string($data) ? String1::getSomeText($data, 150) : 'Done', $data, 200);
    }

    static function catchError(callable $runCallBackMethod)
    {
        try {
            $result = $runCallBackMethod();
            return self::make(!!$result, is_string($result) ? $result : 'Done', $result, 200);
        } catch (Exception $ex) {
            return self::make(false, $ex->getMessage(), $ex->getMessage(), 400);
        }
    }
}
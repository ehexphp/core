<?php

/**
 * Class ResultObject1 for Api return result
 *  ResultObject1  could be use as Result to return Object
 *      $result = ResultStatus1::make(true, 'data loading...', ['some data']);
 * if($result->getStatus()) echo 'Working...';
 * else echo $result->getMessage();
 * Use mostly With Api, because it allows status and result together
 * @see ResultStatus1
 */
class ResultObject1
{
    public $status = false;
    public $message = "";
    public $data = "";
    public $code = "";

    public function __construct($m_status = true, $m_message = "", $m_data = "", $code = 200)
    {
        $this->status = (bool)$m_status;
        $this->message = String1::getSomeText(String1::toString($m_message), 150);
        $this->data = $m_data;
        $this->code = $code;
    }

    public function toArray()
    {
        return ['status' => $this->status, 'message' => $this->message, 'data' => $this->data, 'code' => $this->code];
    }

    public function toHtml()
    {
        return " <h4>Status</h4><p>$this->status " . ($this->code > 0 ? ($this->code) : '') . "</p> <br/><h4>Status Message</h4><p>$this->message</p> <br/> <h4>Result Data</h4><p>" . String1::toArrayTree($this->data) . "</p>";
    }

    public function __toString()
    {
        return "{Status:" . String1::toBoolean($this->status, 'true', 'false') . " ($this->code), Message:" . '"' . $this->message . '"' . ", Data:" . String1::toArrayTree($this->data) . "}";
    }


    function getStatus()
    {
        return ($this->status);
    }

    function getCode()
    {
        return ($this->code);
    }

    function getMessage()
    {
        return ($this->message);
    }

    function getData()
    {
        return ($this->data);
    }

    static function data($data = null)
    {
        return static::make(!!$data, method_exists($data, 'message') ? $data->message() : '', $data, $data ? 200 : 400);
    }

    static function falseMessage($message = '', $code = 400)
    {
        return new self(false, $message, $message, $code);
    }

    static function trueData($data = null)
    {
        return new self(true, is_string($data) ? $data : "Done", $data);
    }

    static function make($m_status = true, $m_message = "", $m_data = null, $code = 200)
    {
        return new self($m_status, $m_message, $m_data, $code);
    }

    static function catchError(callable $runCallBackMethod)
    {
        try {
            $result = $runCallBackMethod();
            return self::make(!!$result, $result, $result);
        } catch (Exception $ex) {
            return self::make(false, $ex->getMessage(), $ex->getMessage(), $ex->getCode());
        }
    }
}
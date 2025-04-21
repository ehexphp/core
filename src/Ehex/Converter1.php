<?php

class Converter1
{
    /**
     * Email Template Parser Class.
     * @param string $templateHtml_or_filePath HTML template string OR File path to a Email Template file.
     * @param array $param ['userName'=>'Samson Iyanu', 'email'=>'samsoniyau@hotmail.com']
     * @return null|string
     */
    public static function bladeTemplateToHtml($templateHtml_or_filePath, $param = [])
    {
        $_openingTag = '{{';
        $_closingTag = '}}';
        $_valueList = [];

        try {

            if (file_exists($templateHtml_or_filePath)) $_template = file_get_contents($templateHtml_or_filePath);// Template HTML is stored in a File
            else if (is_string($templateHtml_or_filePath)) $_template = $templateHtml_or_filePath; // Template HTML is stored in-line in the $emailTemplate property!
            else throw new Exception('ERROR: Invalid Template.  $template must be a String or else a FilePath');

            // load Parameter
            if (is_array($param)) foreach ($param as $key => $value) $_valueList[$key] = $value;
            else throw new Exception('ERROR: Must be an ARRAY.');

            // output
            $html = $_template;
            foreach ($_valueList as $key => $value) {
                if (isset($value) && $value != '')
                    // Better Regex Required for Better Parse
                    $html = str_replace($_openingTag . $key . $_closingTag, $value, $html);
            }
            return $html;
        } catch (Exception $e) {
            echo $e->getMessage() . ' | FILE: ' . $e->getFile() . ' | LINE: ' . $e->getLine();
        }
        return null;
    }


}
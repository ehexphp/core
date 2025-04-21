<?php

class Console1
{
    /**
     *  Output Object | Array | String or any data type in a fancy PRE
     * @param string $text
     * @param bool $print_stopPageAndDie
     * @param string $title
     * @return string
     */
    static function println($text = '', $print_stopPageAndDie = false, $title = '')
    {
        if ($text === '') die('<br><hr><h3 align="center"> Break - [ ' . date(DateManager1::date(DateManager1::$time_as24Hours)) . ' ] </h3><br></hr>');
        if (is_string($print_stopPageAndDie)) {
            $title = $print_stopPageAndDie;
            $print_stopPageAndDie = true;
        };

        echo '<pre class="console1_println" style="direction: ltr; max-width: 90%; margin: 30px auto;overflow:auto; font-family: Monaco, Consolas, \'Lucida Console\',monospace;font-size: 16px;padding: 20px;
                       border-left:20px solid #2295bc;border-right:20px solid #2295bc; border-radius:20px; height:auto !important; 
                       white-space: pre-wrap;  white-space: -moz-pre-wrap;  *white-space: pre-wrap;  white-space: -o-pre-wrap;  word-wrap: break-word;
                       clear:both;top:0;position: relative;z-index: 9999999999;background:#e4e7e7;color:#2295bc">' . (($title !== '') ? "<h2>" . $title . '</h2><hr/>' : '') . print_r($text, true) . '</pre>';

        if ($print_stopPageAndDie) die('');
        return '';
    }

    static function log($data, $title = "Ehex Info")
    {
        echo "<script> console.log('-[$title]-'); console.dir('" . String1::toString($data, ', ') . "'); </script>";
    }

    static function popupAny($obj)
    {
        echo "<script> alert('" . String1::toArrayTree($obj) . "'); </script>";
    }


    static function d($data)
    {
        if (is_null($data)) {
            $str = "<i>NULL</i>";
        } elseif ($data == "") {
            $str = "<i>Empty</i>";
        } elseif (is_array($data)) {
            if (count($data) == 0) {
                $str = "<i>Empty array.</i>";
            } else {
                $str = "<table style=\"border-bottom:0px solid #000;\" cellpadding=\"0\" cellspacing=\"0\">";
                foreach ($data as $key => $value) {
                    $str .= "<tr><td style=\"background-color:#008B8B; color:#FFF;border:1px solid #000;\">" . $key . "</td><td style=\"border:1px solid #000;\">" . self::d($value) . "</td></tr>";
                }
                $str .= "</table>";
            }
        } elseif (is_resource($data)) {
            while ($arr = mysqli_fetch_array($data)) {
                $data_array[] = $arr;
            }
            $str = self::d($data_array);

        } elseif (is_object($data)) {
            $str = self::d(get_object_vars($data));
        } elseif (is_bool($data)) {
            $str = "<i>" . ($data ? "True" : "False") . "</i>";
        } else {
            $str = $data;
            $str = preg_replace("/\n/", "<br>\n", $str);
        }
        return $str;
    }


    static function dd($data)
    {
        self::d($data);
        echo "<hr/>[" . date("Y/m/d H:i:s") . "]<hr>\n";
        return exit ? '' : '';
    }
}
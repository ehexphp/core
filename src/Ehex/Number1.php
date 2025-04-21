<?php

class Number1
{
    /**
     * using date help
     */
    static function isNumber($value)
    {
        return is_numeric($value);
    }

    static function getUniqueId()
    {
        return date(time() . rand(100, 999));
    }

    static function sortNumbers($list = [], $sort_flag = null)
    {
        sort($list, $sort_flag);
        return $list;
    }

    static function getRandomNumber($max = 2, $min = 0, $lenght = 1)
    {
        $num = '';
        foreach (range(1, $lenght) as $_) $num .= rand($min, $max) . '';
        return (int)$num;
    }

    static function getDateId()
    {
        return date("Ymd_h1s");
    }

    static function toSizeUnit($sizeValue)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($sizeValue / pow(1024, ($i = floor(log($sizeValue, 1024)))), 2) . ' ' . $unit[$i];
    }


    static function formatNumber($number, $fractional = false, $decimalPlaces = 2)
    {
        if ($fractional) $number = sprintf("%.{$decimalPlaces}f", $number);
        while (true) {
            $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
            if ($replaced != $number) $number = $replaced;
            else break;
        }
        return $number;
    }

    static function toDecimalPlace($value, $decimalPlace = 2)
    {
        return String1::replace(
            Math1::formatNumber(Math1::toMoney($value, ""), true, $decimalPlace)
            , ",", ""
        );
    }

    static function toMoney($val, $symbol = '₦', $r = 2)
    {
        if (String1::contains('.', $val)) {
            return $val;
        }

        $zeros = "." . String1::repeat("0", $r);
        if (($val == 0) || (self::filterNumber_regex((integer)$val) == 0)) {
            return $symbol . $val . $zeros;
        }

        $val = self::filterNumber_regex((integer)$val);
        // algorithm
        $money = self::formatNumber($val);
        $r = (!String1::contains('.', $money)) ? $zeros : '';
        if ($money != '') {
            return $symbol . $money . $r;
        }
    }


    /**
     * @param $valueB
     * @param $valueA_orTotalValue
     * @param int $percentageIs
     * @return int (return only percentage B of As. e.g percetage 5 of 40 = 12.5%)
     */
    static function getPercentageBofA($valueB, $valueA_orTotalValue, $percentageIs = 100)
    {
        return (($valueB / $valueA_orTotalValue) * $percentageIs);
    }

    /**
     * @param $value
     * @param int $percentage
     * @param int $percentageIs
     * @param bool $noNegativeNumber
     * @return int (return only percentage of the value passed in)
     */
    static function getPercentageValue($value, $percentage = 10, $noNegativeNumber = true, $percentageIs = 100)
    {
        if ($percentage == 0) return 0;
        $perc = ($value * ($percentage / $percentageIs));
        return ($noNegativeNumber) ? (($perc <= 0) ? $value : $perc) : $perc;
    }


    /**
     * @param $value
     * @param int $percentage
     * @param bool $noNegativeNumber
     * @return array ( array that consist of min, max, percentage or discount, maxFake, and value keys)
     */
    static function getValueMinMaxByPercentage($value, $percentage = 10, $noNegativeNumber = true)
    {
        $discount = ($value * ($percentage / 100));
        $min = ($value - $discount);
        $min = ($noNegativeNumber) ? (($min <= 0) ? $value : $min) : $min;
        $max = ($value + $discount);
        $value = ($value);
        return array(
            'min' => $min,
            'max' => $max,
            'maxFake' => ($max - ($min - $discount)),
            'percentage' => ($discount),
            'discount' => ($percentage),
            'value' => $value
        );
    }


    /**
     * @param array ...$list
     * @return float (use when you have more than one percentage to deal with);
     * e.g (find average percentage of 60%,40%. just  convert the two number to
     * decimal by dividing them with 100, so u get 0.6 & 0.4, then add the two
     * numbers then divide the result by 2 i.e 0.5, finally multiply he result with 100)
     */
    static function getAverage($list, $inPercentage = true)
    {
        $sum = 0;
        foreach ($list as $percentage) $sum += $inPercentage ? (((float)$percentage) / 100) : $percentage;
        return ($sum / count($list)) * $inPercentage ? (100) : 1;
    }

    /**
     * @param $value (that must exists between $min and $max)
     * @param $min
     * @param $max
     * @return bool
     */
    public static function isInRange($value, $min, $max)
    {
        if ($value >= $min && $value <= $max) return true;
        return false;
    }

    public static function filterNumber_regex($numberString)
    {
        return preg_replace("/\D/", "", $numberString);
    }

    public static function filterNumber($numberString)
    {
        return self::toNumber(utf8_encode($numberString));
    }

    /**
     * @param $numberString
     * @return number
     */
    public static function toNumber($numberString)
    {
        return filter_var($numberString, FILTER_SANITIZE_NUMBER_INT);
    }

    static function encodeToShortAlphaNum($n, $codeSet = "23456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ")
    {
        $base = strlen($codeSet);
        $converted = '';
        while ($n > 0) {
            $converted = substr($codeSet, bcmod($n, $base), 1) . $converted;
            $n = bcmul(bcdiv($n, $base), '1', 0); //self::bcFloor(bcdiv($n, $base));
        }
        return ($converted);
    }

    static function decodeFromShortAlphaNum($code, $codeSet = "23456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ")
    {
        $base = strlen($codeSet);
        $c = '0';
        for ($i = strlen($code); $i; $i--)
            $c = bcadd($c, bcmul(strpos($codeSet, substr($code, (-1 * ($i - strlen($code))), 1)), bcpow($base, $i - 1)));
        return (bcmul($c, 1, 0));
    }


    /**
     * @param $number
     * @param bool $strictlyNumber
     * @return string
     *
     * If you want to convert an integer into an English word string, eg. 29 -> twenty-nine, then here's a function to do it
     * Note on use of fmod()
     *   I used the floating point fmod() in preference to the % operator, because % converts the operands to int, corrupting values outside of the range [-2147483648, 2147483647]
     * I haven't bothered with "billion" because the word means 10e9 or 10e12 depending who you ask.
     * The function returns '#' if the argument does not represent a whole number.
     */
    public static function toWord($number, $strictlyNumber = true)
    {
        if ($strictlyNumber === true) $number = self::filterNumber_regex($number);
        $nwords = ["zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", "ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "nineteen", "twenty", 30 => "thirty", 40 => "forty", 50 => "fifty", 60 => "sixty", 70 => "seventy", 80 => "eighty", 90 => "ninety"];
        if (!is_numeric($number)) $w = '#';
        else if (fmod($number, 1) != 0) $w = '#';
        else {
            if ($number < 0) {
                $w = 'minus ';
                $number = -$number;
            } else
                $w = '';
            // ... now  $number is a non-negative integer.
            if ($number < 21)   // 0 to 20
                $w .= $nwords[$number];
            else if ($number < 100) {   // 21 to 99
                $w .= $nwords[10 * floor($number / 10)];
                $r = fmod($number, 10);
                if ($r > 0) $w .= '-' . $nwords[$r];
            } else if ($number < 1000) {   // 100 to 999
                $w .= $nwords[floor($number / 100)] . ' hundred';
                $r = fmod($number, 100);
                if ($r > 0) $w .= ' and ' . self::toWord($r);
            } else if ($number < 1000000) {   // 1000 to 999999
                $w .= self::toWord(floor($number / 1000)) . ' thousand';
                $r = fmod($number, 1000);
                if ($r > 0) {
                    $w .= ' ';
                    if ($r < 100) $w .= 'and ';
                    $w .= self::toWord($r);
                }
            } else {
                $w .= self::toWord(floor($number / 1000000)) . ' million';
                $r = fmod($number, 1000000);
                if ($r > 0) {
                    $w .= ' ';
                    if ($r < 100) $w .= 'and ';
                    $w .= self::toWord($r);
                }
            }
        }
        return $w;
    }


    /**
     * Is integer even number
     * @param $number
     * @return bool
     */
    static function isEven($number)
    {
        return ($number % 2 == 0);
    }

    /**
     * Checks if the provided integer is a prime number.
     * isPrime(3); // true
     * @param $number
     * @return bool
     */
    static function isPrime($number)
    {
        $boundary = floor(sqrt($number));
        for ($i = 2; $i <= $boundary; $i++) if ($number % $i === 0) return false;
        return $number >= 2;
    }

    /**
     * Checks if two numbers are approximately equal to each other.
     * Use abs() to compare the absolute difference of the two values to epsilon. Omit the third parameter, epsilon, to use a default value of 0.001.
     * approximatelyEqual(10.0, 10.00001); // true
     * approximatelyEqual(10.0, 10.01); // false
     * @param $number1
     * @param $number2
     * @param float $epsilon
     * @return bool
     */
    static function isApproximatelyEqual($number1, $number2, $epsilon = 0.001)
    {
        return abs($number1 - $number2) < $epsilon;
    }


    /**
     * Returns the n minimum elements from the provided array.
     * @param array $numbers
     * @return int
     */
    static function getMinNumber(array $numbers)
    {
        if (empty($numbers)) return false;
        $smallNumb = $numbers[0];
        foreach ($numbers as $num) if ($num < $smallNumb) $smallNumb = $num;
        return $smallNumb;
    }


    /**
     * Returns the n maximum elements from the provided array.
     * @param $numbers
     * @return int
     */
    static function getMaxNumber(array $numbers)
    {
        /*$maxValue = max($numbers);
        $maxValueArray = array_filter($numbers, function ($value) use ($maxValue) {
            return $maxValue === $value;
        });
        return count($maxValueArray);*/
        if (empty($numbers)) return false;
        $largeNumb = $numbers[0];
        foreach ($numbers as $num) if ($num > $largeNumb) $largeNumb = $num;
        return $largeNumb;
    }


    /**
     * Returns the median of an array of numbers.
     *  median([1, 3, 3, 6, 7, 8, 9]); // 6
     * median([1, 2, 3, 6, 7, 9]); // 4.5
     * @param array $numbers
     * @return float|int|mixed
     */
    static function getMedian(array $numbers)
    {
        sort($numbers);
        $totalNumbers = count($numbers);
        $mid = floor($totalNumbers / 2);
        return ($totalNumbers % 2) === 0 ? ($numbers[$mid - 1] + $numbers[$mid]) / 2 : $numbers[$mid];
    }


    /**
     * Returns the least common multiple of two or more numbers.
     * lcm(12, 7); // 84
     * lcm(1, 3, 4, 5); // 60
     * @param mixed ...$numbers
     * @return float|int|mixed
     */
    static function getLCM(...$numbers)
    {
        $ans = $numbers[0];
        for ($i = 1; $i < count($numbers); $i++) $ans = ((($numbers[$i] * $ans)) / (gcd($numbers[$i], $ans)));
        return $ans;
    }

    /**
     * Calculates the greatest common divisor between two or more numbers.
     *  gcd(8, 36); // 4
     * gcd(12, 8, 32); // 4
     * @param mixed ...$numbers
     * @return float|int|mixed
     */
    static function getGCD(...$numbers)
    {
        if (count($numbers) > 2) {
            return array_reduce($numbers, 'gcd');
        }
        $r = $numbers[0] % $numbers[1];
        return $r === 0 ? abs($numbers[1]) : gcd($numbers[1], $r);
    }


    /**
     * Generates an array, containing the Fibonacci sequence, up until the nth term.
     * fibonacci(6); // [0, 1, 1, 2, 3, 5]
     * @param $n
     * @return array
     */
    static function fibonacci($n)
    {
        $sequence = [0, 1];
        for ($i = 2; $i < $n; $i++) {
            $sequence[$i] = $sequence[$i - 1] + $sequence[$i - 2];
        }
        return $sequence;
    }


    /**
     * Calculates the factorial of a number.
     * factorial(6); // 720
     * @param $n
     * @return float|int
     */
    static function factorial($n)
    {
        if ($n <= 1) {
            return 1;
        }
        return $n * factorial($n - 1);
    }

    /**
     * Returns the average of two or more numbers.
     * average(1, 2, 3); // 2
     * @param mixed ...$items
     * @return float|int
     */
    static function average(...$items)
    {
        return count($items) === 0 ? 0 : array_sum($items) / count($items);
    }

    /**
     * @param int $amount
     * @param string $currency
     * @return bool|string
     */
    static function convertCurrencyToBitcoin($amount = 500, $currency = 'USD')
    {
        return file_get_contents("https://blockchain.info/tobtc?currency=$currency&value=$amount");
    }

    /**
     * Used Pagination ...other [1,2,4,5] to get
     * small and dynamic CurrentIndex surroundig value
     * @param $totalValueCount
     * @param int $currentIndex
     * @param int $maxPageCount
     * @return array
     */
    public static function getSurroundingValues($totalValueCount, $currentIndex = 1, $maxPageCount = 6)
    {
        // init
        $total = $totalValueCount;
        if ($currentIndex > $total) $currentIndex = $total;
        if ($currentIndex < 0) $currentIndex = 0;
        $maxPageCount = $maxPageCount > $total && $total > 10 ? $total / 2 : $maxPageCount;

        // separate
        $backCount = $frontCount = floor($maxPageCount / 2);
        $backList = $frontList = [];
        // normalizer
        if (($frontCount + $currentIndex) >= $total) {
            $frontCount = (($frontCount + $currentIndex) - $frontCount) - $total;
            $backCount = (($backCount + $currentIndex) + $backCount) - $total;
        }
        // signed to positive unsigned
        $frontCount = abs($frontCount);
        $backCount = abs($backCount);
        // for backward nav
        for ($i = $backCount; $i > 0; $i--) {
            if ($currentIndex - $i >= 1) $backList[] = $currentIndex - $i;
            else $frontCount++;
        }
        // for forward nav
        for ($i = 0; $i < $frontCount + 1; $i++) {
            $cur = $currentIndex + $i <= $totalValueCount ? $currentIndex + $i : null;
            if ($cur) $frontList[] = $cur;
        }
        // array merge
        $more = array_merge($backList, $frontList);
        return $more;
    }

}
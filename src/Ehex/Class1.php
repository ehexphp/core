<?php

class Class1
{

    /**
     * @param array|object $object_or_array
     * @return mixed
     * <p>
     *     this can be use to merge php class object together,
     *     to make object behave like array and array like object,
     *     and also show all functions in each object.
     *     to add more function
     *         Example
     *             $flexibleObject = Class1::toArrayObject(true,  (new User), (new Picture) );
     *             $flexible->newMethod = function (){
     *                 return 'hello world';
     *             }
     *
     *
     *             echo $flexibleObject->fullName;
     *                         OR
     *             echo $flexibleObject['fullName'];
     *                         OR
     *             Console1::print( $flexibleObject );
     * </p>
     */
    public static function toArrayObject($addClassMethods, ...$object_or_array)
    {
        $object_or_array = !is_bool($addClassMethods) && !$object_or_array ? [$addClassMethods] : $object_or_array;
        // create flexible ArrayObject that allows onFly Method to be added
        $flexibleObject = null;
        for ($i = 0; $i < count($object_or_array); $i++) {
            if ($i === 0) $flexibleObject = new exArrayObject1($object_or_array[$i]); // new ArrayObject($object_OR_array, ArrayObject::ARRAY_AS_PROPS)
            else $flexibleObject->addObject($object_or_array[$i]); // merge multiple object
        }
        // Add List Of Existing Methods in '$object_or_array' to Current Object
        if ($addClassMethods) {
            foreach ($object_or_array as $object) {
                if (is_object($object)) {
                    foreach (get_class_methods($object) as &$method) { //// get_object_vars($clunker)
                        $flexibleObject->{$method} = function (...$param) use ($object, $method) {
                            return call_user_func_array([($object), $method], $param);
                        };
                    }
                }
            }
        }
        return $flexibleObject;
    }


    static function cast($object, $class = 'object', $addMethods = false)
    {
        if (!class_exists($class)) $class = __NAMESPACE__ . "\\$class";
        if (!class_exists($class)) throw new InvalidArgumentException(sprintf('Unknown class: %s.', $class));

        // case with serialization
        $newObject = @unserialize(
            preg_replace(
                '/^O:\d+:"[^"]++"/',
                'O:' . @strlen($class) . ':"' . $class . '"',
                serialize($object)
            )
        );

        // add methods
        if ($addMethods && is_object($object)) {
            foreach (get_class_methods(new $class) as &$method) {
                $newObject->{$method} = function (...$param) use ($object, $method) {
                    return call_user_func_array([($object), $method], $param);
                };
            }
        }

        return $newObject;
    }

    /**
     * Get current Method parament Information.
     * Pass in debug_backtrace like this. var_dump( Object1::getCurrentMethodParams( debug_backtrace(null, 2)[1]) );
     * @param null $debug_backtrace_instance
     * @return array
     * @throws ReflectionException
     */
    static function getCurrentMethodParams($debug_backtrace_instance = null, $defaultArgs = true)
    {
        try {
            $methodInfo = $debug_backtrace_instance ? $debug_backtrace_instance : debug_backtrace(null, 2)[1];
            if ($defaultArgs) return self::getMethodParams($methodInfo['function'], $methodInfo['class']);
            // pie args
            $params = [];
            foreach ((new \ReflectionClass($methodInfo['class']))->getMethod($methodInfo['function'])->getParameters() as $k => $parameter) $params[$parameter->name] = isset($methodInfo['args'][$k]) ? $methodInfo['args'][$k] : $parameter->getDefaultValue();
            return $params;
        } catch (Exception $ex) {
            return [];
        }
    }


    /**
     * @param $class
     * @param $method
     * @param array $overriderWith
     * @param bool $paramNameAsIndex
     * @throws ReflectionException
     */
    static function getMethodParams($method, $class = null, $overrideKeyValue = [], $paramNameAsIndex = true)
    {
        $r = $class ? new ReflectionMethod($class, $method) : new ReflectionFunction($method);
        $neededParam = [];
        foreach ($r->getParameters() as $param) {
            $paramName = $param->getName();
            $paramPrimaryValue = String1::isset_or($overrideKeyValue[$paramName], null);
            $paramValue = String1::isset_or($overrideKeyValue[$paramName], $param->isOptional() ? $param->getDefaultValue() : null);
            if (empty($overrideKeyValue)) {
                if ($paramNameAsIndex) $neededParam[$paramName] = $paramValue;
                else $neededParam[] = $paramValue;
            } else {
                if ($paramNameAsIndex) { // optional is not present in self::request(), hence
                    //if($param->isOptional() && empty($paramPrimaryValue)) $neededParam[$paramName] = $paramValue;
                    //else
                    $neededParam[$paramName] = $paramValue;
                } else {
                    //if($param->isOptional() && empty($paramPrimaryValue)) $neededParam[] = $paramValue;
                    //else
                    $neededParam[] = $paramValue;
                }
            }
        }
        return $neededParam;
    }


    /**
     * Convert Array to Object
     * @param $array
     * @param null $className
     * @param bool $addMethod
     * @return bool|mixed
     * @see convertArrayToObject()
     */
    static function toObject($array, $className = null, $addMethod = false)
    {
        return self::convertArrayToObject($array, $className, $addMethod);
    }

    /**
     * Convert Object to array
     * @param $object
     * @return mixed
     * @see convertObjectToArray()
     */
    static function toArray($object)
    {
        return is_array($object) ? $object : json_decode(json_encode($object), 1);
    }


    /**
     * Convert Object to Array
     * @param $object
     * @return array
     * @see toArray()
     */
    static function convertObjectToArray($object)
    {
        if (is_array($object)) return $object;
        $_arr = is_object($object) ? get_object_vars($object) : $object;
        $arr = array();
        foreach ($_arr as $key => $val) {
            $val = (is_array($val) || is_object($val)) ? self::convertObjectToArray($val) : $val;
            $arr[$key] = $val;
        }
        return $arr;
    }


    /**
     * Convert array to object
     * @param $array
     * @param string $className
     * @param bool $addMethod
     * @return bool|mixed|$class
     * @see toObject()
     */
    static function convertArrayToObject($array, $className = null, $addMethod = false)
    {
        if (String1::startsWith($className, 'class@anonymous')) return $array;
        $value = json_decode(json_encode($array), FALSE);
        return ($className) ? self::cast($value, $className, $addMethod) : $value;
    }


    /**
     * get unique hashcode key for object
     * @param $obj
     * @return string
     */
    static function hashCode($obj)
    {
        return spl_object_hash($obj);
    }

    /**
     * Combine many Object into one
     * @param mixed ...$object_or_array
     * @return bool|mixed
     */
    static function mergeObject(...$object_or_array)
    {
        $className = '';
        $objArray = [];
        for ($i = 0; $i < count($object_or_array); $i++) {
            if ($i === 0) $className = get_class($object_or_array[$i]);
            $objArray = array_merge($objArray, (array)$object_or_array[$i]); // merge multiple object
        }
        return self::convertArrayToObject($objArray, $className);
    }


    /**
     * Get list of object variable available
     * @param $object
     * @return array
     */
    static function getClassObjectVariables($object)
    {
        return get_object_vars($object);
    }

    /**
     * both static and object variables
     * @param $object
     * @return array
     */
    static function getClassVariables($object)
    {
        return get_class_vars(get_class($object));
    }

    /**
     * It relies on an interesting property: the fact that get_object_vars only returns the non-static variables of an object.
     * @param $object
     * @return array
     */
    static function getClassStaticVariables($object)
    {
        //print_r( array_diff(self::getAllClassVariables($object), self::getClassVariables($object)) );
        if (is_string($object)) $object = new $object;
        return array_diff(get_class_vars(get_class($object)), get_object_vars($object));
    }


    /**
     * Get Session Executed Class by Name or by Parent Class with debug_backtrace()
     * @param array $classList
     * @param callable $onFoundCallBack
     * @param bool $searchParentClass
     * @return array
     */
    static function getExecutedClass($classList = [], $searchParentClass = false, callable $onFoundCallBack = null)
    {
        $classPie = [];
        foreach (debug_backtrace() as $calledClassInfo) {
            foreach (Array1::makeArray($classList) as $class) {
                if (isset($calledClassInfo['class']) && $calledClassInfo['class']) {
                    if ($calledClassInfo['class'] == $class || ($searchParentClass && self::isParentClassExistIn($calledClassInfo['class'], $class))) {
                        if ($onFoundCallBack) $onFoundCallBack($calledClassInfo['class']);
                        $classPie[] = $calledClassInfo['class'];
                    }
                }
            }
        }
        return $classPie;
    }


    /**
     * Find Parent Class
     * @param $class
     * @param $parentClass
     * @return bool
     */
    static function isParentClassExistIn($class, $parentClass)
    {
        return in_array($parentClass, class_parents($class));
    }

    /**
     * Find Parent Implementation
     * @param null $className
     * @param null $parentInterface
     * @return bool
     */
    static function isInterfaceImplementExistIn($className = null, $parentInterface = null)
    {
        return in_array($parentInterface, class_implements($className));
    }

    /**
     * check if class exists and match condition then return them
     *  $availableClass = Class1::getClassesIf(function($class){ return $class::isTableExists(); }, 'Inbox', 'User');
     * @param callable $filterCallback
     * @param array $classList
     * @return array
     */
    static function getClassesIf(callable $filterCallback = null, ...$classList)
    {
        $classes = [];
        foreach ($classList as $av) {
            if (class_exists($av) && ($filterCallback ? $filterCallback($av) : true)) $classes[] = $av;
        }
        return $classes;
    }

}
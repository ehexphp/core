<?php

class ArrayObject1 extends \ArrayObject
{

    public function __get($index)
    {
        if ($this->offsetExists($index)) return $this->offsetGet($index); else  return null;
    }//throw new UnexpectedValueException('Undefined key ' . $index); }

    public function __set($index, $value)
    {
        $this->offsetSet($index, $value);
        return $this;
    }

    public function __isset($index)
    {
        return $this->offsetExists($index);
    }

    public function __unset($index)
    {
        return $this->offsetUnset($index);
    }

    public function __toString()
    {
        return serialize($this);
    }


    public function __construct(...$object_or_array)
    {
        foreach ($object_or_array as $arguments) {
            if (!empty($arguments)) {
                foreach ($arguments as $property => $argument) {
                    $this->{$property} = $argument;
                }
            }
        }
    }

    public function addObject(...$object_or_array)
    {
        foreach ($object_or_array as $arguments) {
            if (!empty($arguments)) {
                foreach ($arguments as $property => $argument) {
                    $this->{$property} = $argument;
                }
            }
        }
    }

    public function addMethod($methodName, callable $callback)
    {
        $this->{$methodName} = $callback;
    }


    public function __call($method, $arguments)
    {
        if (isset($this->{$method}) && is_callable($this->{$method})) {
            return call_user_func_array($this->{$method}, $arguments);
        } else {
            die("Fatal error: Call to undefined method stdObject::{$method}()");
        }
    }
}
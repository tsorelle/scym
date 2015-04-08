<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/24/2015
 * Time: 12:24 PM
 */

namespace Tops\sys;


class TCollection {
    private $collection;
    public function __construct($collection = null)
    {
        if ($collection === null) {
            $this->collection = array();
        } else {
            $this->collection = $collection;
        }
    }

    /**
     * @param $callback
     * @param null $arguments
     * @return TCollection
     */
    public function filter($callback, $arguments = null)
    {
        $result = array();

        foreach($this->collection as $item) {
            if ($arguments == null) {
                $found = call_user_func($callback, $item);
            } else {
                $found = call_user_func($callback, $item, $arguments);
            }


            if ($found) {
                array_push($result,$item);
            }
        }
        return new TCollection($result);
    }

    public function process($callback, $arguments = null)
    {
        array_walk($this->collection,$callback,$arguments);
    }


    public function first($callback, $arguments) {
        foreach($this->collection as $item) {
            if ($arguments == null) {
                $found = call_user_func($callback, $item);
            } else {
                $found = call_user_func($callback, $item, $arguments);
            }
            if ($found) {
                return $item;
            }
        }
        return null;
    }

    public function add($item) {
        array_push($this->collection, $item);
    }

    public function setItem($key, $value) {
        $this->collection[$key] = $value;
    }

    public function asArray()
    {
        return $this->collection;
    }

    public function toArray()
    {
        $result = array_map(function($item) {return clone $item;}, $this->collection);
        return $result;
    }

    public function clear() {
        $this->collection =  array();
    }

    public function getCount() {
        return sizeof($this->collection);
    }

    public function copy() {
        return new TCollection($this->toArray());
    }

    public function get($key) {
        if (array_key_exists($key,$this->collection)) {
            return $this->collection[$key];
        }
        return null;
    }

    public function set($key, $value) {
        $this->collection[$key] = $value;
    }

    public function findKey($callback, $arguments = null) {
        foreach(array_keys($this->collection) as $key) {
            $item = $this->collection[$key];
            if ($arguments == null) {
                $found = call_user_func($callback, $item);
            } else {
                $found = call_user_func($callback, $item, $arguments);
            }
            if ($found) {
                return $key;
            }
        }
        return null;
    }

    public function removeItem($callback, $arguments = null) {
        $keys = array_keys($this->collection);
        foreach($keys as $key) {
            $item = $this->collection[$key];
            if ($arguments == null) {
                $found = call_user_func($callback, $item);
            } else {
                $found = call_user_func($callback, $item, $arguments);
            }
            if ($found) {
                unset($this->collection[$key]);
            }
        }
    }

    public function getSort( $compareFunction) {
        $result = $this->toArray();
        usort($result,$compareFunction);
        return $result;
    }

    public function removeByKey($key) {
        unset($this->collection[$key]);
    }

}
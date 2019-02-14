<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/21/17
 * Time: 7:47 PM
 */

namespace PapaLocal\Entity;

/**
 * Class Entity
 *
 * @package PapaLocal\Entity
 *
 * Default implementation for PapaLocal\Entity family of classes.
 */
abstract class Entity implements EntityInterface
{
    /**
     * Converts this object into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = array();

        // find getter methods
        $methods = array_filter(get_class_methods($this),
            function ($method)
            {
                return (strpos($method, 'get') === 0);
            }
        );

        // iterate over, and remove uninitialized member vars
        foreach($methods as $method) {

            // omit uninitialized values
            if (! is_null($this->$method()) && ! empty($this->$method())) {

                $key = lcfirst(substr($method, 3));

                // member var $key is initialized, add it to the array
                $propValue = $this->$method();

                // exclude objects from the array
                if (! is_object($propValue)) {
                    $array[$key] = $propValue;
                }
            }
        }

        return $array;
    }

    /**
     * Whether or not this object can be displayed on the feed page.
     *
     * @return bool
     */
    public function isFeedItem(): bool
    {
        return ( $this instanceof FeedItemInterface);
    }

    /**
     * Iterates over all member vars and calls a getter on the $comparator
     * to fetch value for comparison. Returns false if any of the object's members
     * does not match the corresponding value in $comparator.
     *
     * @param $comparator
     * @return bool
     */
    public function equals(Entity $comparator): bool
    {
        if (! ($comparator instanceof self)) {
            return false;
        }

        // compare the objects
        $match = true;

        // iterate over, and remove uninitialized member vars
        foreach(get_object_vars($this) as $key => $val) {
            // call getter on comparator
            $method = 'get' . ucfirst($key);

            if ($val !== call_user_func(array($method))) {
                $match = false;
            }
        }

        return $match;
    }
}
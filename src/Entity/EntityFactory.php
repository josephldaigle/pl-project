<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/5/17
 * Time: 12:03 PM
 */

namespace PapaLocal\Entity;

use PapaLocal\Entity\Exception\SetterNotFoundException;

/**
 * EntityFactory.
 *
 * A factory for creating instances of Entity.
 */
class EntityFactory
{
    /**
     * Initialize an instance of $class. Cannot be used with classes that have
     * constructor arguments.
     *
     * @param string $class
     * @return Entity
     * @throws \InvalidArgumentException
     */
    public function create(string $class): Entity
    {
        //check if class exists
        if (! class_exists($class)) {

            throw new \InvalidArgumentException(sprintf('Unable to load class: %s in %s',
                $class, __METHOD__));
        }

        //create the class
        $instance = new $class();

        //check if class is entity
        if (! ($instance instanceof Entity)) {
            throw new \InvalidArgumentException(sprintf('%s expects Param 1 to be an instance of %s. %s given.',
                __METHOD__, Entity::class, get_class($instance)));
        }

        //return instance
        return $instance;
    }

    /**
     * Creates an instance of $class with the values in $data
     * loaded in to member vars.
     *
     * $class should have a method named set{MemberName} for each index
     * in $data. Indexes and setters must adhere to camelcase as shown in sample.
     *
     * For the array $data = array('firstName' => 'Thomas');
     * class $class must contain a method named setFirstName(), which will receive
     * 'Thomas' as it's argument.
     *
     * @param string $class
     * @param array  $data
     *
     * @return Entity
     * @throws SetterNotFoundException if $data contains an index that does not have corresponding
     *      setter method in $class
     */
    public function createFromArray(string $class, array $data): Entity
    {
        //if data is empty return empty $class
        if (empty($data)) {
            return $this->create($class);
        }

        //create instance of class
        $instance = $this->create($class);

        //iterate $data and load values into $class
        foreach ($data as $key => $val) {

            //call setter on $class
            $function = 'set' . ucfirst($key);

            if (method_exists($instance, $function)) {
                call_user_func(array($instance, $function), $data[$key]);
            } else {
                throw new SetterNotFoundException(sprintf('Index %s does not have a matching setter in class %s',
                    $key, get_class($instance)));
            }
        }

        return $instance;
    }




    //TODO: Leave this function until certain not needed.
    /**
     * Convert a json string into an instance of $class with data included.
     *
     * Key names must be valid $class members, and are converted to setters
     * using:
     *      set . ucfirst($key)
     *
     * @param string $class
     * @param string $json
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
//    public static function fromJson(string $class, string $json)
//    {
//
//        if (! class_exists($class)) {
//            throw new \InvalidArgumentException(sprintf('[%s]: Unable to locate class: %s in %s',
//                __CLASS__, $class, __METHOD__));
//        }
//
//
//        //convert to object
//        $object = json_decode($json, TRUE);
//
//        //create instance
//        $instance = self::fromArray($class, $object);
//
//        return $instance;
//    }
}
<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/5/17
 * Time: 12:03 PM
 */

namespace PapaLocal\Entity\Factory;


class EntityFactory implements EntityFactoryInterface
{
    /**
     * Initialize an instance of $class. Cannot be used with classes that have
     * constructor arguments.
     *
     * @param string $class
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function create(string $class)
    {
        //validate class
        if (empty($class)) {
            throw new \InvalidArgumentException(sprintf('Param 1 provided to %s cannot be empty.',
                __METHOD__));
        }

        if (! class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Unable to load class: %s in %s',
                $class, __METHOD__));
        }

        //return instance
        return new $class();
    }

    /**
     * Creates an instance of $class with $data as loaded values.
     *
     * @see $class should have a method named set{MemberName} for each index
     * in $data. Indexes and setters must adhere to camelcase as shown in sample.
     *
     *  For $data = array('firstName' => 'Thomas');
     *  class $class must contain a method named setFirstName(string $firstName)
     *
     *
     * @param string $class
     * @param array  $data
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function createFromArray(string $class, array $data)
    {
        //create instance of class
        $instance = $this->create($class);

        //if data is empty, throw exception
        if (empty($data)) {
            throw new \InvalidArgumentException(sprintf('Param 2 provided to %s cannot be empty.',
                __METHOD__));
        }

        //load data into class
        foreach ($data as $key => $val) {

            $function = 'set' . ucfirst($key);

            if (method_exists($instance, $function)) {
                call_user_func(array($instance, $function), $data[$key]);
            } else {
                throw new \InvalidArgumentException(sprintf('Index %s does not have a matching setter in class %s',
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
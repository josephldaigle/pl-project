<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/17/17
 * Time: 3:07 PM
 */

namespace PapaLocal\Entity;

/**
 * Interface AddressInterface.
 *
 * Describe an Address;
 */
interface AddressInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * Fetch the route portion of the address (number and street).
     *
     * @return mixed
     */
    public function getStreetAddress();

    /**
     * @return mixed
     */
    public function getCity();

    /**
     * @param bool $short
     *
     * @return mixed
     */
    public function getState(bool $short = false);

    /**
     * @return mixed
     */
    public function getPostalCode();

    /**
     * @param bool $short
     *
     * @return mixed
     */
    public function getCountry(bool $short = false);

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @return mixed
     */
    public function toString();
}
<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/17/17
 * Time: 3:05 PM
 */

namespace PapaLocal\Entity;


/**
 * Interface PersonInterface.
 *
 * @package PapaLocal\Entity
 *
 * Describe an interface.
 */
interface PersonInterface
{
    /**
     * @return mixed string first name or null if not set
     */
    public function getFirstName();

    /**
     * @return mixed string last name or null if not set
     */
    public function getLastName();

    /**
     * @return mixed string description or null if not set
     */
//    public function getAbout(); // TODO: This is now optional because of referral contact.
}
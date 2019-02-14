<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/31/18
 * Time: 11:27 AM
 */

namespace PapaLocal\Entity;

/**
 * Interface UserInterface.
 *
 * Describe a User entity.
 */
interface UserInterface extends PersonInterface
{
    /**
     * @return mixed
     */
    public function getUserId();

    /**
     * @return mixed string username or null if not set
     */
    public function getUsername();

    /**
     * @return mixed string password or null if not set
     */
    public function getPassword();

    /**
     * @return mixed string timeZone (Y-m-d H:i:s) or null if not set
     */
    public function getTimeZone();

    /**
     * @return bool whether the user is active
     */
    public function getIsActive(): bool;
}
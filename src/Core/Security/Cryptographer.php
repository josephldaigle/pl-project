<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/6/18
 * Time: 1:14 PM
 */


namespace PapaLocal\Core\Security;


use Ramsey\Uuid\Uuid;


/**
 * Class Cryptographer
 *
 * Utility class for generating and verifying salts and hashes.
 *
 * @package PapaLocal\Core\Security
 */
class Cryptographer
{
    /**
     * Creates a hash that can be publicized, and decoded later using $salt.
     *
     * @param string $key
     *
     * @return string
     * @throws \LogicException
     */
    public function createHash(string $key): string
    {
        $hash = password_hash($key, PASSWORD_BCRYPT);
        if ($hash == false) {
            throw new \LogicException(sprintf('Failed to create password_hash using %s', $key));
        }
        return $hash;
    }

    /**
     * Create a salt for use as key when producing email hashes.
     *
     * @param int $length default is 16
     *
     * @return int
     */
    public function createSalt(int $length = null)
    {
        // set default length
        if (null === $length || $length < 1) { $length = 16; }

        $rand = '';
        for($i = 0; $i < $length; $i++) {
            $rand .= random_int(1, 9);
        }

        // return random salt
        return $rand;
    }

    /**
     * @param string $hash the result of createHash()
     * @param string $key the salt used to create $key
     *
     * @return bool     the result of the verification
     */
    public function verify(string $hash, string $key): bool
    {
        return password_verify($key, $hash);
    }
}
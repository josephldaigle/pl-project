<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 7/5/18
 * Time: 10:47 PM
 */

namespace PapaLocal\Core\Security\Entity;


use PapaLocal\Entity\EmailAddress;
use PapaLocal\Entity\EntityInterface;


/**
 * Interface EmailSaltInterface.
 *
 * @package PapaLocal\Core\Security
 *
 * Describe a secure email link used to identify inbound urls.
 */
interface EmailSaltInterface extends EntityInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return int
     */
    public function getPersonId(): int;

	/**
	 * @return int
	 */
	public function getEmailId(): int;

	/**
	 * @return string
	 */
	public function getSalt(): string;

	/**
	 * @return string
	 */
	public function getTimeCreated(): string;
}
<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 7/12/18
 */


namespace PapaLocal\Data\Repository\Strategy;


use PapaLocal\ValueObject\Form\RegisterUser;


/**
 * Interface CreateUserStrategyInterface.
 *
 * @package PapaLocal\Data\Repository\Strategy
 *
 * Describe a strategy for creating user accounts.
 */
interface CreateUserStrategyInterface
{
    /**
     * Persist a new user account.
     *
     * @return mixed
     */
    public function create(RegisterUser $form);
}
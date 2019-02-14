<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/1/18
 */


namespace PapaLocal\Core\Service;


use PapaLocal\Core\Messenger\Query\FindByGuidInterface;
use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Interface MessageFactoryInterface.
 *
 * Describe a factory for creating messages.
 *
 * @package PapaLocal\Core\Service
 */
interface MessageFactoryInterface
{
    /**
     * @param GuidInterface $guid
     */
//    public function newFindByGuid(GuidInterface $guid);
}
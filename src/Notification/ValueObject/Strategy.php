<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/12/18
 * Time: 2:33 PM
 */

namespace PapaLocal\Notification\ValueObject\Strategy;


use PapaLocal\Core\Enum\AbstractEnum;


/**
 * Class Strategy
 *
 * @package PapaLocal\Notification\ValueObject\Strategy
 */
class Strategy extends AbstractEnum
{
    private const APP = '15b8a6913dc0cf5b8a6913dc0d1994367930';
    private const EMAIL = '15b8a6917abe4c5b8a6917abe4e452775858';
    private const SMS = '15b8a69188be7e5b8a69188be80328736519';
}
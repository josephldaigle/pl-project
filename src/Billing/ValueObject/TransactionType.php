<?php
/**
 * Created by Joseph Daigle.
 * Date: 1/1/19
 * Time: 10:10 PM
 */


namespace PapaLocal\Billing\ValueObject;


use PapaLocal\Core\Enum\AbstractEnum;


/**
 * TransactionType.
 *
 * @package PapaLocal\Billing\ValueObject
 */
class TransactionType extends AbstractEnum
{
    private const DEBIT = 'debit';
    private const CREDIT = 'credit';
}
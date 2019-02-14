<?php
/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 9/24/18
 * Time: 12:15 PM
 */


namespace PapaLocal\Referral\Workflow;


/**
 * ReferralGuardBlockCode.
 *
 * These codes represent reasons why a referral cannot be created.
 *
 * @package PapaLocal\Referral\Workflow
 */
class ReferralGuardBlockCode
{
    const INACTIVE_AGREEMENT = '22c806f1-1cf0-4fbf-8218-ac22f6bff32c';
    const AGREEMENT_QUOTA = '979245f1-e25c-4d01-8618-ce0d9c15d239';
    const CONTACT_IS_USER = '0afb6dd1-3dce-4b96-977d-f48ceea1aa18';
    const USER_NOT_FOUND = '3cb5d49f-ff7c-44b1-8ffc-22ad26bf7058';
}
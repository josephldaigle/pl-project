<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/13/18
 * Time: 8:18 PM
 */

namespace PapaLocal\AuthorizeDotNet;
use PapaLocal\Entity\EmailAddress;
use net\authorize\api\contract\v1\CreateCustomerProfileResponse;
use net\authorize\api\contract\v1\CustomerProfileType;

/**
 * Interface CustomerProfileInterface.
 *
 * Describe operations on a customer profile.
 */
interface CustomerProfileInterface
{
    /**
     * Creates a customer profile for the user in Authorize.Net.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $emailAddress
     * @return int|false the newly created customer id, or false
     *
     * @see https://developer.authorize.net/api/reference/index.html#customer-profiles-create-customer-profile
     */
    public function createCustomerProfile(string $firstName, string $lastName, string $emailAddress);

    /**
     * Fetches a user's entire profile, including payment profiles.
     *
     * @param   string  username
     * @return  mixed
     *
     * @see https://developer.authorize.net/api/reference/index.html#customer-profiles-get-customer-profile
     */
    public function fetchCustomerProfile(string $username);

    /**
     * Update a user's existing Authorize.Net customer profile.
     * Email Address is the only field on the Customer Profile that can be updated. To update other customer
     * details, use updatePaymentProfile
     *
     * @param   int     $id         the customers assigned profile id
     * @param   string  $username   customers username registered email address
     * @return  mixed
     *
     * @see https://developer.authorize.net/api/reference/index.html#customer-profiles-update-customer-profile
     */
    public function updateCustomerProfile(int $id, string $username);

    /**
     * Delete a user's customer profile, including payment profiles.
     *
     * @param   int     $profileId the user's authorize.net profile id
     * @return  mixed
     *
     * @see https://developer.authorize.net/api/reference/index.html#customer-profiles-delete-customer-profile
     */
    public function deleteCustomerProfile(int $profileId);

    /**
     * Fetch all customer profile id's.
     *
     * @return mixed
     *
     * @see https://developer.authorize.net/api/reference/index.html#customer-profiles-get-customer-profile-ids
     */
    public function fetchCustomerProfileIds();
}
<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/24/17
 * Time: 1:45 AM
 */

namespace PapaLocal\Data;

/**
 * AttrType.
 *
 * Holds constant values from L_ tables in database.
 */
class AttrType
{
    /*
     |-----------------------------------
     | Address Types
     | Table: L_AddressType
     |-----------------------------------
     */
    const ADDRESS_MAILING   = 'Mailing';
    const ADDRESS_BILLING = 'Billing';
    const ADDRESS_SHIPPING   = 'Shipping';
    const ADDRESS_PHYSICAL = 'Physical';


    /*
     |-----------------------------------
     | Email Address Types
     | Table: L_EmailAddressType
     |-----------------------------------
     */
    const EMAIL_BUSINESS   = 'Business';
    const EMAIL_PERSONAL   = 'Personal';
    const EMAIL_SUPPORT    = 'Support';
    const EMAIL_SALES      = 'Sales';
    const EMAIL_OTHER      = 'Other';
    const EMAIL_USERNAME   = 'Username';
    const EMAIL_PRIMARY    = 'Primary';


    /*
     |-----------------------------------
     | Address Types
     | Table: L_PhoneNumberType
     |-----------------------------------
     */
    const PHONE_BUSINESS   = 'Business';
    const PHONE_PERSONAL = 'Personal';
    const PHONE_MAIN   = 'Main';
    const PHONE_CELL = 'Cell';
    const PHONE_FAX = 'Fax';
    const PHONE_OTHER = 'Other';


    /*
     |-----------------------------------
     | User Roles
     | Table: L_UserRole
     |-----------------------------------
     */
    const SECURITY_ROLE_REFERRAL_PARTNER = 'ROLE_REFERRAL_PARTNER';
    const SECURITY_ROLE_COMPANY = 'ROLE_COMPANY';
    const SECURITY_ROLE_USER = 'ROLE_USER';
    const SECURITY_ROLE_ADMIN = 'ROLE_ADMIN';
    const SECURITY_ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';


    public static function getEmailTypes()
    {
        return get_defined_constants(true);
    }
}
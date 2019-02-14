<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/6/18
 * Time: 9:52 AM
 */

namespace PapaLocal\Data;

/**
 * Ewebify.
 *
 * Holds application constants that should be used anywhere these values appear in the application.
 *
 * This is not environment-specific information, but rather business-domain info, that can be considered
 * owned by the business, and not the app. DO NOT USE where a change in environment will cause a logic error.
 *
 * To make constants available in Twig, make them public.
 */
class Ewebify
{
    public const APP_NAME = 'PapaLocal';
    public const BUSINESS_NAME = 'eWebify';
    public const BUSINESS_SHORT_NAME = 'eWebify';

    public const DOMAIN_NAME = 'papalocal.com';
    public const WEB_ADDRESS = 'https://www.papalocal.com';
    public const SERVICE_TERMS_ADDRESS = 'https://www.papalocal.com/terms-of-service';

    public const ADMIN_EMAIL = 'app@ewebify.com';
    public const ADMIN_PHONE = '(678) 723-3558';

    public const CUST_SUPP_EMAIL = 'info@ewebify.com';
    public const CUST_SUPP_LINK = '<a href="mailto:' . self::CUST_SUPP_EMAIL . '">' . self::CUST_SUPP_EMAIL . '</a>';
    public const CUST_SUPP_PHONE = '(678) 723-3558';
    public const CUST_SUPP_ADDRESS = '307 Dividend Dr, Peachtree City, Georgia 30269';

    public const SOCIAL_FACEBOOK = 'https://www.facebook.com/ewebify/';
    public const SOCIAL_TWITTER = 'https://twitter.com/ewebify';
    public const SOCIAL_LINKEDIN = 'https://www.linkedin.com/company/ewebify';
    public const SOCIAL_GOOGLEPLUS = 'https://plus.google.com/+eWebifyPeachtreeCity';
}
<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/17/18
 * Time: 8:02 AM
 */

namespace PapaLocal\ReferralAgreement\Data\Query\Invitee;


/**
 * Class FindAllBy
 *
 * @package PapaLocal\ReferralAgreement\Data\Query\Invitee
 */
class FindAllBy
{
    /**
     * @var array
     */
    private $filterVars;

    /**
     * FindAllBy constructor.
     *
     * @param array $filterVars
     */
    public function __construct(array $filterVars)
    {
        $this->filterVars = $filterVars;
    }

    /**
     * @return array
     */
    public function getFilterVars(): array
    {
        return $this->filterVars;
    }
}
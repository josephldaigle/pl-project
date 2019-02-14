<?php
/**
 * Created by Ewebify, LLC.
 * Date: 2/6/18
 * Time: 9:27 PM
 */

namespace PapaLocal\Data\Command\Company;

use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\Company\CompanyIndustry;

/**
 * LoadCompanyIndustries.
 *
 * @package PapaLocal\Data\Command\Company
 */
class LoadCompanyIndustries extends QueryCommand
{
    /**
     * @var int
     */
    private $companyId;

    /**
     * LoadCompanyIndustryList constructor.
     *
     * @param int $companyId
     */
    public function __construct(int $companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        $industryList = $this->serializer->denormalize(array(), Collection::class, 'array');

        // fetch industries
        $this->tableGateway->setTable('v_company_industry');
        $industryRows = $this->tableGateway->findBy('id', $this->companyId);

        if (count($industryRows) < 1) {
            return $industryList;
        }

        // parse rows to entities
        foreach($industryRows as $industryRow) {
            $industry = $this->serializer->denormalize(array(
                'name' => $industryRow['industry'],
                'type' => $industryRow['type']
            ), CompanyIndustry::class, 'array');
            $industryList->add($industry);
        }

        return $industryList;
    }

    /**
     * @inheritDoc
     */
    protected function filterException(\Exception $exception): \Exception
    {
        return $exception;
    }

}
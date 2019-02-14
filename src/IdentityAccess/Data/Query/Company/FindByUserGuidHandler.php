<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/13/18
 * Time: 9:00 PM
 */

namespace PapaLocal\IdentityAccess\Data\Query\Company;


use PapaLocal\Core\Data\Exception\QueryException;
use PapaLocal\Core\Data\Exception\QueryExceptionCode;
use PapaLocal\Core\Data\Record;
use PapaLocal\Core\Data\RecordSet;
use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Core\Data\TableGateway;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class FindByUserGuidHandler
 *
 * @package PapaLocal\IdentityAccess\Data\Query\Company
 */
class FindByUserGuidHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * FindByUserGuidHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param FindByUserGuid $query
     *
     * @return Record
     * @throws QueryException
     */
    function __invoke(FindByUserGuid $query): Record
    {
        $this->tableGateway->setTable('v_company_owner');
        $records = $this->tableGateway->findBy('ownerGuid', $query->getUserGuid());

        if ($records->count() < 1) {
            throw new QueryException(sprintf('Could not find Company with owner [guid]: %s.', $query->getUserGuid()), QueryExceptionCode::NOT_FOUND());
        }

        if ($records->count() > 1) {
            trigger_error(sprintf('More than one company exists for userGuid %s. Potential data corruption.', $query->getUserGuid()), E_USER_WARNING);
        }

        return $records->current();
    }

}
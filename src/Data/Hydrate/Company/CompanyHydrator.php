<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/7/18
 * Time: 8:28 PM
 */

namespace PapaLocal\Data\Hydrate\Company;


use PapaLocal\Core\Data\AbstractHydrator;
use PapaLocal\Core\Data\TableGateway;
use PapaLocal\Entity\Company;
use PapaLocal\Entity\Entity;
use PapaLocal\Entity\EntityFactory;
use PapaLocal\Entity\Exception\ServiceOperationFailedException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * CompanyHydrator.
 *
 * @package PapaLocal\Data\Hydrate
 */
class CompanyHydrator extends AbstractHydrator
{

    /**
     * @var CompanyContactProfileHydrator
     */
    private $profileHydrator;

    /**
     * CompanyHydrator constructor.
     *
     * @param TableGateway                  $tableGateway
     * @param EntityFactory                 $entityFactory
     * @param SerializerInterface           $serializer
     * @param CompanyContactProfileHydrator $companyContactProfileHydrator
     *
     * @inheritDoc
     */
    public function __construct(TableGateway $tableGateway,
                                EntityFactory $entityFactory,
                                SerializerInterface $serializer,
                                CompanyContactProfileHydrator $companyContactProfileHydrator)
    {
        parent::__construct($tableGateway, $entityFactory, $serializer);

        $this->profileHydrator = $companyContactProfileHydrator;
    }


    /**
     * @param Entity $entity an instance of Company
     * @inheritDoc
     */
    public function setEntity(Entity $entity)
    {
        if (! $entity instanceof Company) {
            throw new \InvalidArgumentException(sprintf('Param 1 provided to %s must be an instance of %s',
                __METHOD__, Company::class));
        }

        $this->entity = $entity;
    }

    /**
     * Hydrate a Company entity.
     *
     * @param bool $cascade if set to true, the company will have a contact profile hydrated as well.
     *
     * @return Entity
     * @throws ServiceOperationFailedException
     * @throws \LogicException
     */
    public function hydrate(bool $cascade = false): Entity
    {
        if (! is_numeric($this->entity->getId())) {
            throw new \LogicException(sprintf('Entity supplied must have an id assigned: %s.',
                json_encode($this->serializer->normalize($this->entity, 'array'))));
        }

        // hydrate the company
        $this->tableGateway->setTable('v_company');
        $companyRows = $this->tableGateway->findById($this->entity->getId());

        if (count($companyRows) !== 1) {
            throw new ServiceOperationFailedException(sprintf('Unable to find a matching company: %s.',
                json_encode($this->serializer->normalize($this->entity, 'array'))));
        }

        // set entity to found company
        $this->entity = $this->serializer->denormalize($companyRows[0], Company::class, 'array');

        // hydrate contact profile if $cascade set to true
        if (true === $cascade) {
            $this->profileHydrator->setEntity($this->entity);
            $this->entity = $this->profileHydrator->hydrate();
        }

        return $this->entity;
    }

    /**
     * Hydrate the address list portion of the contact profile.
     */
    public function hydrateAddressList()
    {
        $this->setEntity($this->entity);
        return $this->profileHydrator->hydrateAddressList();
    }

    /**
     * Hydrate the email address list portion of the contact profile.
     */
    public function hydrateEmailAddressList()
    {
        $this->setEntity($this->entity);
        return $this->profileHydrator->hydratePhoneNumberList();
    }

    /**
     * Hydrate the phone number list portion of the contact profile.
     */
    public function hydratePhoneNumberList()
    {
        $this->setEntity($this->entity);
        return $this->profileHydrator->hydratePhoneNumberList();

    }
}
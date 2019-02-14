<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/22/17
 * Time: 9:49 PM
 */

namespace PapaLocal\Data\DataMapper;

use PapaLocal\Entity\Entity;
use PapaLocal\Entity\Exception\UnhandledRequestException;
use PapaLocal\Entity\User;

/**
 * UserMapper.
 */
class UserMapper extends DataMapper
{
    /**
     * @inheritdoc
     */
    protected function toEntity(string $className, array $data)
    {
        // only handle when $className is User
        if ($className === User::class) {

            //remove personId from array
            unset($data['personId']);

            return $this->entityFactory->createFromArray(User::class, $data);
        }

        // request not handled by me
        throw new UnhandledRequestException(sprintf('%s cannot map %s to entity.', __CLASS__, $className));
    }

    /**
     * @inheritdoc
     */
    protected function toTable(Entity $entity)
    {
        // only handle when Entity is User
        if (! $entity instanceof User) {
            throw new UnhandledRequestException(sprintf('%s expects Param 1 to be an instance of %s.', __METHOD__,
                User::class));
        }

        $row = $entity->toArray();

        // if user has person, set personId in result array
        if ($person = $entity->getPerson()) {;
            $row['personId'] = $person->getGuid();
        }

        unset($row['username']);

        return $row;
    }
}
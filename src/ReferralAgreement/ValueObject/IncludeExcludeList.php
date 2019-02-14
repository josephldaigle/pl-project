<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/25/18
 */

namespace PapaLocal\ReferralAgreement\ValueObject;


use PapaLocal\Entity\Collection\Collection;


/**
 * Class ListInterface.
 *
 * @package PapaLocal\ReferralAgreement\ValueObject
 */
class IncludeExcludeList extends Collection
{
    /**
     * @return Collection
     */
    public function getIncludes(): Collection
    {
        $includes = clone $this;

        foreach ($includes as $key => $item) {
            if ($item->getType()->getValue() !== LocationType::INCLUDE()->getValue()) {
                $includes->remove($key);
            }
        }

        return $includes;
    }

    /**
     * @return Collection
     */
    public function getExcludes(): Collection
    {
        $excludes = clone $this;

        foreach ($excludes as $key => $item) {
            if ($item->getType()->getValue() !== LocationType::EXCLUDE()->getValue()) {
                $excludes->remove($key);
            }
        }

        return $excludes;
    }

    /**
     * @param IncludeExcludeList $newList
     */
    public function replaceIncludes(IncludeExcludeList $newList)
    {
        foreach($this->getIncludes() as $key => $includedLocation) {
            $this->remove($key);
        }

        $this->addAll($newList->getIncludes());
    }

    /**
     * @param IncludeExcludeList $newList
     */
    public function replaceExcludes(IncludeExcludeList $newList)
    {
        foreach($this->getExcludes() as $key => $excludedLocation) {
            $this->remove($key);
        }

        $this->addAll($newList->getExcludes());
    }
}
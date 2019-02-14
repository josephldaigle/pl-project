<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 8/10/18
 */

namespace PapaLocal\Core\Entity\Builder;

/**
 * Class BuilderRegistry.
 *
 * @package PapaLocal\Core\Entity\Builder
 */
class BuilderRegistry
{
    private $builders;


    public function __construct(array $builders = null)
    {
        $this->addAll($builders);
    }


    protected function addAll(array $builders)
    {
        foreach($builders as $builder) {
            if (! $builder instanceof EntityBuilderInterface) {
                throw new \InvalidArgumentException(sprintf('Unable to add an item to the registry that is not an instance of %s', EntityBuilderInterface::class));
            }
        }
    }
}
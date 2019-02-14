<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 8/10/18
 */

namespace PapaLocal\Core\Entity\Builder;


/**
 * Interface EntityBuilderInterface.
 *
 * Describe an entity builder.
 *
 * @package PapaLocal\Core\Entity\Builder
 */
interface EntityBuilderInterface
{
    /**
     * @return mixed
     */
    public function build();
}
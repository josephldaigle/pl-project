<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/8/18
 * Time: 2:41 PM
 */

namespace PapaLocal\Test;


use RDV\SymfonyContainerMocks\DependencyInjection\TestKernelTrait;
use PapaLocal\Kernel;


/**
 * Class DomainIntegrationTestKernel
 *
 * This class allows mocking container services.
 *
 * @package PapaLocal\Test
 */
class TestKernel extends Kernel
{
    use TestKernelTrait;
}
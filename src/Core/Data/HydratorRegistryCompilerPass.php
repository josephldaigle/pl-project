<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 8/19/18
 */


namespace PapaLocal\Core\Data;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;


/**
 * Class HydratorRegistryCompilerPass.
 *
 * @package PapaLocal\Core\Data
 */
class HydratorRegistryCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // do nothing if service not found
        if (!$container->has('PapaLocal\Core\Data\HydratorRegistry')) {
            return;
        }

        // fetch the service definition from the container
        $definition = $container->findDefinition('PapaLocal\Core\Data\HydratorRegistry');

        // find all service IDs with the papalocal_data.hydrator tag
        $taggedServices = $container->findTaggedServiceIds('papalocal_data.hydrator');

        // add the services to the registry
        foreach ($taggedServices as $id => $tags) {
            $repoDef = $container->findDefinition($id);
            $definition->addMethodCall('add', array(new Reference($id), $repoDef->getClass()));
        }
    }

}
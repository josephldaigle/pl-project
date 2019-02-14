<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 7/25/18
 * Time: 9:33 PM
 */


namespace PapaLocal\Core\Data;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;


/**
 * Class RepositoryRegistryCompilerPass
 *
 * @package PapaLocal\Core\Data
 */
class RepositoryRegistryCompilerPass implements CompilerPassInterface
{
	/**
	 * @inheritdoc
	 */
	public function process(ContainerBuilder $container)
	{
        // do nothing if service not found
        if (!$container->has('PapaLocal\Core\Data\RepositoryRegistry')) {
			return;
		}

        // fetch the service definition from the container
        $definition = $container->findDefinition('PapaLocal\Core\Data\RepositoryRegistry');

		// find all service IDs with the papalocal_data.repository tag
		$taggedServices = $container->findTaggedServiceIds('papalocal_data.repository');

        // add the service to the registry
        foreach ($taggedServices as $id => $tags) {
            $repoDef = $container->findDefinition($id);
			$definition->addMethodCall('add', array(new Reference($id), $repoDef->getClass()));
		}
	}

}
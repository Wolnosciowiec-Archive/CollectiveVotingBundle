<?php
namespace CollectiveVotingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class CollectiveVotingExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $path = realpath(__DIR__ . '/../Resources/config');

        if (is_dir($path)) {
            $loader = new YamlFileLoader($container, new FileLocator($path));
            $loader->load('services/services_factory.yml');
            $loader->load('services/services_manager.yml');
            $loader->load('services/services_decision_maker.yml');
            $loader->load('services/services_transformer.yml');
        }
    }
}
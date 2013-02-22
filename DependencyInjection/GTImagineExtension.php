<?php

namespace GT\ImagineBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;


/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class GTImagineExtension extends Extension
{

    /**
     * {@inheritDoc}
     *
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (!empty($config["driver"])
                && !empty($config["mkdir_mode"])
                && !empty($config["web_root"])
                && !empty($config["data_root"])
                && !empty($config["formats"])
                && !empty($config["filter_sets"])) {

            $container->setParameter('gt_imagine.driver', $config["driver"]);
            $container->setParameter('gt_imagine.mkdir_mode', $config["mkdir_mode"]);
            $container->setParameter('gt_imagine.formats', $config["formats"]);
            $container->setParameter('gt_imagine.web_root', $config["web_root"]);
            $container->setParameter('gt_imagine.data_root', $config["data_root"]);
            $container->setParameter('gt_imagine.filter_sets', $config["filter_sets"]);
        } else {
            throw new \Exception("Error configuration in gt_imagine");
        }


        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

}
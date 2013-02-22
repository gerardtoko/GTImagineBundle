<?php

namespace GT\ImagineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('gt_imagine');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue('gd')
                    ->validate()
                        ->ifTrue(function($v) { return !in_array($v, array('gd', 'imagick', 'gmagick')); })
                        ->thenInvalid('Invalid imagine driver specified: %s')
                    ->end()
                ->end()
                ->scalarNode('mkdir_mode')->defaultValue(0777)->end()
                ->arrayNode('formats')
                    ->defaultValue(array("png", "jpg"))
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('web_root')->defaultValue('%kernel.root_dir%/../web')->end()
                ->scalarNode('data_root')->defaultValue('%kernel.root_dir%/../web/medias/thumbnails')->end()
                ->arrayNode('filter_sets')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('directory')->defaultValue('%kernel.root_dir%/../web/medias/images')->end()
                            ->scalarNode('quality')->defaultValue(75)->end()
                            ->scalarNode('data_loader')->defaultValue('filesystem')->end()
                            ->arrayNode('filters')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->useAttributeAsKey('name')
                                    ->prototype('variable')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}

<?php

namespace Nadia\Bundle\PaginatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nadia_paginator');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('default_options')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('input_key_class')
                            ->defaultValue('Nadia\Bundle\PaginatorBundle\Input\InputKeys')
                        ->end()
                        ->scalarNode('default_page_size')->defaultValue(10)->end()
                        ->scalarNode('default_page_range')->defaultValue(8)->end()
                        ->scalarNode('session_enabled')->defaultFalse()->end()
                        ->scalarNode('default_translation_domain')->defaultNull()->end()
                        ->scalarNode('default_paginator_translation_domain')
                            ->defaultValue('nadia_paginator')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('templates')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('pages')
                            ->defaultValue('@NadiaPaginator/templates/bootstrap4/pages.html.twig')
                        ->end()
                        ->scalarNode('searches')
                            ->defaultValue('@NadiaPaginator/templates/bootstrap4/searches.html.twig')
                        ->end()
                        ->scalarNode('filters')
                            ->defaultValue('@NadiaPaginator/templates/bootstrap4/filters.html.twig')
                        ->end()
                        ->scalarNode('sorts')
                            ->defaultValue('@NadiaPaginator/templates/bootstrap4/sorts.html.twig')
                        ->end()
                        ->scalarNode('sort_link')
                            ->defaultValue('@NadiaPaginator/templates/bootstrap4/sort_link.html.twig')
                        ->end()
                        ->scalarNode('page_sizes')
                            ->defaultValue('@NadiaPaginator/templates/bootstrap4/page_sizes.html.twig')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

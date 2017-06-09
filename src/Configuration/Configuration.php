<?php

namespace Ihsan\Client\Platform\Configuration;

use Pimple\Container;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var array
     */
    private $configs = [];

    /**
     * @param Container $container
     */
    public function process(Container $container)
    {
        $configs = ['app' => []];
        /** @var CacheItemPoolInterface $cache */
        $cache = $container['internal.cache_handler'];
        $item = str_replace('\\', '_', __CLASS__);
        if ($cache->hasItem($item) && 'prod' === $container['environment']) {
            $this->merge($container, $cache->getItem($item)->get());

            return;
        }

        foreach ($this->configs as $config) {
            $yaml = Yaml::parse(file_get_contents($config));
            if (!empty($yaml['app'])) {
                $configs['app'] = array_merge($configs['app'], $yaml['app']);
            }
        }

        $processor = new Processor();
        $configs = $processor->processConfiguration($this, $configs);
        $cache->save($cache->getItem($item)->set($configs));

        $this->merge($container, $cache->getItem($item)->get());
    }

    /**
     * @param string $resource
     */
    public function addResource($resource)
    {
        $this->configs[] = $resource;
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('app');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('api')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->children()
                        ->scalarNode('base_url')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('api_key')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('param_key')->defaultValue('api_key')->end()
                    ->end()
                ->end()
                ->scalarNode('http_client')->defaultValue(null)->end()
                ->arrayNode('middlewares')
                    ->defaultValue([])
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->variableNode('parameters')
                                ->defaultValue([])
                            ->end()
                            ->integerNode('priority')->defaultValue(0)->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('event_listeners')
                    ->defaultValue([])
                    ->prototype('array')
                        ->children()
                            ->scalarNode('event')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('class')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('method')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->integerNode('priority')->defaultValue(0)->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('routes')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('path')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('controller')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->arrayNode('methods')
                                ->prototype('scalar')
                                    ->beforeNormalization()
                                        ->ifString()
                                        ->then(function ($v) { return strtoupper($v); })
                                    ->end()
                                    ->validate()
                                        ->ifNotInArray(['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'HEAD'])
                                        ->thenInvalid('Invalid HTTP Verb')
                                    ->end()
                                    ->defaultValue(['GET'])
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('template')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->children()
                        ->scalarNode('engine')->defaultValue(null)->end()
                        ->scalarNode('path')->defaultValue(null)->end()
                        ->scalarNode('cache_dir')->defaultValue(null)->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * @param Container $container
     * @param array     $configs
     */
    private function merge(Container $container, array $configs)
    {
        foreach ($configs as $key => $value) {
            $container[$key] = $value;
        }
    }
}

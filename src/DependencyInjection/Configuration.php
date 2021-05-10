<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /** @noinspection NullPointerExceptionInspection */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('labsbh_anonymizer');

        //@formatter:off
        $treeBuilder
            ->getRootNode()
            ->children()
                ->arrayNode('hints')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('formatter')->end()
                            ->scalarNode('words')
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(static fn ($value) => [$value])
                                ->end()
                                ->isRequired()
                            ->end()
                            ->arrayNode('arguments')->end()
                            ->booleanNode('date')->end()
                            ->booleanNode('unique')->end()
                        ->end()
                    ->end()
                ->defaultValue(
                //@formatter:on
                    [
                        [
                            'formatter' => 'firstName',
                            'words'     => ['firstName'],
                        ],
                        [
                            'formatter' => 'lastName',
                            'words'     => ['lastName'],
                        ],
                        [
                            'formatter' => 'city',
                            'words'     => ['city', 'town'],
                        ],
                        [
                            'formatter' => 'streetAddress',
                            'words'     => ['streetAddress', 'address', 'billingAddress', 'deliveryAddress'],
                        ],
                        [
                            'formatter' => 'postcode',
                            'words'     => ['postcode', 'postalCode', 'zip', 'zipCode'],
                        ],
                        [
                            'formatter' => 'country',
                            'words'     => ['country'],
                        ],
                        [
                            'formatter' => 'phoneNumber',
                            'words'     => ['phoneNumber', 'phone', 'mobile'],
                        ],
                        [
                            'formatter' => 'realText',
                            'words'     => ['comment'],
                            'arguments' => [200, 2],
                        ],
                        [
                            'formatter' => 'dateTimeBetween',
                            'words'     => ['birthdate', 'birthday'],
                            'arguments' => ['-30 years', 'now', null],
                            'date'      => true,
                        ],
                        [
                            'formatter' => 'safeEmail',
                            'words'     => ['mail', 'email'],
                            'unique'    => true,
                        ],
                        [
                            'formatter' => 'userName',
                            'words'     => ['userName'],
                            'unique'    => true,
                        ],
                        [
                            'formatter' => 'password',
                            'words'     => ['password'],
                        ],
                        [
                            'formatter' => 'creditCardNumber',
                            'words'     => ['creditCardNumber', 'creditCard'],
                        ],
                        [
                            'formatter' => 'siren',
                            'words'     => ['siren'],
                            'unique'    => true,
                        ],
                        [
                            'formatter' => 'siret',
                            'words'     => ['siret'],
                            'unique'    => true,
                        ],
                        [
                            'formatter' => 'vat',
                            'words'     => ['vat'],
                        ],
                        [
                            'formatter' => 'nir',
                            'words'     => ['nir'],
                            'unique'    => true,
                        ],
                    ]
                //@formatter:off
                )
                ->end()
                ->arrayNode('defaults')
                    ->scalarPrototype()->example(['locale' => 'fr_FR', 'seed' => 'seed_key'])->end()
                ->end()
            ->end();
        //@formatter:on

        $connectionsRootNode = (new TreeBuilder('connections'))->getRootNode();
        /** @var ArrayNodeDefinition $node */
        $node = $connectionsRootNode
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array');

        //@formatter:off
        $node
            ->arrayPrototype()
                ->children()
                    ->booleanNode('truncate')->defaultFalse()->end()
                    ->arrayNode('primary_key')
                        ->scalarPrototype()->end()
                    ->end()
                    ->arrayNode('fields')
                        ->arrayPrototype()
                            ->variablePrototype()->end()
                        ->end()
                    ->end() // fields
                ->end()
            ->end()
            ->beforeNormalization()
                ->ifTrue(static function ($v) {
                    return \is_array($v) && \array_key_exists('defaults', $v) && \is_array($v['defaults']);
                })
                ->then(static function ($c) {
                    if (isset($c['tables'])) {
                        foreach ($c['tables'] as &$tableConfig) {
                            if ($tableConfig['fields']) {
                                foreach ($tableConfig['fields'] as &$fieldConfig) {
                                    if (isset($c['defaults'])) {
                                        foreach ($c['defaults'] as $defaultKey => $defaultValue) {
                                            if (!\array_key_exists($defaultKey, $fieldConfig)) {
                                                $fieldConfig[$defaultKey] = $defaultValue;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    return $c;
                })
            ->end()
        ;


        $treeBuilder
            ->getRootNode()
            ->beforeNormalization()
                ->ifTrue(
                    static function ($v) {
                        return is_array($v) && !array_key_exists('connections', $v);
                    })
                ->then(
                    static function ($v) {
                        $connection = [];
                        foreach ($v as $key => $value) {
                            $connection[$key] = $value;
                            unset($v[$key]);
                        }

                        $v['connections'] = ['default' => $connection];

                        return $v;
                    })
            ->end()
            ->append($connectionsRootNode);
        //@formatter:on
        return $treeBuilder;
    }
}

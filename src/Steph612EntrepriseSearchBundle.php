<?php

namespace Steph612\EntrepriseSearchBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Steph612\EntrepriseSearchBundle\Client\EntrepriseSearchClient;
use Steph612\EntrepriseSearchBundle\Client\EntrepriseSearchClientInterface;
use Steph612\EntrepriseSearchBundle\Command\SearchEntrepriseCommand;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
* Bundle pour l'API Recherche d'entreprises.
*/
class Steph612EntrepriseSearchBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $definition->rootNode();

        $rootNode
            ->children()
                ->integerNode('timeout')
                    ->defaultValue(10)
                    ->min(1)
                    ->max(15)
                    ->info('Timeout des requêtes HTTP en secondes')
                ->end()
            ->end()
        ;
    }
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder
    ): void {
        // Paramètres
        $container->parameters()->set('steph612_entreprise_search.timeout', $config['timeout']);

        // Services
        $container->services()
            // Client principal
            ->set(EntrepriseSearchClient::class)
            ->args([service('http_client'), service('logger')->ignoreOnInvalid(), '%steph612_entreprise_search.timeout%'])
            ->public()
        ;

        // Alias pour l'interface
        $container->services()
            ->alias(EntrepriseSearchClientInterface::class, EntrepriseSearchClient::class)
            ->public()
        ;

        // Alias nommé
        $container->services()
            ->alias('steph612_entreprise_search.client', EntrepriseSearchClientInterface::class)
            ->public()
        ;

        // Commande
        $container->services()
            ->set(SearchEntrepriseCommand::class)
            ->args([service(EntrepriseSearchClientInterface::class)])
            ->tag('console.command')
        ;
    }
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}

<?php

namespace Steph612\EntrepriseSearchBundle\Command;

use Steph612\EntrepriseSearchBundle\Client\EntrepriseSearchClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'steph612:search-entreprise',
    description: 'Recherche une entreprise via l\'API',
)]
class SearchEntrepriseCommand extends Command
{
    public function __construct(
        private readonly EntrepriseSearchClientInterface $entrepriseSearch
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('query', InputArgument::REQUIRED, 'Termes de recherche')
            ->addOption('siren', 's', InputOption::VALUE_NONE, 'Rechercher par SIREN')
            ->addOption('page', 'p', InputOption::VALUE_OPTIONAL, 'Numéro de la page', 1)
            ->addOption('per-page', 'r', InputOption::VALUE_OPTIONAL, 'Nombre de résultats', 10)
            ->setHelp(
                <<<'HELP'
                <fg=bright-cyan>Exemples :</>
                <info>symfony console steph612:search-entreprise 503932568 -s</info>                       Rechercher une SIREN
                <info>php bin/console steph612:search-entreprise "carrefour" --page=2 --per-page=5</info>  Rechercher des entreprises <comment>[default: -p 1, -r 10]</comment>
                HELP
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $query = $input->getArgument('query');
        $isSiren = $input->getOption('siren');
        $page = (int) $input->getOption('page');
        $perPage = (int) $input->getOption('per-page');

        try {
            if ($isSiren) {
                $entreprise = $this->entrepriseSearch->findBySiren($query);
                if (!$entreprise) {
                    $io->warning('Aucune entreprise trouvée');
                    return Command::SUCCESS;
                }

                $io->success('Entreprise trouvée !');
                $io->definitionList(
                    ['SIREN' => $entreprise->siren],
                    ['Nom' => $entreprise->nomComplet],
                    ['Adresse' => $entreprise->siege?->adresse ?? 'N/A'],
                    ['Code NAF' => $entreprise->activitePrincipale ?? 'N/A'],
                    ['Actif' => $entreprise->isActif() ? '✅ Oui' : '❌ Non']
                );
            } else {
                $result = $this->entrepriseSearch->search($query, $page, $perPage);

                if (!$result->hasResults()) {
                    $io->warning('Aucun résultat trouvé');
                    return Command::SUCCESS;
                }

                $io->success(sprintf('%d résultat(s) trouvé(s)', $result->totalResults));

                $rows = [];
                foreach ($result->results as $entreprise) {
                    $rows[] = [
                        $entreprise->siren,
                        substr($entreprise->nomComplet, 0, 40),
                        $entreprise->siege?->codePostal ?? 'N/A',
                        $entreprise->isActif() ? '✅ ' : '❌ ',
                    ];
                }

                $io->table(['SIREN', 'Nom', 'CP', 'Actif'], $rows);
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Erreur : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

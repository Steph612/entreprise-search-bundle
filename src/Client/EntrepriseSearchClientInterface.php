<?php

namespace Steph612\EntrepriseSearchBundle\Client;

use Steph612\EntrepriseSearchBundle\Model\Entreprise;
use Steph612\EntrepriseSearchBundle\Model\SearchResult;

/**
* Interface pour le client de recherche d'entreprises.
*/
interface EntrepriseSearchClientInterface
{
    public function search(
        string $query,
        int $page = 1,
        int $perPage = 10,
        array $filters = []
    ): SearchResult;

    public function findBySiren(string $siren): ?Entreprise;

    public function searchByCodePostal(
        string $codePostal,
        int $page = 1,
        int $perPage = 10
    ): SearchResult;
}

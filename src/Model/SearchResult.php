<?php

namespace Steph612\EntrepriseSearchBundle\Model;

/**
 * Résultat paginé d'une recherche d'entreprises.
 */
class SearchResult
{
    /**
     * @param Entreprise[] $results
     */
    public function __construct(
        public readonly array $results,
        public readonly int $totalResults,
        public readonly int $page,
        public readonly int $perPage,
        public readonly int $totalPages,
    ) {
    }

    public static function fromArray(array $data): self
    {
        // Transforme les données brutes de l'API en objets Entreprise
        // array_map() applique la fonction Entreprise::fromArray() à chaque élément du
        // $data['results'] ?? [] est un array vide si $data['results'] est null
        $results = array_map(
            fn (array $item) => Entreprise::fromArray($item),
            $data['results'] ?? []
        );

        return new self(
            results: $results,
            totalResults: $data['total_results'] ?? 0,
            page: $data['page'] ?? 1,
            perPage: $data['per_page'] ?? 10,
            totalPages: $data['total_pages'] ?? 0,
        );
    }

    public function hasResults(): bool
    {
        return count($this->results) > 0;
    }

    public function getFirstResult(): ?Entreprise
    {
        return $this->results[0] ?? null;
    }
}

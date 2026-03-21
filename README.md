# Recherche Entreprises Bundle
Bundle Symfony pour l'[API Recherche d'entreprises](https://recherche-entreprises.api.gouv.fr)

## 📦 Installation
```bash
composer require stay612/recherche-entreprises-bundle
```

## ⚙️ Configuration (optionnelle)
```yaml
# config/packages/steph612_entreprise_search.yaml
steph612_entreprise_search:
  timeout: 10
# Timeout en secondes (défaut: 10)
```

## 🚀 Utilisation
### Dans un Contrôleur
```php
use Steph612\EntrepriseSearchBundle\Client\EntrepriseSearchClientInterface;

public function __construct(
    private EntrepriseSearchClientInterface $entrepriseClient
) {
}

public function search(): Response
{
    $result = $this->entrepriseClient->search('carrefour');
    foreach ($result->results as $entreprise) {
        echo $entreprise->nomComplet . ' - ' . $entreprise->siren;
    }
}
```

### Recherche par SIREN
```php
$entreprise = $this->entrepriseClient->findBySiren('652014051');if ($entreprise) {
    echo $entreprise->nomComplet;
}
```

### Commande Console
```bash
# Recherche simple
php bin/console steph612:search-entreprise carrefour

# Par SIREN (détails complets)
php bin/console steph612:search-entreprise 652014051 --siren
```

### 📊 Modèles
- `SearchResult` : Résultat paginé
- `Entreprise` : Données d'entreprise
- `Siege` : Établissement siège

### 🧪 Tests
```bash
vendor/bin/phpunit --testdox
```

### 📝 Licence
MIT

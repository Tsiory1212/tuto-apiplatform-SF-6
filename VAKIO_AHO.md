# Creation 
1- symfony new (name_project)
2- symfony composer require api
3- php bin/console doctrine:database:create
4- php bin/console make:entity
5- #[ApiResource()] sur l'Entity
6- url : https://127.0.0.1:8000/api

# Règle de validation (Assert de symfony)
    Soit on peut tout de suite utiliser cette règle sur toutes les sérialisations (normalisation, dénormalisation)
    soit, On peut la définir avec des groups et on l'applique seulement que sur les sérialisation concernée (ex : PUT mais pas POST) 

# Pagination
#[ApiResource(paginationClientItemsPerPage: true)] => pour que le contrôle de nb d'item peut être fait en client-side 
#[GetCollection(paginationMaximumItemsPerPage: 5)] => pour limiter le client à acceder au nombre de ressources

Il est préférable d'accepter "Accept header" avec *application/ld+json* pour pouvoir récupérer plus d'info comme le *Hydra*
 
## Filtre
#[ApiFilter(SearchFilter::class, properties: ["title" => "partial"])] //exact or partial => exact by default

# Documentation
openapiContext: []
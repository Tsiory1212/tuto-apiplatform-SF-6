# Creation 
1- symfony new (name_project)
2- symfony composer require api
3- php bin/console doctrine:database:create
4- php bin/console make:entity
5- #[ApiResource()] sur l'Entity

# Règle de validation (Assert de symfony)
    Soit on peut tout de suite utiliser cette règle sur toutes les sérialisations (normalisation, dénormalisation)
    soit, On peut la définir avec des groups et on l'applique seulement que sur les sérialisation concernée (ex : PUT mais pas POST) 

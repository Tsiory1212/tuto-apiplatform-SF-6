# Creation 
1- symfony new (name_project)
2- symfony composer require api
3- php bin/console doctrine:database:create
4- php bin/console make:entity
5- #[ApiResource()] sur l'Entity
6- url : https://127.0.0.1:8000/api

# Controller & endpoint
**Attention**
    on n'oublie pas l'attribut #[AsController] pour un endpoint qui utilise un Controller personnalisé

## Endpoint
**Creation**
    - Soit à partir d'un Entity (...et on travail par des Controller ou des State(DataProvider/Persiter) )
    - Soit on le customize par "OpenApiFactory"

**Cacher**
    #[ApiResource(
        operations: [ 
            new Post( openapi: false )
        ]
    )]

**Customisation**
    On peut utiliser "OpenApiFactory"
    url => https://api-platform.com/docs/core/openapi/#overriding-the-openapi-specification

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

# Response/Request
si le dump php est décodé ou n'est pas lisible, on peut essayer de regarder dans le réponse XHR du navigateur

# Custom Provider/Persister
**Attention**
N'oublier pas de préciser le 'processor' et le 'provider' dans l'attribut #[ApiResource] car celà créera un probleme d'opération

===> #[ApiResource(
        provider: ExampleProvider::class,
        processor: ExampleProcessor::class
    )]


## Provider
api_platform 2 : ContextAwareCollectionDataProviderInterface
api_platform 3 : ProviderInterface
            
            ------------

url : https://api-platform.com/docs/core/state-providers/
cmd : bin/console make:state-provider


**Filter**
On ne peut pas utiliser *SearchFilter* pour les 'Class/Entity' qui n'utilisent pas doctrine 

## Persister
api_platform 2 : ContextAwareDataPersisterInterface
api_platform 3 : ProcessorInterface
            
            ------------

url : https://api-platform.com/docs/core/state-processors/
cmd : bin/console make:state-processor


# PUT vs PATCH
PATCH sert à une modification partielles

# AUTHENTIFICATION
Il y a 2 types :
  * Stateful => côté Server (Cookie json)
  * Stateless => utilisation de jeton (ex : JWT) 


# Privilège et Rôle (Symfony)
    Pour restreindre une action par le Rôle de l'User, on ajoute
    
    #[ApiResource(
        operations: [ 
            new GetCollection( security: 'is_granted("ROLE_USER")' )
        ]
    )]

    ou #[ApiResource( security: 'is_granted("ROLE_USER")' ) )]

*Swagger-ui* 
<!-- Pour ajouter l'icon Cadenas sur l'endpoint -->
    #[ApiResource(
        operations: [
            new Get(
                openapiContext: [
                    'security' => [['cookieAuth' =>  []]]
                ]
            )
        ]
    )]

<!-- Syntax -->
api_platform 2 : 'openapi_context' => [ 'security' => ['cookieAuth' => []]] <!-- 'security' => [] -->
api_platform 2 : openapiContext: => ['security' => [['cookieAuth' =>  []]]] <!-- 'security' => [[]] -->

# Swagger
## Bouton Authorize (api-platform)
    Pour enrichir le contenu, 
    voir => config/packages/api_platform.yaml 
    ou => https://api-platform.com/docs/core/jwt/#documenting-the-authentication-mechanism-with-swaggeropen-api


# JWT
**lexik/jwt-authentication-bundle**
Ce bundle symfony gère l'auth JWT 

**payload JWT**
On crée une Subscriber qui implémente "EventSubscriberInterface" pour modifier le paylod dans JWT (Les infos dans le Token)

**Intérrogation BDD**
 - Pour éviter d'intérroger à chq fois le BDD lorsqu'on utilise les endpoints "api", on se sert du provider de "lexik_jwt"
 - url : https://github.com/lexik/LexikJWTAuthenticationBundle/blob/2.x/Resources/doc/8-jwt-user-provider.rst
# Creation 
1- symfony new (name_project)
2- symfony composer require api
3- php bin/console doctrine:database:create
4- php bin/console make:entity
5- #[ApiResource()] sur l'Entity
6- url : https://127.0.0.1:8000/api

# Normalization/Denormalization
 * Normalization : GET / PUT / PATCH / DELETE et POST (agissant comme PUT)
 * Denormalization :  POST

 Serialize = "Object" ----(Normalization)---->    "Array"    ------(encode)----->  "JSON"

# Operation
*Collection*
GET, POST

*Item*
GET, PUT, PATCH, DELETE

# Controller & endpoint
**Attention**
  - on n'oublie pas l'attribut #[AsController] pour un endpoint qui utilise un Controller personnalisé
  - Il est très conseillé de mettre "application/ld+json" comme header "Accept", lorsqu'on faite une action GetCollection 
        <!-- Ex : Probème dans Voter lorsque application/json à été choisi comme "Accept"... il ne traite que le premier élément -->
        <!-- C'est pour la # Pagination aussi... infos sur *Hydra*-->

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
  * Stateful => côté Server (Cookie json) <!-- Dépend de la BDD/Session  *** Server side -->
  * Stateless => utilisation de jeton (ex : JWT) <!-- Plus légé *** Client side -->
## JWT
*Processus Authentification*
  * Pour le processus, il passe d'abord dans le firewall "login" pour servir le provider "app_user_provider", 
  * mais après, lorqu'on utilise l'endpoint "^/api", c'est le provider "jwt" qui travail, 
  * c-à-d qu'on joue sur les JWT généré après l'authentification

## RefreshToken
  * ce "refresh_token" n'est pas un token JWT, c'est un token particulier sauvegarder en BDD
  * Si on supprime l'accès au site à un User, on peut supprimer (revoke) son token (Le token ne peut plus être rafraichit), 
    mais attention, si l'User se fait voler son token, l'usurpateur peut l'utiliser jusqu'à ce que le token expire

## Clé d'API
  * Voir App\Security\ApiKeyAuthenticator et "apikey_user_provider" dans security.yaml

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
api_platform 2 : openapiContext: => ['security' => [['cookieAuth' =>  []]]] <!-- 'security' => [[]] -->
        
**OpenApi**
<!-- Syntax -->
  * ce synthax ne sert simplement à décorer l'interface de l'Endpoint Mais aussi de l'appliquer la sécurité JWT 

    openapiContext: [
        'security' => [['bearerAuth' =>  []]]
    ],

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


# Permission et Security (Object's Owner)
## $secutity->getUser()
  - le "User" retourné de createFromPayload() dans User.php sera le $security->getUser() dans toute l'application 
  - N'oublie pas de charger createFromPayload() avec setRoles() car c'est important pour faire des conditions isGranted() 

## Extension DOCTRINE   
  - Sert à intercepter et à personnaliser la requête Doctrine sur les actions "collection" ou "item"
  - Interface : "QueryCollectionExtensionInterface", "QueryItemExtensionInterface"
  - url : https://api-platform.com/docs/core/extensions/#custom-doctrine-orm-extension

# VOTER
https://api-platform.com/docs/core/security/#hooking-custom-permission-checks-using-voters

# Normalization et Denormalization
*Serializer*
L'interface "SerializerContextBuilderInterface" intercepte tous les normalization et denormalization en cours

*Normalization*
Pour intercepter toutes les normalisations en cours, on utilise l'interface "NormalizerInterface" avec "NormalizerAwareInterface"

*Denormalization*
<!-- Utile pour injecter l'utilisateur dans une requête (action POST) -->
Pour intercepter toutes les dénormalisations en cours, on utilise l'interface "DenormalizerInterface" avec "DenormalizerAwareInterface"


# IMAGE
 - Activer "multipart/form-data" dans api_platform.yaml
    api_platform:
        formats: 
            multipart: ['multipart/form-data']
 
 *Pourquoi on utilise un Normalizer (PostNormalizer.php) pour modifier les data or on utilise 'POST' comme action*
 *On peut utiliser un Controller pour intercepter un Article avec l'action 'POST' et le modifier plutôt que d'utiliser l'action 'PUT'*
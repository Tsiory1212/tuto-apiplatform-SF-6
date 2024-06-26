<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post as MetadataPost;
use ApiPlatform\Metadata\Put;
use App\Attribute\ApiAuthGroups;
use App\Controller\CountPostController;
use App\Controller\PostImageController;
use App\Controller\PublishPostController;
use App\Entity\Interface_\UserOwnedInterface;
use App\Repository\PostRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Valid;

use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    normalizationContext:[
        'groups' => ['Post:read', 'Category:read', 'read:User'],
        'openapi_definition_name' => 'Collection-normalization-Test'
    ],
    denormalizationContext:['groups' => ['Post:write', 'Category:write']],
    paginationClientItemsPerPage: true,
    operations: [
        new GetCollection(
            name: 'count',
            uriTemplate: '/posts/count',
            controller: CountPostController::class,
            read: false,
            paginationEnabled: false,
            filters: [
                'properties' => 'content'
            ],
            openapiContext: [
                'summary' =>  'Récupère le nombre total d\'article',
                'parameters' => [
                    [
                        'in' => 'query',
                        'name' => 'online',
                        'schema' => [
                            'type' => 'integer'
                        ],
                        'description' => 'Filtre les articles en ligne'
                    ],
                    [
                        'in' => 'query',
                        'name' => 'title',
                        'apiproperties' => false,
                        'required' => false

                    ]
                ],
                'responses' => [
                    '200' =>  [
                        'description' => 'OK',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'integer',
                                    'example' => 3
                                ]
                            ]
                        ]
                    ]
                ]

            ]
        ),
        new MetadataPost(
            name: 'publication',
            // routeName: 'post_post_publication', // Ne marche pas, je sais pas pourquoi !
            uriTemplate: '/posts/{id}/publication',
            controller: PublishPostController::class,
            openapiContext: [
                'summary' =>  'Permet de publier un article'
            ]
        ),
        new MetadataPost(
            name: 'image',
            uriTemplate: '/posts/{id}/image',
            deserialize: false,
            controller: PostImageController::class,
            openapiContext: [
                'summary' =>  'Permet de poster une image dans un Article déjà existé',
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        )
    ]
)]
#[Put(validationContext: ['groups' => [Post::class, 'validationGroups']])] // ici, on utilise l'approche "Dynamic Validation Groups" (Static function)
#[Get(
    normalizationContext: [
        'openapi_definition_name' => 'Detail' 
    ],
    openapiContext: [
        'security' => [['bearerAuth' =>  []]]
    ],
)]
#[GetCollection(
    paginationItemsPerPage: 3, 
    paginationMaximumItemsPerPage: 3,
    filters: [],
    openapiContext: [
        'summary' => 'Récupère tous les articles',
        'security' => [['bearerAuth' =>  []]],
        'parameters' => [
            [
                'in' => 'query',
                'name' => 'title',
            ]
        ]
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ["title" => "partial"])] //exact or partial => exact by default
#[Delete()]
#[Patch()]
#[MetadataPost(validationContext: ['groups' => ['Post:createdAt:POST:maxToDay', 'Cagegory:name:POST:validationMax']])] // Ici, on a mis une règle de validation pour la Methode POST comme => la date de création "createdAt" doit être supérieure à la date d'aujourd'hui 
#[ApiAuthGroups([ # Voici un ex de comment créer un attribut personnalisé 
    'CAN_EDIT' => ['read:collection:Owner'],
    'ROLE_USER' => ['read:collection:User']
])]
class Post implements UserOwnedInterface # Cet Iterface nous sert à mettre une condition dans l'Extension Doctrine pour filtrer les ressources
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Post:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[
        Groups(['Post:read', 'Post:write']),
        Length(min: 5, groups: ['Post:title:PUT:validationMax'])
    ]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:collection:Owner'])]
    private ?string $slug = null;
    
    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['Post:write', 'Post:read:Auth'])] # ce Post:read:Auth sera ajouter à la normalizationContext après verification dans "SerializerContextBuilderInterface"
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Range(min: new DateTime(), groups: ['Post:createdAt:POST:maxToDay'])]  // Dès qu'on a spécifié le "groups", cette règle de validation ne s'applique pas tant que la "validationContext" avec "groups" n'est pas définit dans les sérialisations
    #[Groups(['Post:read', 'Post:write'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'posts', cascade: ['persist'])] // cascade: ['persist'] => permet de dire à symfony qu'on peut créer une catégorie en même temps qu'on ajoute un article
    #[Groups(['Post:read', 'Post:write']), Valid()]
    private ?Category $category = null;

    #[ORM\Column(options: ["default" => 0])]
    #[Groups(['Post:read', 'Post:write'])]
    #[ApiProperty(
        openapiContext: [
            'example' => false,
            'description' => 'En ligne ou pas ?'
        ],
        security: "is_granted('POST_VIEW', object) "
    )]
    private ?bool $online = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[Groups(['Post:read'])]
    private ?User $user = null;

   
    #[Vich\UploadableField(mapping: "post_image", fileNameProperty: "filePath")]
    private ?File $file;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filePath = null;

    #[Groups(['Post:read'])]
    private ?string $fileUrl;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public static  function validationGroups(self $post)
    {
        return ['Post:title:PUT:validationMax'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function isOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): self
    {
        $this->online = $online;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;
        return $this;
    }

    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    public function setFileUrl(?string $fileUrl): self
    {
        $this->fileUrl = $fileUrl;

        return $this;
    }

}

<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Odm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post as MetadataPost;
use ApiPlatform\Metadata\Put;
use App\Repository\PostRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    normalizationContext:['groups' => ['Post:read', 'Category:read']],
    denormalizationContext:['groups' => ['Post:write', 'Category:write']],
    paginationClientItemsPerPage: true
)]
#[Put(validationContext: ['groups' => [Post::class, 'validationGroups']])] // ici, on utilise l'approche "Dynamic Validation Groups" (Static function)
#[Get()]
#[GetCollection(
    paginationItemsPerPage: 3, 
    paginationMaximumItemsPerPage: 3
)]
#[ApiFilter(SearchFilter::class, properties: ["title" => "partial"])] //exact or partial => exact by default
#[Delete()]
#[MetadataPost(validationContext: ['groups' => ['Post:createdAt:POST:maxToDay', 'Cagegory:name:POST:validationMax']])] // Ici, on a mis une règle de validation pour la Methode POST comme => la date de création "createdAt" doit être supérieure à la date d'aujourd'hui 
class Post
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
    #[Groups(['Post:read', 'Post:write'])]
    private ?string $slug = null;
    
    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['Post:write', 'Post:read'])]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Range(min: new DateTime(), groups: ['Post:createdAt:POST:maxToDay'])]  // Dès qu'on a spécifié le "groups", cette règle de validation ne s'applique pas tant que la "validationContext" avec "groups" n'est pas définit dans les sérialisations
    #[Groups(['Post:read', 'Post:write'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'posts', cascade: ['persist'])] // cascade: ['persist'] => permet dire à symfony qu'on peut créé une catégorie en même temps qu'on ajoute un article
    #[Groups(['Post:read', 'Post:write']), Valid()]
    private ?Category $category = null;

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

}

<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\State\DependencyProcessor;
use App\State\DependencyProvider;
use ApiPlatform\Metadata\Post as MetadataPost;
use ApiPlatform\Metadata\Put;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    paginationEnabled: false,
    processor: DependencyProcessor::class,
    provider: DependencyProvider::class
)]
#[Get(provider: DependencyProvider::class)]
#[GetCollection(provider: DependencyProvider::class)]
#[MetadataPost(processor: DependencyProcessor::class)]
#[Put(processor: DependencyProcessor::class)]
#[Patch(processor: DependencyProcessor::class)] 
#[Delete(processor: DependencyProcessor::class)] 

class Dependency 
{
    #[ApiProperty(identifier: true)] //Quand notre classe n'est pas relié à ORM, il faut préciser "identifier" pour que API_platform permet de faire les actions comme Get()
    private string $uuid;
    #[Assert\NotBlank(message: 'Le champ nom ne doit pas être vide')] 
    private string $name;
    #[Assert\NotBlank(message: 'Le champ version ne doit pas être vide')] 
    #[Groups(['Dependency:write'])]
    private string $version;

    public function __construct(string $name, string $version)
    {
        $this->uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString();
        $this->name = $name;
        $this->version = $version;
    }

    

    /**
     * Get the value of uuid
     */ 
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of version
     */ 
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the value of version
     *
     * @return  self
     */ 
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }
}

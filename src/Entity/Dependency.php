<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\State\DependencyProvider;

#[ApiResource(
    paginationEnabled: false
)]
#[Get(provider: DependencyProvider::class)]
#[GetCollection(provider: DependencyProvider::class)]
class Dependency 
{
    #[ApiProperty(identifier: true)] //Quand notre classe n'est pas relié à ORM, il faut préciser "identifier" pour que API_platform permet de faire les actions comme Get()
    private string $uuid;
    private string $name;
    private string $version;

    public function __construct(string $uuid, string $name, string $version)
    {
        $this->uuid = $uuid;
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
}

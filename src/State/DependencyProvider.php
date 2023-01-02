<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Dependency;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\KernelInterface;

class DependencyProvider implements ProviderInterface
{

    public function __construct(private KernelInterface $rootPath, private ProviderInterface $itemProvider)
    {

    }

    public function getDependencies(): array    
    {
        $path = $this->rootPath->getProjectDir().'/composer.json';
        $json = json_decode(file_get_contents($path), true);
        return $json['require'];
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $items = [];
        
        foreach ($this->getDependencies() as $name => $version) {
            $items[] = new Dependency(Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString(), $name, $version);
        }
        
        // On vérifie si l'opération va être  Get ou GetCollection
        if (isset($uriVariables['uuid'])) {
            /** @var Dependency $dependency */
            foreach ($items as $dependency) {
                $uuidUri = $uriVariables['uuid'];
                if ($uuidUri ===  $dependency->getUuid()) {
                    $item =  new Dependency($uuidUri, $dependency->getName(), $dependency->getVersion());
                    return $item ;
                }
            }

        }else{
            return $items;
        }

    }

}

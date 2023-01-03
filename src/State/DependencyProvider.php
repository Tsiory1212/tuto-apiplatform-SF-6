<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\DependencyRepository;

class DependencyProvider implements ProviderInterface
{
    protected $repoDependency;

    public function __construct(DependencyRepository $repoDependency, private ProviderInterface $itemProvider)
    {
        $this->repoDependency = $repoDependency;
    }


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {        
        $items = $this->repoDependency->findAll();
        
        if ($operation instanceof CollectionOperationInterface) {
            return $items;
        }
        return $this->repoDependency->find($uriVariables['uuid']);
    }
}

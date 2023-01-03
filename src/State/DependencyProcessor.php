<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\DependencyRepository;

class DependencyProcessor implements ProcessorInterface
{
    

    public function __construct(private DependencyRepository $repoDependency)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($operation instanceof DeleteOperationInterface) {
            return $this->repoDependency->remove($data);
        }

        return $this->repoDependency->persist($data);
    }
}

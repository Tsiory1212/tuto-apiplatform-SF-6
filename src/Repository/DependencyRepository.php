<?php
namespace App\Repository;

use App\Entity\Dependency;
use Symfony\Component\HttpKernel\KernelInterface;

class DependencyRepository {

    private $path;

    public function __construct(private KernelInterface $rootPath)
    {
        $this->path = $this->rootPath->getProjectDir().'/composer.json';
    }

    public function getDependencies(): array    
    {
        $json = json_decode(file_get_contents($this->path), true);
        return $json['require'];
    }

    /**
     * @return Dependency[]
     */
    public function findAll(): array
    {
        $items = [];
        
        foreach ($this->getDependencies() as $name => $version) {
            $items[] = new Dependency($name, $version);
        }
        
        return $items;
    }

    public function find(string $uuidUri): ?Dependency
    {

         /** @var Dependency $dependency */
         foreach ($this->findAll() as $dependency) {
            if ($uuidUri ===  $dependency->getUuid()) {
                $item =  new Dependency($dependency->getName(), $dependency->getVersion());
                return $item ;
            }
        }
    }

    public function persist(Dependency $dependency)
    {
        $json = json_decode(file_get_contents($this->path), true);
        $json['require'][$dependency->getName()] = $dependency->getVersion();

        /**
         * JSON_PRETTY_PRINT : Permet de formater le json (affichage)
         * JSON_UNESCAPED_SLASHES : Permet d'échaper l'anti-slash
         */
        file_put_contents($this->path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); 
    }

    public function remove(Dependency $dependency)
    {
        $json = json_decode(file_get_contents($this->path), true);
        unset($json['require'][$dependency->getName()]);
        file_put_contents($this->path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); 
    }
}

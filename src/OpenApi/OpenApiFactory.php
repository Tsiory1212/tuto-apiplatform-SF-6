<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\OpenApi;

class OpenApiFactory  implements OpenApiFactoryInterface
{

    public function __construct(private OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi {
        $openApi = $this->decorated->__invoke($context);

        $openApi->getPaths()->addPath('/custom', new PathItem(null, 'Custom', null, new Operation('Custom-api', [], [], 'Point d\'entrée personalisée ')));
        return $openApi;
    }

    
}

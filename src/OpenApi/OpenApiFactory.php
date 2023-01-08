<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
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
        
        $schemas = $openApi->getComponents()->getSecuritySchemes() ?? [];
        // $schemas['cookieAuths'] = new \ArrayObject([
        //     'type' => 'apikey',
        //     'in' => 'cookie',
        //     'name' => 'PHPSESSID'
        // ]);
        $schemas['bearerAuth'] = new \ArrayObject([
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT'
        ]);




  
        /**
         * Un exemple de customisation d'un endpoint déjà existé        (apiPlatform 2 (???) )
         * Ici, on enleve les parameters dans UI apiPlatform pour cet endpoint
         */
        $meOperation = $openApi->getPaths()->getPath('/api/me')->getGet()->withParameters([]);
        $mePathItem = $openApi->getPaths()->getPath('/api/me')->withGet($meOperation);
        $openApi->getPaths()->addPath('/api/me', $mePathItem);


        
        /** On fait référence a l'endpoint   /api/login     */
        $schemas = $openApi->getComponents()->getSchemas();
        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'john@doe.fr',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => '0000',
                ],
            ],
        ]);
        $schemas['Token'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        
        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postApiLogin',
                tags: ['Auth'],
                requestBody: new RequestBody(
                    // description: 'Generate new JWT Token',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials',
                            ],
                        ],
                    ]),
                ),
                responses: [
                    '200' => [
                        'description' => 'Token JWT',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token',
                                ],
                            ],
                        ],
                    ],
                ]
            ),
        );
        $openApi->getPaths()->addPath('/api/login', $pathItem);


        /** On fait référence a l'endpoint   /api/logout     */
        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postApiLogout',
                tags: ['Auth'],
                responses: [
                    '204' => [],
                ]
            ),
        );
        $openApi->getPaths()->addPath('/api/logout', $pathItem);


        /** Pour enlever l'icon cadena sur toutes les endpoint */
        /** Ce qui est à l'inverse de apiPlatform 2 (Je ne sais pas pourquoi)*/
        $openApi = $openApi->withSecurity([]); 

        return $openApi; 
    }

    
}

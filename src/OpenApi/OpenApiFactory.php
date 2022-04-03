<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\Model;
use ArrayObject;

class OpenApiFactory implements OpenApiFactoryInterface
{

    public function __construct(private OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $openApi = $openApi->withInfo((new Model\Info('API MY Explore Bag', 'v2', 'ABSYSTEMS'))->withExtensionProperty('info-key', 'Info value'));


        $schemas = $openApi->getComponents()->getSecuritySchemes();
        $schemas['bearerAuth'] = new ArrayObject([
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerForamt' => 'JWT'
        ]);

        $schema = $openApi->getComponents()->getSchemas();

        $schema['Credentials'] = new ArrayObject([

            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'myeb@myeb.com'
                ],
                'password' => [
                    'type' => 'string',
                    'example' => '1234securité'
                ]
            ]
        ]);


        $schema['Token'] = new ArrayObject([

            'properties' => [
                'token' => [
                    'type' => 'string',
                    'example' => 'trJ1eXAiOiJKV1QiLCJhbGciztJSUzI1NiJ8.eyJpYXQiOjE2MjczODkyNDcsImV4cCI6MTYyNzM5Mjg0Nywicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiYW50aG9ueWJzLnByb0BnbWFpbC5jb20ifQ.gJAon81jTq8xv7N5Jy-KLu_IwqwQMeHZxMbLzhi5fvArIijzeAh36eVfQsYBnZUR1-cZ-IdYqYr0BlhKAHXHbybY2ZySASpHCoqAct5aPVsFXoz_zCDAeRpHJznLYwNcrlOB1Sd-wyn8sru3jbcJtPSid0j0s201YH4kRJY5NNH-P23jNTeZDVne613DnqUfwBZxYb__LmfEpb0rYdL9fF97FYTd3egU8lbu_g5CERQq0z-aiooY7qgXiaxDSjF8IDgSL_UUOKdoLpqM3p5754U1d5wRwM2qtctxmpJkx2isTVYw-p2jOB6zVBAG9dueaZ-UHRSGpgX7KKX5-IvSXxUEZF6akpr6eUEBtcpUZmwJ2JEyuVKbcaZRC7D5RfUk1CPRbk_rLAtRcEODIuwAgRuD0RE7i8j33ms9ZDf6y0L4S1bzZkQ7RhClZIG1Uuk2p_9IcHiPxIcmOvZu7K8hi-vZnYNF6bw6BDV0Gc8nzR0xWQqKx3_BBuU9gUFQeA91PpThF8ud8PDZtaas8l0-eGy5xrGb70MznB4SRCfjZLfhegtcEQKH2A7CPe6tK_IySKGByuNOi8Dpb9ZxPTFOnZiIQnUMC5nVFTWz8NXn6gMnATKLMlD8N1Ic3Vk66Zfv1t4uJbbxeVXJFJEPR4lnuNcK--qTah8ACFKumxQezoQ'
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'example' => 'zqfy41b6b381bff78e8002a8a784b9d3d9e1e881a70c848cd6b934b4a6be500a8f31f9b5c5fd4af58789664c8a79a7fdf824a613599d4fb171fcc962201c82a3'
                ]
            ]
        ]);

        $schema['refresh'] = new ArrayObject([

            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                    'example' => 'zqfy41b6b381bff78e8002a8a784b9d3d9e1e881a70c848cd6b934b4a6be500a8f31f9b5c5fd4af58789664c8a79a7fdf824a613599d4fb171fcc962201c82a3'
                ]
            ]
        ]);

        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postApiLogin',
                tags: ['User'],
                requestBody: new RequestBody(
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials'
                            ]
                        ]
                    ])
                ),
                responses: [
                    '200' => [
                        'description' => 'User connect',
                        'content'  => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token'
                                ]
                            ]
                        ]
                    ]
                ]

            )
        );

        $resfreshPath = new PathItem(
            post: new Operation(
                operationId: 'postRefreshJwt',
                tags: ['User'],
                requestBody: new RequestBody(
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/refresh'
                            ]
                        ]
                    ])
                ),
                responses: [
                    '200' => [
                        'description' => 'Refresh JWT Tokens',
                        'content'  => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token'
                                ]
                            ]
                        ]
                    ]
                ]

            )
        );

        $openApi->getPaths()->addPath('/api/login', $pathItem);
        $openApi->getPaths()->addPath('/api/token/refresh', $resfreshPath);

        return $openApi;
    }
}

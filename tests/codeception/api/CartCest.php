<?php

class CartCest
{

    /**
     * Shop cart API testing
     * @param ApiTester $I
     */
    public function cartProcessingTest(ApiTester $I)
    {
        $I->comment('Check send HTTP method "GET" to recive empty shop cart.');
        $I->sendGET('/cart');
        $json = [
            'data' => [
                'total_sum'      => 0,
                'products_count' => 0,
                'products'       => [],
            ]
        ];
        $I->seeResponseContainsJson($json);

        $I->comment('Check send HTTP method "POST" to get errors adding product to shop cart.');
        $I->comment('Check error product and quantity blank');

        $params = [
            'product_id' => 0,
            'quantity'   => 0,
        ];
        $I->sendPOST('/cart', $params);

        $json = [
            'error' => [
                'params'  => [
                    [
                        'code'    => 'required',
                        'message' => 'Product cannot be blank.',
                        'name'    => 'product_id',
                    ],
                    [
                        'code'    => 'required',
                        'message' => 'Quantity cannot be blank.',
                        'name'    => 'quantity',
                    ],
                ],
                'type'    => 'invalid_param_error',
                'message' => 'Invalid data parameters',
            ],
        ];
        $I->seeResponseContainsJson($json);

        $I->comment('Check error quantity interval');
        $params = [
            'product_id' => 1,
            'quantity'   => 20,
        ];
        $I->sendPOST('/cart', $params);

        $json = [
            'error' => [
                'params'  => [
                    [
                        'code'    => 'required',
                        'message' => 'Incorrect quantity: \'20\'.',
                        'name'    => 'quantity',
                    ],
                ],
                'type'    => 'invalid_param_error',
                'message' => 'Invalid data parameters',
            ],
        ];
        $I->seeResponseContainsJson($json);

        $I->comment('Check correct adding product');
        $params = [
            'product_id' => 1,
            'quantity'   => 1,
        ];
        $I->sendPOST('/cart', $params);

        $I->seeResponseEquals('');

        $I->comment('Check send HTTP method "GET" to recive empty shop cart.');
        $I->sendGET('/cart');
        $json = [
            'data' => [
                'total_sum'      => 0,
                'products_count' => 0,
                'products'       => [],
            ]
        ];
        $I->dontSeeResponseContainsJson($json);

        $I->comment('Check send HTTP method "GET" to recive not empty shop cart.');
        $I->sendGET('/cart');
        $json = [
            'data' => [
                'total_sum'      => 50,
                'products_count' => 1,
                'products'       => [
                    [
                        'product_id' => 1,
                        'quantity'   => 1,
                        'sum'        => 50,]
                ],
            ]
        ];
        $I->seeResponseContainsJson($json);

        $I->comment('Check send HTTP method "DELETE" to get errors deleting product from shop cart.');
        $I->comment('Check error delete path');
        $I->sendDELETE('/cart');
        $json = [
            'error' => [
                'params'  => [
                    [
                        'code'    => 'required',
                        'message' => 'Product cannot be blank.',
                        'name'    => 'product_id',
                    ],
                ],
                'type'    => 'invalid_param_error',
                'message' => 'Invalid data parameters',
            ],
        ];
        
        $I->seeResponseContainsJson($json);

        $I->comment('Check correct delete product');
        $I->sendDELETE('/cart/1');
        $I->seeResponseEquals('');

        $I->comment('Check error delete product');
        $I->sendDELETE('/cart/1');
        $json = [
            'error' => [
                'params'  => [
                    [
                        'code'    => 'required',
                        'message' => 'Product \'1\' is not found.',
                        'name'    => 'product_id',
                    ],
                ],
                'type'    => 'invalid_param_error',
                'message' => 'Invalid data parameters',
            ],
        ];
        
        $I->seeResponseContainsJson($json);

        $I->comment('Check send HTTP method "GET" to recive empty shop cart.');
        $I->sendGET('/cart');
        $json = [
            'data' => [
                'total_sum'      => 0,
                'products_count' => 0,
                'products'       => [],
            ]
        ];
        $I->seeResponseContainsJson($json);
    }

}

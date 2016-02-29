<?php

class ProductsCest
{

    /**
     * Product API testing
     * @param ApiTester $I
     */
    public function productsProcessingTest(ApiTester $I)
    {
        $I->comment('Check send HTTP method "GET" to recive empty shop cart.');
        $I->sendGET('/products');
        $json = [
            'data' => [
                [
                    'id'          => 1,
                    'name'        => 'Product #1',
                    'description' => 'Product Description',
                    'price'       => 50,
                ],
                [
                    'id'          => 2,
                    'name'        => 'Product #2',
                    'description' => 'Product Description',
                    'price'       => 100,
                ],
                [
                    'id'          => 3,
                    'name'        => 'Product #3',
                    'description' => 'Product Description',
                    'price'       => 150,
                ],
            ],
        ];
        $I->seeResponseContainsJson($json);
    }

}

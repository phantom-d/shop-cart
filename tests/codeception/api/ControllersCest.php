<?php

class ControllersCest
{

    /**
     * Controllers testing
     * @param ApiTester $I
     */
    public function controllersProcessingTest(ApiTester $I)
    {
        $I->comment('Check send HTTP method "GET" to response 200 HTTP code.');
        $I->sendGET('/cart');
        $I->seeResponseCodeIs(200);
        
        $I->comment('Check send HTTP method "GET" to response 404 HTTP code.');
        $I->sendGET('/cart/1');
        $I->seeResponseCodeIs(404);
        
        $I->comment('Check send HTTP method "GET" to response 400 HTTP code.');
        $I->sendPOST('/cart');
        $I->seeResponseCodeIs(400);
        
        $I->comment('Check send HTTP method "GET" to response 400 HTTP code.');
        $I->sendPOST('/products');
        $I->seeResponseCodeIs(500);
    }

}

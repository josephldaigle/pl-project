<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/5/17
 * Time: 6:37 PM
 */


namespace Test\Functional;


use PapaLocal\Test\WebTestCase;


/**
 * FrameworkExceptionTest.
 *
 * Tests that the framework returns the correct error page when request
 * cannot be resolved to an available url.
 */
class FrameworkExceptionTest extends WebTestCase
{
    public function testNotFoundHandling()
    {
        $this->client->request('GET', '/never-gonna-be-a-url');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
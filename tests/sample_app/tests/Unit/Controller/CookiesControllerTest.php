<?php /* Native cakephp integration tests will run ok */
namespace App\Test\Unit\Controller;

use Cake\TestSuite\IntegrationTestCase;

class CookiesControllerTest extends IntegrationTestCase
{
    public function testIndex()
    {
        $this->get('/cookies');
        $this->assertResponseOk();
        $this->assertResponseContains('The cookies page.');
    }
}

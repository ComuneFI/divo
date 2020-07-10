<?php

namespace App\DCoreBundle\Tests\Controller;

use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Panther\PantherTestCase;

class HomepageTest extends PantherTestCase {
    /*
     * @test
     */

    private $client = null;

    public function setUp(): void {
        $this->client = static::createClient();
    }

    public function testHomepage() {
        $this->logIn();
        $crawler = $this->client->request('GET', '/');
        //$this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        
    }

    private function logIn() {
        $container = $this->client->getContainer();
        $firewall = 'main';
        $session = $container->get('session');
        /* @var $userManager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $container->get('fos_user.user_manager');
        /* @var $loginManager \FOS\UserBundle\Security\LoginManager */
        $user = $userManager->findUserByUsername('divoadmin');
        $token = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

}

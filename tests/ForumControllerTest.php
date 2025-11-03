<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ForumControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        // On crée un client HTTP simulé (comme un navigateur)
        $client = static::createClient();

        // On envoie une requête GET sur la page d'accueil "/"
        $crawler = $client->request('GET', '/');

        // ✅ Vérifie que la page répond bien (code HTTP 200 = OK)
        $this->assertResponseIsSuccessful();

        // ✅ Vérifie que le texte "Forum T'chaton ensemble ! 😺"
        // apparaît bien dans une balise <h1>
        $this->assertSelectorTextContains('h1', 'Forum T\'chaton ensemble ! 😺');
    }
}

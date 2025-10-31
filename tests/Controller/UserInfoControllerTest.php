<?php

namespace App\Tests\Controller;

// Importation de l'entité UserInfo (celle qu'on veut tester)
use App\Entity\UserInfo;

// Permet de gérer la base de données dans le test
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

// Client HTTP de Symfony pour simuler un navigateur
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

// Classe de base de Symfony pour les tests web
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserInfoControllerTest extends WebTestCase
{
    // Navigateur simulé
    private KernelBrowser $client;

    // Gestionnaire d'entités Doctrine
    private EntityManagerInterface $manager;

    // Repository pour interagir avec UserInfo en base
    private EntityRepository $userInfoRepository;

    // URL de base du CRUD UserInfo
    private string $path = '/user/info/';

    protected function setUp(): void
    {
        // Création d’un faux navigateur Symfony
        $this->client = static::createClient();

        // Récupération du gestionnaire Doctrine
        $this->manager = static::getContainer()->get('doctrine')->getManager();

        // Récupération du repository UserInfo
        $this->userInfoRepository = $this->manager->getRepository(UserInfo::class);

        // 🚨 Nettoyer la base avant chaque test
        foreach ($this->userInfoRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        // Appliquer la suppression
        $this->manager->flush();
    }

    public function testIndex(): void
    {
        // Suivre automatiquement les redirections HTTP
        $this->client->followRedirects();

        // Simuler un GET sur la page index
        $crawler = $this->client->request('GET', $this->path);

        // Vérifier que la réponse est 200 OK
        self::assertResponseStatusCodeSame(200);

        // Vérifier que le titre contient "UserInfo index"
        self::assertPageTitleContains('UserInfo index');

        // Ici on pourrait tester le contenu de la page (exemple commenté)
        // self::assertSame('Texte présent', $crawler->filter('.classCss')->first()->text());
    }

    public function testNew(): void
    {
        // Indique que le test n'est pas encore finalisé (tu peux l'activer plus tard)
        $this->markTestIncomplete();

        // Accéder à la page de création
        $this->client->request('GET', sprintf('%snew', $this->path));

        // Vérifier que la page renvoie un code 200
        self::assertResponseStatusCodeSame(200);

        // Soumettre le formulaire avec un nom "Testing"
        $this->client->submitForm('Save', [
            'user_info[name]' => 'Testing',
        ]);

        // Vérifier que l'utilisateur est redirigé vers la liste
        self::assertResponseRedirects($this->path);

        // Vérifier qu'on a bien 1 élément dans la base
        self::assertSame(1, $this->userInfoRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();

        // Crée un UserInfo en base
        $fixture = new UserInfo();
        $fixture->setName('My Title');

        // Persiste l'objet
        $this->manager->persist($fixture);
        $this->manager->flush();

        // Accède à la page "show" de cet objet
        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        // Vérifie le code 200
        self::assertResponseStatusCodeSame(200);

        // Vérifie que le titre contient "UserInfo"
        self::assertPageTitleContains('UserInfo');
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();

        // Création d'un objet en base
        $fixture = new UserInfo();
        $fixture->setName('Value');
        $this->manager->persist($fixture);
        $this->manager->flush();

        // Aller sur la page d'édition
        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        // Soumettre le formulaire avec une nouvelle valeur
        $this->client->submitForm('Update', [
            'user_info[name]' => 'Something New',
        ]);

        // Vérifier redirection
        self::assertResponseRedirects('/user/info/');

        // Récupérer les données en base et vérifier la modification
        $fixture = $this->userInfoRepository->findAll();
        self::assertSame('Something New', $fixture[0]->getName());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        // Crée un UserInfo en base
        $fixture = new UserInfo();
        $fixture->setName('Value');
        $this->manager->persist($fixture);
        $this->manager->flush();

        // Accède à la page show
        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        // Clique sur "Delete"
        $this->client->submitForm('Delete');

        // Vérifie redirection
        self::assertResponseRedirects('/user/info/');

        // Vérifie que la base est vide
        self::assertSame(0, $this->userInfoRepository->count([]));
    }
}

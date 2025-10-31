<?php

namespace App\Tests\Repository;

use App\Entity\Topic;
use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TopicRepositoryTest extends KernelTestCase
{
    private TopicRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel(); // Démarre le Kernel Symfony
        $this->repository = static::getContainer()->get(TopicRepository::class);
    }

    public function testCreateAndFindTopic(): void
    {
        //Récupère l'EntityManager pour interagir avec la base de données test
        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');

        // Création d’un nouveau topic
        $topic = new Topic();
        $topic->setTitle('Sujet de test');
        $topic->setDescription('Ceci est un sujet créé pour un test d’intégration');
        $topic->setCreationDate(new \DateTime());

        //Prépare Doctrine à enregistrer l’objet
        $entityManager->persist($topic);
        //Enregistre réellement dans la base de données
        $entityManager->flush();

        // Vérifie que le topic a bien été inséré
        $foundTopic = $this->repository->find($topic->getId());
        //Vérifie que le topic existe bien
        $this->assertNotNull($foundTopic);
        //Vérifie que le titre récupéré est correct
        $this->assertSame('Sujet de test', $foundTopic->getTitle());

        // Nettoyage : on supprime le topic pour ne pas polluer la BDD
        $entityManager->remove($foundTopic);
        $entityManager->flush();
    }
}

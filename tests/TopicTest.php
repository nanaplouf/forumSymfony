<?php

namespace App\Tests;

use App\Entity\Topic;
use PHPUnit\Framework\TestCase;

class TopicTest extends TestCase
{
    public function testTopicSetterAndGetters(): void
    {
        // On crée un nouvel objet Topic
        $topic = new Topic();

        // On définit un titre
        $topic->setTitle('Mon premier sujet');

        // On définit une description
        $topic->setDescription('Ceci est une description de test.');

        // ✅ On vérifie que getTitle retourne bien le titre qu'on a mis
        $this->assertSame('Mon premier sujet', $topic->getTitle());

        // ✅ On vérifie que getDescription retourne bien la description qu'on a mise
        $this->assertSame('Ceci est une description de test.', $topic->getDescription());
    }

    public function testCreationDateIsImmutable(): void
    {
        // On crée un nouvel objet Topic
        $topic = new Topic();

        // On définit une date manuellement
        $topic->setCreationDate(new \DateTime('2024-01-01'));

        // ✅ On vérifie que la valeur retournée est bien un objet DateTime
        $this->assertInstanceOf(\DateTime::class, $topic->getCreationDate());
    }
}

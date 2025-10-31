<?php

namespace App\Tests;

// ✅ Importe la classe TestCase de PHPUnit
// C'est la classe de base pour écrire des tests unitaires
use PHPUnit\Framework\TestCase;

// ✅ Déclaration de la classe ExampleTest
// Elle hérite de TestCase pour accéder aux outils PHPUnit (assertions, etc.)
class ExampleTest extends TestCase
{
    // ✅ Méthode de test
    // Elle doit obligatoirement commencer par "test" pour être reconnue par PHPUnit
    public function testBasicMathWorks(): void
    {
        // ✅ Assertion (vérification) :
        // On vérifie que 2 + 2 donne bien 4
        // Si ce n'est pas le cas → le test échoue
        $this->assertEquals(4, 2 + 2);
    }

    public function testAddition(): void
    {
        $this->assertEquals(10, 7 + 3);
    }

    // public function testMathFails(): void
    // {
    //     // Ce test échouera car 5 ≠ 2 + 2
    //     $this->assertEquals(5, 2 + 2);
    // }
}

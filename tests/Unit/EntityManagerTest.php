<?php

declare(strict_types=1);

namespace Tests\SamMcDonald\Norm\Unit;

use PHPUnit\Framework\TestCase;
use SamMcDonald\Norm\EntityManager;
use Tests\SamMcDonald\Norm\Factory\Norma;

class EntityManagerTest extends TestCase
{
    public function testNewEntityManager(): void
    {
        $userEntity = new \Tests\SamMcDonald\Norm\Unit\Entities\User();
        $userEntity->userFirstName = 'foo';
        $userEntity->email = null;

        $connection = Norma::createValidConnection();
        $sut = new EntityManager($connection);

        // When success, true should be returned
        static::assertTrue(
            $sut->persist($userEntity)
        );

        $results = $sut->findAll(\Tests\SamMcDonald\Norm\Unit\Entities\User::class);
        static::assertCount(1, $results);

        // @todo: Now assert the actual db
    }
}

<?php

namespace Wexample\SymfonyPseudocode\Tests\Fixtures\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Wexample\SymfonyPseudocode\Tests\Fixtures\Entity\TestEntity;

class TestEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestEntity::class);
    }

    public function findByName(string $name): ?TestEntity
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function findActive(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.active = :active')
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }
}

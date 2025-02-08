<?php

namespace Wexample\SymfonyPseudocode\Tests\Fixtures\Classes\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Wexample\SymfonyPseudocode\Tests\Fixtures\Entity\TestMinimalEntity;

class TestMinimalEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestMinimalEntity::class);
    }

    public function findByName(string $name)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getResult();
    }
}

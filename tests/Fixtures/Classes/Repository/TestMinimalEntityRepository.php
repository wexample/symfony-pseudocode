<?php

namespace Wexample\SymfonyPseudocode\Tests\Fixtures\Classes\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Wexample\SymfonyPseudocode\Tests\Fixtures\Entity\TestMinimalEntity;

/**
 * A basic Symfony repository
 */
class TestMinimalEntityRepository extends ServiceEntityRepository
{
    /**
     * Class initialization.
     *
     * @param ManagerRegistry $registry A given service.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestMinimalEntity::class);
    }
}

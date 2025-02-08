<?php

namespace Wexample\SymfonyPseudocode\Tests\Fixtures\Classes\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * A Symfony entity that only contains an id in a flat notation
 */
#[ORM\Entity]
class TestMinimalEntity
{
    /**
     * @var ?int The identifier of the entity.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Returns the unique id of the entity instance.
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}

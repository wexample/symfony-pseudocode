<?php

namespace Wexample\SymfonyPseudocode\Tests\Fixtures\Classes\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class TestMinimalEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}

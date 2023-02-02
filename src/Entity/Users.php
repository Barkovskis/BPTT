<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $invited_by_user_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getInvitedByUserId(): ?int
    {
        return $this->invited_by_user_id;
    }

    public function setInvitedByUserId(?int $invited_by_user_id): self
    {
        $this->invited_by_user_id = $invited_by_user_id;

        return $this;
    }
}

<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class Hh implements UserInterface
{
    private $j;

    /**
     * @var list<string> The user roles
     */
    private $roles = [];

    public function getJ(): ?string
    {
        return $this->j;
    }

    public function setJ(string $j): static
    {
        $this->j = $j;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->j;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }
}

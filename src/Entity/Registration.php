<?php

namespace App\Entity;

use App\Repository\RegistrationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegistrationRepository::class)]
class Registration
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CANCELLED = 'cancelled';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Fateh::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Fateh $user = null;

    #[ORM\ManyToOne(targetEntity: SummitLocation::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?SummitLocation $summitLocation = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $mealPreference = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $specialNeeds = null;

    #[ORM\Column(length: 30, options: ['default' => self::STATUS_ACTIVE])]
    private string $status = self::STATUS_ACTIVE;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Fateh
    {
        return $this->user;
    }

    public function setUser(?Fateh $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getSummitLocation(): ?SummitLocation
    {
        return $this->summitLocation;
    }

    public function setSummitLocation(?SummitLocation $summitLocation): static
    {
        $this->summitLocation = $summitLocation;

        return $this;
    }

    public function getMealPreference(): ?string
    {
        return $this->mealPreference;
    }

    public function setMealPreference(?string $mealPreference): static
    {
        $this->mealPreference = $mealPreference;

        return $this;
    }

    public function getSpecialNeeds(): ?string
    {
        return $this->specialNeeds;
    }

    public function setSpecialNeeds(?string $specialNeeds): static
    {
        $this->specialNeeds = $specialNeeds;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}

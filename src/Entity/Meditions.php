<?php

namespace App\Entity;

use App\Repository\MeditionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeditionsRepository::class)]
class Meditions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sensors $sensor = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Wines $wine = null;

    #[ORM\Column(length: 100)]
    private ?string $color = null;

    #[ORM\Column]
    private ?int $temperature = null;

    #[ORM\Column]
    private ?int $graduation = null;

    #[ORM\Column]
    private ?int $ph = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getSensor(): ?Sensors
    {
        return $this->sensor;
    }

    public function setSensor(?Sensors $sensor): static
    {
        $this->sensor = $sensor;

        return $this;
    }

    public function getWine(): ?Wines
    {
        return $this->wine;
    }

    public function setWine(?Wines $wine): static
    {
        $this->wine = $wine;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getTemperature(): ?int
    {
        return $this->temperature;
    }

    public function setTemperature(int $temperature): static
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getGraduation(): ?int
    {
        return $this->graduation;
    }

    public function setGraduation(int $graduation): static
    {
        $this->graduation = $graduation;

        return $this;
    }

    public function getPh(): ?int
    {
        return $this->ph;
    }

    public function setPh(int $ph): static
    {
        $this->ph = $ph;

        return $this;
    }
}

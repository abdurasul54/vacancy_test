<?php

namespace App\Entity;

use App\Repository\TaxRatesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaxRatesRepository::class)]
class TaxRates
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $country_code = null;

    #[ORM\Column(length: 255)]
    private ?string $rate_percentage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountryCode(): ?string
    {
        return $this->country_code;
    }

    public function setCountryCode(string $country_code): self
    {
        $this->country_code = $country_code;

        return $this;
    }

    public function getRatePercentage(): ?string
    {
        return $this->rate_percentage;
    }

    public function setRatePercentage(string $rate_percentage): self
    {
        $this->rate_percentage = $rate_percentage;

        return $this;
    }
}

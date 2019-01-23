<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EpisodesRepository")
 */
class Episodes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Animes", inversedBy="episodes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $anime;

    /**
     * @ORM\Column(type="integer")
     */
    private $season;

    /**
     * @ORM\Column(type="string", length=255,  nullable=true)
     */
    private $episodes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnime(): ?Animes
    {
        return $this->anime;
    }

    public function setAnime(?Animes $anime): self
    {
        $this->anime = $anime;

        return $this;
    }

    public function getSeason(): ?int
    {
        return $this->season;
    }

    public function setSeason(int $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getEpisodes(): ?string
    {
        return $this->episodes;
    }

    public function setEpisodes(string $episodes): self
    {
        $this->episodes = $episodes;

        return $this;
    }
}

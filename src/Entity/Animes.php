<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnimesRepository")
 */
class Animes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $synopsis;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TypeLink", mappedBy="anime", orphanRemoval=true)
     */
    private $typeLinks;

    public function __construct()
    {
        $this->typeLinks = new ArrayCollection();
    }

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

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection|TypeLink[]
     */
    public function getTypeLinks(): Collection
    {
        return $this->typeLinks;
    }

    public function addTypeLink(TypeLink $typeLink): self
    {
        if (!$this->typeLinks->contains($typeLink)) {
            $this->typeLinks[] = $typeLink;
            $typeLink->setAnime($this);
        }

        return $this;
    }

    public function removeTypeLink(TypeLink $typeLink): self
    {
        if ($this->typeLinks->contains($typeLink)) {
            $this->typeLinks->removeElement($typeLink);
            // set the owning side to null (unless already changed)
            if ($typeLink->getAnime() === $this) {
                $typeLink->setAnime(null);
            }
        }

        return $this;
    }
}

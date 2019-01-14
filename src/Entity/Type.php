<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TypeRepository")
 */
class Type
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
     * @ORM\OneToMany(targetEntity="App\Entity\TypeLink", mappedBy="type", orphanRemoval=true)
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
            $typeLink->setType($this);
        }

        return $this;
    }

    public function removeTypeLink(TypeLink $typeLink): self
    {
        if ($this->typeLinks->contains($typeLink)) {
            $this->typeLinks->removeElement($typeLink);
            // set the owning side to null (unless already changed)
            if ($typeLink->getType() === $this) {
                $typeLink->setType(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TypeLinkRepository")
 */
class TypeLink
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Animes", inversedBy="typeLinks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $anime;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Type", inversedBy="typeLinks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

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

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnimesRepository")
 * @UniqueEntity("name", message="Cet animé est déjà enregistré.")
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Type", inversedBy="animes")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Episodes", mappedBy="anime", orphanRemoval=true)
     */
    private $episodes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageCard;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageCardBlur;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="anime")
     */
    private $comments;

    public function __construct()
    {
        $this->typeLinks = new ArrayCollection();
        $this->type = new ArrayCollection();
        $this->episodes = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
     * @return Collection|Type[]
     */
    public function getType(): Collection
    {
        return $this->type;
    }

    public function addType(Type $type): self
    {
        if (!$this->type->contains($type)) {
            $this->type[] = $type;
        }

        return $this;
    }

    public function removeType(Type $type): self
    {
        if ($this->type->contains($type)) {
            $this->type->removeElement($type);
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Episodes[]
     */
    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    public function addEpisode(Episodes $episode): self
    {
        if (!$this->episodes->contains($episode)) {
            $this->episodes[] = $episode;
            $episode->setAnime($this);
        }

        return $this;
    }

    public function removeEpisode(Episodes $episode): self
    {
        if ($this->episodes->contains($episode)) {
            $this->episodes->removeElement($episode);
            // set the owning side to null (unless already changed)
            if ($episode->getAnime() === $this) {
                $episode->setAnime(null);
            }
        }

        return $this;
    }

    public function getImageCard(): ?string
    {
        return $this->imageCard;
    }

    public function setImageCard(?string $imageCard): self
    {
        $this->imageCard = $imageCard;

        return $this;
    }

    public function getImageCardBlur(): ?string
    {
        return $this->imageCardBlur;
    }

    public function setImageCardBlur(?string $imageCardBlur): self
    {
        $this->imageCardBlur = $imageCardBlur;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAnime($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAnime() === $this) {
                $comment->setAnime(null);
            }
        }

        return $this;
    }
}

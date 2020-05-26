<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Games
 *
 * @ORM\Table(name="games")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\GamesRepository")
 */
class Games
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_game", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idGame;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="text", length=65535, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="genres", type="text", length=65535, nullable=true)
     */
    private $genres;

    /**
     * @var string|null
     *
     * @ORM\Column(name="publishers", type="text", length=65535, nullable=true)
     */
    private $publishers;

    /**
     * @var int|null
     *
     * @ORM\Column(name="review_score", type="integer", nullable=true)
     */
    private $reviewScore;

    /**
     * @var float|null
     *
     * @ORM\Column(name="used_price", type="float", precision=10, scale=0, nullable=true)
     */
    private $usedPrice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="console", type="text", length=65535, nullable=true)
     */
    private $console;

    /**
     * @var int|null
     *
     * @ORM\Column(name="release_year", type="integer", nullable=true)
     */
    private $releaseYear;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Users", inversedBy="idGame")
     * @ORM\JoinTable(name="comments",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_game", referencedColumnName="id_game")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     *   }
     * )
     */
    private $idUser;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idUser = new \Doctrine\Common\Collections\ArrayCollection();
    }

    // Getters & Setters

    /**
     * @return string|null
     */
    public function getIdGame() {
        return $this->idGame;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getGenres(): ?string
    {
        return $this->genres;
    }

    public function setGenres(?string $genres): self
    {
        $this->genres = $genres;

        return $this;
    }

    public function getPublishers(): ?string
    {
        return $this->publishers;
    }

    public function setPublishers(?string $publishers): self
    {
        $this->publishers = $publishers;

        return $this;
    }

    public function getReviewScore(): ?int
    {
        return $this->reviewScore;
    }

    public function setReviewScore(?int $reviewScore): self
    {
        $this->reviewScore = $reviewScore;

        return $this;
    }

    public function getUsedPrice(): ?float
    {
        return $this->usedPrice;
    }

    public function setUsedPrice(?float $usedPrice): self
    {
        $this->usedPrice = $usedPrice;

        return $this;
    }

    public function getConsole(): ?string
    {
        return $this->console;
    }

    public function setConsole(?string $console): self
    {
        $this->console = $console;

        return $this;
    }

    public function getReleaseYear(): ?int
    {
        return $this->releaseYear;
    }

    public function setReleaseYear(?int $releaseYear): self
    {
        $this->releaseYear = $releaseYear;

        return $this;
    }

    /**
     * @return Collection|Users[]
     */
    public function getIdUser(): Collection
    {
        return $this->idUser;
    }

    public function addIdUser(Users $idUser): self
    {
        if (!$this->idUser->contains($idUser)) {
            $this->idUser[] = $idUser;
        }

        return $this;
    }

    public function removeIdUser(Users $idUser): self
    {
        if ($this->idUser->contains($idUser)) {
            $this->idUser->removeElement($idUser);
        }

        return $this;
    }

}

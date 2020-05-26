<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Games
 *
 * @ORM\Table(name="games")
 * @ORM\Entity
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

}

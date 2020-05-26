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
     * @ORM\Column(name="title", type="text", length=65535, nullable=true, options={"default"="NULL"})
     */
    private $title = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="genres", type="text", length=65535, nullable=true, options={"default"="NULL"})
     */
    private $genres = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="publishers", type="text", length=65535, nullable=true, options={"default"="NULL"})
     */
    private $publishers = 'NULL';

    /**
     * @var int|null
     *
     * @ORM\Column(name="review_score", type="integer", nullable=true, options={"default"="NULL"})
     */
    private $reviewScore = 'NULL';

    /**
     * @var float|null
     *
     * @ORM\Column(name="used_price", type="float", precision=10, scale=0, nullable=true, options={"default"="NULL"})
     */
    private $usedPrice = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="console", type="text", length=65535, nullable=true, options={"default"="NULL"})
     */
    private $console = 'NULL';

    /**
     * @var int|null
     *
     * @ORM\Column(name="release_year", type="integer", nullable=true, options={"default"="NULL"})
     */
    private $releaseYear = 'NULL';


}

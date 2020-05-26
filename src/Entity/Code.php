<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Code
 *
 * @ORM\Table(name="code", indexes={@ORM\Index(name="code_games_id_game_fk", columns={"id_game"}), @ORM\Index(name="Code_User0_FK", columns={"id_user"})})
 * @ORM\Entity
 */
class Code
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_code", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCode;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=30, nullable=false)
     */
    private $code;

    /**
     * @var bool
     *
     * @ORM\Column(name="used", type="boolean", nullable=false)
     */
    private $used;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", precision=10, scale=0, nullable=false)
     */
    private $price;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="purchase_date", type="date", nullable=false)
     */
    private $purchaseDate;

    /**
     * @var string
     *
     * @ORM\Column(name="url_invoice", type="string", length=20, nullable=false)
     */
    private $urlInvoice;

    /**
     * @var \Games
     *
     * @ORM\ManyToOne(targetEntity="Games")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_game", referencedColumnName="id_game")
     * })
     */
    private $idGame;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     * })
     */
    private $idUser;

    public function getIdCode(): ?int
    {
        return $this->idCode;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getUsed(): ?bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): self
    {
        $this->used = $used;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPurchaseDate(): ?\DateTimeInterface
    {
        return $this->purchaseDate;
    }

    public function setPurchaseDate(\DateTimeInterface $purchaseDate): self
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    public function getUrlInvoice(): ?string
    {
        return $this->urlInvoice;
    }

    public function setUrlInvoice(string $urlInvoice): self
    {
        $this->urlInvoice = $urlInvoice;

        return $this;
    }

    public function getIdGame(): ?Games
    {
        return $this->idGame;
    }

    public function setIdGame(?Games $idGame): self
    {
        $this->idGame = $idGame;

        return $this;
    }

    public function getIdUser(): ?Users
    {
        return $this->idUser;
    }

    public function setIdUser(?Users $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }


}

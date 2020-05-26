<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Code
 *
 * @ORM\Table(name="code", indexes={@ORM\Index(name="Code_User0_FK", columns={"id_user"})})
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
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     * })
     */
    private $idUser;


}

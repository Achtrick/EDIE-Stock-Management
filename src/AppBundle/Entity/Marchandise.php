<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Marchandise
 *
 * @ORM\Table(name="marchandise")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MarchandiseRepository")
 */
class Marchandise
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var int
     *
     * @ORM\Column(name="quantite", type="integer")
     */
    private $quantite;

    /**
     * @var int
     *
     * @ORM\Column(name="usedquantite", type="integer")
     */
    private $usedquantite;

    /**
     * @var int
     *
     * @ORM\Column(name="chantier", type="integer")
     */
    private $chantier;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Marchandise
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set quantite
     *
     * @param integer $quantite
     *
     * @return Marchandise
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get quantite
     *
     * @return int
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set chantier
     *
     * @param integer $chantier
     *
     * @return Marchandise
     */
    public function setChantier($chantier)
    {
        $this->chantier = $chantier;

        return $this;
    }

    /**
     * Get chantier
     *
     * @return int
     */
    public function getChantier()
    {
        return $this->chantier;
    }

    /**
     * Set usedquantite
     *
     * @param integer $usedquantite
     *
     * @return Marchandise
     */
    public function setUsedquantite($usedquantite)
    {
        $this->usedquantite = $usedquantite;

        return $this;
    }

    /**
     * Get usedquantite
     *
     * @return integer
     */
    public function getUsedquantite()
    {
        return $this->usedquantite;
    }
}

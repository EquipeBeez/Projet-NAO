<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Taxrefv10
 *
 * @ORM\Table(name="taxrefv10")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Taxrefv10Repository")
 */
class Taxrefv10
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
     * @ORM\Column(name="famille", type="string", length=255, nullable=true)
     */
    private $Famille;

    /**
     * @var string
     *
     * @ORM\Column(name="lb_nom", type="string", length=255, nullable=true)
     */
    private $LbNom;

    /**
     * @var string
     *
     * @ORM\Column(name="lb_auteur", type="string", length=255, nullable=true)
     */
    private $LbAuteur;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_vern", type="string", length=255, nullable=true)
     */
    private $NomVern;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_vern_eng", type="string", length=255, nullable=true)
     */
    private $NomVernEng;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $Url;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set famille
     *
     * @param string $famille
     *
     * @return Taxrefv10
     */
    public function setFamille($famille)
    {
        $this->Famille = $famille;

        return $this;
    }

    /**
     * Get famille
     *
     * @return string
     */
    public function getFamille()
    {
        return $this->Famille;
    }

    /**
     * Set lbNom
     *
     * @param string $lbNom
     *
     * @return Taxrefv10
     */
    public function setLbNom($lbNom)
    {
        $this->LbNom = $lbNom;

        return $this;
    }

    /**
     * Get lbNom
     *
     * @return string
     */
    public function getLbNom()
    {
        return $this->LbNom;
    }

    /**
     * Set lbAuteur
     *
     * @param string $lbAuteur
     *
     * @return Taxrefv10
     */
    public function setLbAuteur($lbAuteur)
    {
        $this->LbAuteur = $lbAuteur;

        return $this;
    }

    /**
     * Get lbAuteur
     *
     * @return string
     */
    public function getLbAuteur()
    {
        return $this->LbAuteur;
    }

    /**
     * Set nomVern
     *
     * @param string $nomVern
     *
     * @return Taxrefv10
     */
    public function setNomVern($nomVern)
    {
        $this->NomVern = $nomVern;

        return $this;
    }

    /**
     * Get nomVern
     *
     * @return string
     */
    public function getNomVern()
    {
        return $this->NomVern;
    }

    /**
     * Set nomVernEng
     *
     * @param string $nomVernEng
     *
     * @return Taxrefv10
     */
    public function setNomVernEng($nomVernEng)
    {
        $this->NomVernEng = $nomVernEng;

        return $this;
    }

    /**
     * Get nomVernEng
     *
     * @return string
     */
    public function getNomVernEng()
    {
        return $this->NomVernEng;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Taxrefv10
     */
    public function setUrl($url)
    {
        $this->Url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->Url;
    }
}

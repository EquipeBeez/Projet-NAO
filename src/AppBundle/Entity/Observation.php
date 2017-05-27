<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Observation
 *
 * @ORM\Table(name="observation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ObservationRepository")
 */
class Observation
{

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $approuvedBy;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Taxrefv10")
     * @ORM\JoinColumn(nullable=false)
     */
    private $espece;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateRecord = new \DateTime();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateRecord", type="datetime")
     */
    private $dateRecord;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateObservation", type="datetime")
     */
    private $dateObservation;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var float
     *
     * @ORM\Column(name="gpsLatitude", type="float")
     */
    private $gpsLatitude;

    /**
     * @var float
     *
     * @ORM\Column(name="gpsLongitude", type="float")
     */
    private $gpsLongitude;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $image;


    /**
     * @var text
     *
     * @ORM\Column(name="rejectMessage", type="text", nullable=true)
     */
    private $rejectMessage;


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
     * Set dateRecord
     *
     * @param \DateTime $dateRecord
     *
     * @return Observation
     */
    public function setDateRecord($dateRecord)
    {
        $this->dateRecord = $dateRecord;

        return $this;
    }

    /**
     * Get dateRecord
     *
     * @return \DateTime
     */
    public function getDateRecord()
    {
        return $this->dateRecord;
    }

    /**
     * Set dateObservation
     *
     * @param \DateTime $dateObservation
     *
     * @return Observation
     */
    public function setDateObservation($dateObservation)
    {
        $this->dateObservation = $dateObservation;

        return $this;
    }

    /**
     * Get dateObservation
     *
     * @return \DateTime
     */
    public function getDateObservation()
    {
        return $this->dateObservation;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Observation
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set gpsLatitude
     *
     * @param float $gpsLatitude
     *
     * @return Observation
     */
    public function setGpsLatitude($gpsLatitude)
    {
        $this->gpsLatitude = $gpsLatitude;

        return $this;
    }

    /**
     * Get gpsLatitude
     *
     * @return float
     */
    public function getGpsLatitude()
    {
        return $this->gpsLatitude;
    }

    /**
     * Set gpsLongitude
     *
     * @param float $gpsLongitude
     *
     * @return Observation
     */
    public function setGpsLongitude($gpsLongitude)
    {
        $this->gpsLongitude = $gpsLongitude;

        return $this;
    }

    /**
     * Get gpsLongitude
     *
     * @return float
     */
    public function getGpsLongitude()
    {
        return $this->gpsLongitude;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Observation
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Observation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Observation
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set approuvedBy
     *
     * @param \UserBundle\Entity\User $approuvedBy
     *
     * @return Observation
     */
    public function setApprouvedBy(\UserBundle\Entity\User $approuvedBy = null)
    {
        $this->approuvedBy = $approuvedBy;

        return $this;
    }

    /**
     * Get approuvedBy
     *
     * @return \UserBundle\Entity\User
     */
    public function getApprouvedBy()
    {
        return $this->approuvedBy;
    }

    /**
     * Set espece
     *
     * @param \AppBundle\Entity\Taxrefv10 $espece
     *
     * @return Observation
     */
    public function setEspece(\AppBundle\Entity\Taxrefv10 $espece)
    {
        $this->espece = $espece;

        return $this;
    }

    /**
     * Get espece
     *
     * @return \AppBundle\Entity\Taxrefv10
     */
    public function getEspece()
    {
        return $this->espece;
    }

    /**
     * Set author
     *
     * @param \UserBundle\Entity\User $author
     *
     * @return Observation
     */
    public function setAuthor(\UserBundle\Entity\User $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set rejectMessage
     *
     * @param string $rejectMessage
     *
     * @return Observation
     */
    public function setRejectMessage($rejectMessage)
    {
        $this->rejectMessage = $rejectMessage;

        return $this;
    }

    /**
     * Get rejectMessage
     *
     * @return string
     */
    public function getRejectMessage()
    {
        return $this->rejectMessage;
    }
}


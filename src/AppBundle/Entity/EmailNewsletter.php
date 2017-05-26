<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * EmailNewsletter
 *
 * @ORM\Table(name="email_newsletter")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmailNewsletterRepository")
 * @UniqueEntity(
 *     fields="email",
 *     errorPath="email",
 *     message ="Cette adresse E-mail : '{{ value }}' est déjà inscrite à la Newsletter."
 * )
 */
class EmailNewsletter
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
     * @ORM\Column(name="email", type="string", length=255, unique=true, nullable=false)
     * @Assert\Email(
     *     message ="Cette adresse E-mail : '{{ value }}' n'est pas valide.",
     *     checkMX = false
     * )
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="emailCrypte", type="string", length=255, unique=true)
     */
    private $emailCrypter;

    public function __construct()
    {

        $this->emailCrypter = 'en attente de cryptage';
    }

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
     * Set email
     *
     * @param string $email
     *
     * @return EmailNewsletter
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set emailCrypter
     *
     * @param string $emailCrypter
     *
     * @return EmailNewsletter
     */
    public function setEmailCrypter($emailCrypter)
    {
        $this->emailCrypter = $emailCrypter;

        return $this;
    }

    /**
     * Get emailCrypter
     *
     * @return string
     */
    public function getEmailCrypter()
    {
        return $this->emailCrypter;
    }
}

<?php
namespace AppBundle\Services;

use AppBundle\Entity\EmailNewsletter;
use Doctrine\ORM\EntityManagerInterface;


class InsDesNewsletter
{
    private $em;

    /**
     * InsDesNewsletter constructor.
     * @param EntityManagerInterface $em
     * @param $saltRandom
     */

    public function __construct(EntityManagerInterface $em, $saltRandom)
    {
        $this->em = $em;
        $this->saltRandom = $saltRandom;
    }

    /**
     * @param $user
     */
    public function insDesNewsletter($user)
    {

        $email = $user->getEmail();
        $userEmail = $this->em->getRepository('AppBundle:EmailNewsletter')->findOneBy(array('email' => $email));
        // Ajout de l'adresse mail de l'utilisateur dans la liste de la Newsletter
        if ($user->getNewsletter() === true){

            if ($userEmail === null){
                $emailNewsletter = new EmailNewsletter();
                $emailNewsletter->setEmail($email);

                // Salt Random
                $salt = $this->saltRandom->randSalt(10);

                $emailCrypter = md5($salt.'desinscription'.$email);
                $emailNewsletter->setEmailCrypter($emailCrypter);

                $this->em->persist($emailNewsletter);
                $this->em->flush();
            }
        }
        // Retrait de l'adresse mail de l'utilisateur de la liste de la Newsletter
        else{

            if ($userEmail !== null) {
                $this->em->remove($userEmail);
                $this->em->flush();
            }
        }

    }
}

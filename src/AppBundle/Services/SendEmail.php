<?php

namespace AppBundle\Services;

use AppBundle\Entity\Contact;
use AppBundle\Entity\EmailNewsletter;
use AppBundle\Entity\Observation;
use UserBundle\Entity\User;
use Symfony\Component\Templating\EngineInterface;

class SendEmail
{

    protected $templating;
    protected $mailer;

    public function __construct(EngineInterface $templating, \Swift_Mailer $mailer)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
    }


    /**
     * Function pour envoyer le formulaire de contact en Email à l'administrateur
     *
     * @param Contact $contact
     *
     */
    public function sendEmailContact(Contact $contact)
    {
        // Envoie de l'email à l'Admin
        $message = \Swift_Message::newInstance()
            ->setSubject('Message de NAO')
            ->setFrom(array('info@nao.com' => 'Association NAO'))
            ->setTo('info@trukotop.com')
            ->setCharset('utf-8')
            ->setContentType('text/html')
            ->setBody(
                $this->templating->render('Emails/contactEmailAdmin.html.twig', array('contact' => $contact)),
                'text/html'
            )
        ;
        $this->mailer->send($message);

        // Envoie de l'email à l'Utilisateur
        $message = \Swift_Message::newInstance()
            ->setSubject('Message de NAO')
            ->setFrom(array('info@nao.com' => 'Association NAO'))
            ->setTo($contact->getEmail())
            ->setCharset('utf-8')
            ->setContentType('text/html')
            ->setBody(
                $this->templating->render('Emails/contactEmailUser.html.twig', array('contact' => $contact)),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }


    /**
     * @param Observation $observation
     */
    public function sendEmailReject(Observation $observation)
    {


        // Envoie de l'email à l'auteur
        $message = \Swift_Message::newInstance()
            ->setSubject("Message de NAO - Rejet d'une observation")
            ->setFrom(array('info@nao.com' => 'Association NAO'))
            ->setTo($observation->getAuthor()->getEmail())
            ->setCharset('utf-8')
            ->setContentType('text/html')
            ->setBody(
                $this->templating->render('Emails/rejectEmailAuthor.html.twig', array('observation' => $observation)),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }

    public function sendNewsletter($email, $contenu, $titre, $emailCrypter)
    {


        // Envoie de la newsletter
        $message = \Swift_Message::newInstance()
            ->setSubject("Votre Newsletter NAO")
            ->setFrom(array('info@nao.com' => 'Association NAO'))
            ->setTo($email)
            ->setCharset('utf-8')
            ->setContentType('text/html')
            ->setBody(
                $this->templating->render('Emails/templateNewsletter.html.twig', array(
                    'contenu' => $contenu,
                    'titre' => $titre,
                    'emailCrypter' => $emailCrypter)),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }
}
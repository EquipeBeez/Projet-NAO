<?php

namespace AppBundle\Services;

use AppBundle\Entity\Contact;
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
}
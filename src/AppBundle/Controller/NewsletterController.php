<?php

namespace AppBundle\Controller;



use AppBundle\Entity\EmailNewsletter;
use AppBundle\Form\EmailNewsletterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class NewsletterController extends Controller
{

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @param Request $request
     * @Route("/inscriptionnewsletter", name="inscription_newsletter")
     *
     */
    public function inscriptionNewsletterAction(Request $request)
    {
        $emailNewsletter = new EmailNewsletter();
        // On crée le formulaire
        $formNewsletter = $this->createForm(EmailNewsletterType::class, $emailNewsletter);

        $formNewsletter->handleRequest($request);
        if ($formNewsletter->isSubmitted() && $formNewsletter->isValid()) {
            // Affichage d'un message flash
            $request->getSession()->getFlashBag()->add('success', 'Vous êtes désormais inscrit à notre Newsletter');

            // Sauvegarder en Base de données

            $em = $this->getDoctrine()->getManager();
            $em->persist($emailNewsletter);
            $email = $emailNewsletter->getEmail();
            // Salt Random
            $salt = $this->container->get('app.saltRandom')->randSalt(10);

            $emailCrypter = md5($salt.'desinscription'.$email);
            $emailNewsletter->setEmailCrypter($emailCrypter);
            $em->flush();

            // Retour à la page d'accueil
            return $this->redirectToRoute('homepage');
            }

        // Sinon render page d'accueil afin de conserver les messages d'erreurs des Assert
        $request->getSession()->getFlashBag()->add('info', 'Adresse e-mail incorrect ou déjà enregistrée');
        $em = $this->getDoctrine()->getManager();
        $listLastObservations = $em->getRepository('AppBundle:Observation')->findLastObservations(3);

        return $this->render('AppBundle:Front:index.html.twig', array(
            'listLastObservations' => $listLastObservations,
            'formNews' => $formNewsletter->createView()
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/affichagenewsletter", name="affichage_newsletter")
     */
    public function affichageNewsletterSideBarAction()
    {

        $emailNewsletter = new EmailNewsletter();
        // On crée le formulaire
        $formNewsletter = $this->createForm(EmailNewsletterType::class, $emailNewsletter);

        return $this->render('AppBundle:Front:sideBarNewsletter.html.twig', array(
            'formNews' => $formNewsletter->createView()
        ));

    }

    /**
     * @param Request $request
     * @param $emailCrypter
     * @Route("/desinscription/{emailCrypter}", name="desinscription_newsletter")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function desinscriptionNewsletter(Request $request, $emailCrypter)
    {

        $em = $this->getDoctrine()->getManager();
        $emailNewsletter = $em->getRepository('AppBundle:EmailNewsletter')->findByEmailCrypter($emailCrypter);

        if ($emailNewsletter != null) {
            // Affichage d'un message flash
            $request->getSession()->getFlashBag()->add('success', 'Vous êtes bien désinscrit de notre Newsletter');

            foreach ($emailNewsletter as $value) {
                $email= $value->getEmail();
            }


            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('UserBundle:User')->findByEmail($email);
            if ($user != null) {
                foreach ($user as $value) {
                    $value->setNewsletter(false);
                }

            }
            // Sauvegarder en Base de données
            $em = $this->getDoctrine()->getManager();
            foreach ($emailNewsletter as $value) {
                $em->remove($value);
            }

            $em->flush();

            // Retour à la page d'accueil
            return $this->redirectToRoute('homepage');
        }
        else
        {
            throw new NotFoundHttpException("La page demandée n'existe pas");
        }
    }

}
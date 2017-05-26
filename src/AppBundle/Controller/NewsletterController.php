<?php

namespace AppBundle\Controller;



use AppBundle\Entity\EmailNewsletter;
use AppBundle\Entity\Newsletter;
use AppBundle\Form\EmailNewsletterType;
use AppBundle\Form\NewsletterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Method({"GET", "POST"})
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
     * @Method({"GET", "POST"})
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
    /**
     * @param Request $request
     * @Route("/admin/newsletter", name="admin_newsletter")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_MODERATEUR')")
     * @Method({"GET", "POST"})
     */
    public function newsletterAction(Request $request){

        $newsletter = new Newsletter();
        // On crée le formulaire
        $form = $this->createForm(NewsletterType::class, $newsletter);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $contenu = $newsletter->getContent();
            $titre = $newsletter->getTitle();
            // Sauvegarder en Base de données
            $em = $this->getDoctrine()->getManager();
            $em->persist($newsletter);
            $em->flush();

            // Envoi de la Newsletter
            $em = $this->getDoctrine()->getManager();
            $listEmail = $em->getRepository('AppBundle:EmailNewsletter')->findAll();
            foreach ($listEmail as $item) {

                $email = $item->getEmail();
                $emailCrypter = $item->getEmailCrypter();
                $this->container->get('app.sendEmail')->sendNewsletter($email, $contenu, $titre, $emailCrypter);

            }

            // Affichage d'un message flash
            $request->getSession()->getFlashBag()->add('success', 'Newsletter publiée');
            // Retour à la page newsletter
            //return $this->redirectToRoute('admin_newsletter');
            return $this->render('AppBundle:Admin:newsletter.html.twig', array(
                'form' => $form->createView()));
        }

        return $this->render('AppBundle:Admin:newsletter.html.twig', array(
            'form' => $form->createView()
        ));

    }
    /**
     *
     * @Route("/admin/viewallnewsletter/{page}", name="view_all_newsletter")
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_MODERATEUR')")
     * @Method({"GET"})
     *
     */
    public function viewAllNewsletterAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $em->getRepository('AppBundle:Newsletter')->findAll(), /* query NOT result */
            $page/*page number*/,
            5/*limit per page*/
        );
        return $this->render('AppBundle:Admin:viewAllNewsletter.html.twig', array(
            'pagination' => $pagination,
        ));
    }
    /**
     *
     * @Route("/admin/viewallregistered/{page}", name="view_all_registered")
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET"})
     *
     */
    public function viewAllRegisteredAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $em->getRepository('AppBundle:EmailNewsletter')->findAll(), /* query NOT result */
            $page/*page number*/,
            25/*limit per page*/
        );
        return $this->render('AppBundle:Admin:viewAllRegistered.html.twig', array(
            'pagination' => $pagination,
        ));
    }
    /**
     * @param Request $request
     * @param $emailCrypter
     * @Route("/admin/desinscription/{emailCrypter}", name="admin_desinscription_newsletter")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function adminDesinscriptionNewsletter(Request $request, $emailCrypter)
    {

        $em = $this->getDoctrine()->getManager();
        $emailNewsletter = $em->getRepository('AppBundle:EmailNewsletter')->findByEmailCrypter($emailCrypter);

        if ($emailNewsletter != null) {
            // Affichage d'un message flash
            $request->getSession()->getFlashBag()->add('success', 'L\'utilisateur est bien désinscrit de notre Newsletter');

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

            // Retour à la liste des inscrits
            return $this->redirectToRoute('view_all_registered', array(
                'page' => 1
            ));
        }
        else
        {
            throw new NotFoundHttpException("La page demandée n'existe pas");
        }
    }

}
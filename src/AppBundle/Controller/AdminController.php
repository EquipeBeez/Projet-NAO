<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Newsletter;
use AppBundle\Form\NewsletterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ConfigurationType;
use AppBundle\Entity\Taxrefv10;
use AppBundle\Form\Taxrefv10Type;
use AppBundle\Entity\Observation;
use AppBundle\Form\ObservationType;
use AppBundle\Form\ObservationRejectType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class AdminController extends Controller
{
    /**
     *
     * @Route("/admin", name="admin")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USERNAT')")
     * @Method({"GET"})
     *
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Admin:index.html.twig');
    }

    /**
     *
     * @Route("/admin/configuration", name="admin_configuration")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     *
     */
    public function configurationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // Récuperation de la configuration
        $config = $this->container->get('services.loadconfig')->loadConfig();
        $form = $this->get('form.factory')->create(ConfigurationType::class, $config);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'La configuration à bien été sauvegardée.');
            return $this->redirectToRoute('admin_configuration');
        }
        return $this->render('AppBundle:Admin:configuration.html.twig', array(
            'form'   => $form->createView(),
        ));
    }

    /**
     *
     * @Route("/admin/viewallspecies/{page}", name="admin_view_all_species")
     * @param $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET"})
     *
     */
    public function viewAllSpeciesAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->container->get('services.loadconfig')->loadConfig();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $em->getRepository('AppBundle:Taxrefv10')->getAll(), /* query NOT result */
            $page/*page number*/,
            25/*limit per page*/
        );
        return $this->render('AppBundle:Admin:viewAllSpecies.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     *
     * @Route("/admin/viewonespecies/{id}", name="admin_view_one_species")
     * @param $taxrefv10
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USERNAT')")
     * @Method({"GET"})
     *
     */
    public function viewOneSpeciesAction(Taxrefv10 $taxrefv10)
    {
        $em = $this->getDoctrine()->getManager();
        $listObservations = $em->getRepository('AppBundle:Observation')->findBy(array('espece' => $taxrefv10));
        return $this->render('AppBundle:Admin:viewOneSpecies.html.twig', array(
            'taxrefv10' => $taxrefv10,
            'listObservations' => $listObservations,
        ));
    }

    /**
     *
     * @Route("/admin/delspecies/{id}", name="admin_del_species")
     * @param $taxrefv10
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     *
     */
    public function delSpeciesAction(Taxrefv10 $taxrefv10, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($taxrefv10);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', "L'espèce a bien été supprimée.");
            return $this->redirectToRoute('admin_view_all_species', array('page' => 1));
        }
        return $this->render('AppBundle:Admin:confirmDelSpecies.html.twig', array(
            'taxrefv10' => $taxrefv10,
            'form'   => $form->createView(),
        ));
    }

    /**
     *
     * @Route("/admin/editspecies/{id}", name="admin_edit_species")
     * @param Taxrefv10 $taxrefv10
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     *
     */
    public function editSpeciesAction(Taxrefv10 $taxrefv10, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->create(Taxrefv10Type::class, $taxrefv10);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', "L'espèce a bien été modifiée.");
            return $this->redirectToRoute('admin_view_one_species', array('id' => $taxrefv10->getId()));
        }
        return $this->render('AppBundle:Admin:editSpecies.html.twig', array(
            'taxrefv10' => $taxrefv10,
            'form'   => $form->createView(),
        ));
    }

    /**
     *
     * @Route("/admin/viewallobservations/{page}/{status}", name="admin_view_all_observations")
     * @param $page
     * @param null $status
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USERNAT')")
     * @Method({"GET"})
     *
     */
    public function viewAllObservationsAction($page, $status = null)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->container->get('services.loadconfig')->loadConfig();

        if ($status === null)
        {
            $query = $em->getRepository('AppBundle:Observation')->getAll(); /* query NOT result */
        }
        else
        {
            $query = $em->getRepository('AppBundle:Observation')->findObsWithStatus($status); /* query NOT result */
        }
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            25/*limit per page*/
        );
        return $this->render('AppBundle:Admin:viewAllObservations.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     *
     * @Route("/admin/viewoneobservation/{id}", name="admin_view_one_observation")
     * @param $observation
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USERNAT')")
     * @Method({"GET"})
     *
     */
    public function viewOneObservationAction(Observation $observation)
    {
        return $this->render('AppBundle:Admin:viewOneObservation.html.twig', array(
            'observation' => $observation,
        ));
    }

    /**
     *
     * @Route("/admin/delobservation/{id}", name="admin_del_observation")
     * @param $observation
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     *
     */
    public function delObservationAction(Observation $observation, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($observation);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', "L'observation a bien été supprimée.");
            return $this->redirectToRoute('admin_view_all_observations', array('page' => 1));
        }
        return $this->render('AppBundle:Admin:confirmDelObservation.html.twig', array(
            'observation' => $observation,
            'form'   => $form->createView(),
        ));
    }

    /**
     *
     * @Route("/admin/editobservation/{id}", name="admin_edit_observation")
     * @param $observation
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     *
     */
    public function editObservationAction(Observation $observation, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->create(ObservationType::class, $observation);
        $oldImage = $observation->getImage();
        $form->handleRequest($request);
        if (null === $observation->getImage()) {
            $observation->setImage($oldImage->getFilename());
        }
        if ($form->isSubmitted() && $form->isValid())
        {
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', "L'observation a bien été modifiée.");
            return $this->redirectToRoute('admin_view_one_observation', array('id' => $observation->getId()));
        }
        return $this->render('AppBundle:Admin:editObservation.html.twig', array(
            'observation' => $observation,
            'form'   => $form->createView(),
        ));
    }


    /**
     * @Route("/admin/validobservation/{id}", name="admin_valid_observation")
     * @param $observation
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USERNAT')")
     * @Method({"GET", "POST"})
     *
     */
    public function validObservationAction(Observation $observation, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $oldImage = $observation->getImage()->getFilename();
        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $observation->setStatus($this->getParameter('var_project')['status_obs_valid']);
            $observation->setApprouvedBy($this->getUser());
            $observation->setImage($oldImage);
            $observation->setRejectMessage(null);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', "L'observation a bien été validée.");
            return $this->redirectToRoute('admin_view_all_observations', array('page' => 1));
        }
        return $this->render('AppBundle:Admin:confirmValidObservation.html.twig', array(
            'observation' => $observation,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/rejectobservation/{id}", name="admin_reject_observation")
     * @param $observation
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USERNAT')")
     * @Method({"GET", "POST"})
     *
     */
    public function rejectObservationAction(Observation $observation, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $oldImage = $observation->getImage()->getFilename();
        $form = $this->get('form.factory')->create(ObservationRejectType::class, $observation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $observation->setStatus($this->getParameter('var_project')['status_obs_rejeted']);
            $observation->setApprouvedBy($this->getUser());
            $observation->setImage($oldImage);
            $em->flush();

            // Envoi d'un Email à l'auteur de l'observation.
            $this->container->get('app.sendEmail')->sendEmailReject($observation);

            $request->getSession()->getFlashBag()->add('success', "L'observation a bien été rejetée.");
            return $this->redirectToRoute('admin_view_all_observations', array('page' => 1));
        }
        return $this->render('AppBundle:Admin:confirmRejectObservation.html.twig', array(
            'observation' => $observation,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @Route("/admin/newsletter", name="admin_newsletter")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newsletterAction(Request $request){

        $newsletter = new Newsletter();
        // On crée le formulaire
        $form = $this->createForm(NewsletterType::class, $newsletter);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Affichage d'un message flash
            $request->getSession()->getFlashBag()->add('success', 'Newsletter publiée');

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

            // Retour à la page newsletter
            return $this->redirectToRoute('admin_newsletter');
        }

        return $this->render('AppBundle:Admin:newsletter.html.twig', array(
            'form' => $form->createView()
        ));

    }

}

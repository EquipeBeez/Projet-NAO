<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Contact;
use AppBundle\Entity\EmailNewsletter;
use AppBundle\Entity\Taxrefv10;
use AppBundle\Entity\Observation;
use AppBundle\Form\EmailNewsletterType;
use AppBundle\Form\ObservationFrontType;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $listLastObservations = $em->getRepository('AppBundle:Observation')->findLastObservations(3);

        $emailNewsletter = new EmailNewsletter();
        // On crée le formulaire
        $formNewsletter = $this->createForm(EmailNewsletterType::class, $emailNewsletter);


        return $this->render('AppBundle:Front:index.html.twig', array(
            'listLastObservations' => $listLastObservations,
            'formNews' => $formNewsletter->createView()
        ));
    }

    /**
     *
     * @Route("/viewallspecies/{page}", name="view_all_species")
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method({"GET"})
     *
     */
    public function viewAllSpeciesAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppBundle:Taxrefv10')->getAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page/*page number*/,
            25/*limit per page*/
        );
        return $this->render('AppBundle:Front:viewAllSpecies.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     *
     * @Route("/viewallobservations/{page}", name="view_all_observations")
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method({"GET"})
     *
     */
    public function viewAllObservationsAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppBundle:Observation')->findObsWithStatus($this->getParameter('var_project')['status_obs_valid']);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page/*page number*/,
            25/*limit per page*/
        );
        return $this->render('AppBundle:Front:viewAllObservations.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     *
     * @Route("/viewonespecies/{id}", name="view_one_species")
     * @param $taxrefv10
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method({"GET"})
     *
     */
    public function viewOneSpeciesAction(Taxrefv10 $taxrefv10)
    {
        $em = $this->getDoctrine()->getManager();
        $listObservations = $em->getRepository('AppBundle:Observation')->findBy(array('espece' => $taxrefv10));
        return $this->render('AppBundle:Front:viewOneSpecies.html.twig', array(
            'taxrefv10' => $taxrefv10,
            'listObservations' => $listObservations,
        ));
    }

    /**
     *
     * @Route("/viewoneobservation/{id}", name="view_one_observation")
     * @param $observation
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method({"GET"})
     *
     */
    public function viewOneObservationAction(Observation $observation)
    {
        return $this->render('AppBundle:Front:viewOneObservation.html.twig', array(
            'observation' => $observation,
        ));
    }

    /**
     * @Route("/choicespecies", name="choice_species")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     * @Method({"GET", "POST"})
     *
     */
    public function choiceSpeciesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppBundle:Taxrefv10')->getAll();
        $listSpecies = $query->getArrayResult();
        $arraySpecies = array();
        foreach ($listSpecies as $listSpecie)
        {
            $arrayTmp = array('id' => $listSpecie['id'] , 'value' => $listSpecie['LbNom']. ", " .$listSpecie['NomVern'] , 'lbnom' => $listSpecie['LbNom']);
            $arraySpecies[] = $arrayTmp;
        }
        $form = $this->createFormBuilder()
            ->add('species', TextType::class)
            ->add('id', HiddenType::class)
            ->getForm();

        return $this->render('AppBundle:Front:choiceSpecies.html.twig', array(
            'arraySpecies' => json_encode($arraySpecies, JSON_UNESCAPED_UNICODE),
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/validchoicespecies", name="valid_choice_species")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     * @Method({"GET", "POST"})
     *
     */
    public function validChoiceSpecies(Request $request)
    {
        if ($request->getMethod() == 'POST')
        {
            if ($request->request->get('form')['id'])
            {
                $id = $request->request->get('form')['id'];
                return $this->redirectToRoute('create_observation', array('id' => $id ));
            }
        }
        $request->getSession()->getFlashBag()->add('warning', 'Veuillez choisir une espèce valide !');
        return $this->redirectToRoute('choice_species');
    }

    /**
     *
     * @Route("/createobservation/{id}", name="create_observation")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     * @Method({"GET", "POST"})
     *
     */
    public function createObservationAction(Request $request, $id = null)
    {
        $em = $this->getDoctrine()->getManager();
        $observation = new Observation();
        if ($id != null)
        {
            $species = $em->getRepository('AppBundle:Taxrefv10')->findOneById($id);
            $observation->setEspece($species);
        }
        $observation->setStatus('waiting');
        $observation->setAuthor($this->getUser());
        $form   = $this->get('form.factory')->create(ObservationFrontType::class, $observation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($observation);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'Observation bien enregistrée.');
            return $this->redirectToRoute('view_one_observation', array('id' => $observation->getId()));
        }
        return $this->render('AppBundle:Front:createObservation.html.twig', array(
            'form' => $form->createView(),
            'observation' => $observation,
        ));
    }

    /**
     *
     * @Route("/learnmore", name="learn_more")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method({"GET"})
     *
     */
    public function learnMoreAction()
    {
        return $this->render('AppBundle:Front:learnMore.html.twig');
    }

    /**
     *
     * @Route("/landing", name="landing")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method({"GET"})
     *
     */
    public function landingAction(Request $request)
    {
        $formFactory = $this->get('fos_user.registration.form.factory');
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);
        return $this->render('AppBundle:Landing:index.html.twig', array (
            'form' => $form->createView()));
    }

    /**
     * @Route("/viewmyobservation/{page}", name="view_my_observation")
     * @param $page
     * @return Response
     */
    public function viewMyObservationAction($page)
    {
        // On récupère l'utilisateur courant
        $user = $this->getUser();
        if ($user !== null) {

            $em = $this->getDoctrine()->getManager();
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $em->getRepository('AppBundle:Observation')->findByAuthor($user), /* query NOT result */
                $page/*page number*/,
                25/*limit per page*/
            );
            return $this->render('AppBundle:Front:viewMyObservation.html.twig', array(
                'page' => $page,
                'pagination' => $pagination));
        }
        else {
                throw new NotFoundHttpException("La page demandée n'existe pas");
        }

    }

    /**
     * @Route("/deleteobservation/{id}", name="delete_my_observation")
     * @param Observation $observation
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function deleteMyObservationAction(Observation $observation, Request $request)
    {
        // On récupère l'utilisateur courant
        $user = $this->getUser();

        // On vérifie que l'utilisateur courant est bien l'auteur de l'observation
        if ($user == $observation->getAuthor()) {
            $em = $this->getDoctrine()->getManager();
            $form = $this->get('form.factory')->create();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->remove($observation);
                $em->flush();
                $request->getSession()->getFlashBag()->add('success', "L'observation a bien été supprimée.");
                return $this->redirectToRoute('view_my_observation', array('page' => 1 ));
            }

            return $this->render('AppBundle:Front:confirmDelObservation.html.twig', array(
                'observation' => $observation,
                'form' => $form->createView(),
            ));
        }
        else {
            throw new NotFoundHttpException("La page demandée n'existe pas");
        }
    }

    /**
     * @Route("/editmyobservation/{id}", name="edit_my_observation")
     * @param Observation $observation
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editMyObservationAction(Observation $observation, Request $request)
    {
        // On récupère l'utilisateur courant
        $user = $this->getUser();

        // On vérifie que l'utilisateur courant est bien l'auteur de l'observation
        if ($user == $observation->getAuthor()) {
            $em = $this->getDoctrine()->getManager();
            $form = $this->get('form.factory')->create(ObservationFrontType::class, $observation);
            $oldImage = $observation->getImage();
            $form->handleRequest($request);
            if (null === $observation->getImage()) {
                $observation->setImage($oldImage->getFilename());
            }
            if ($form->isSubmitted() && $form->isValid()) {
                $observation->setStatus($this->getParameter('var_project')['status_obs_waiting']);
                $observation->setRejectMessage(null);
                $em->flush();
                $request->getSession()->getFlashBag()->add('success', "L'observation a bien été modifiée, elle sera ré-étudiée afin d'être validée .");
                return $this->redirectToRoute('view_my_observation', array('page' => 1 ));
            }
            return $this->render('AppBundle:Front:editMyObservation.html.twig', array(
                'observation' => $observation,
                'form' => $form->createView(),
            ));
        }
        else {
            throw new NotFoundHttpException("La page demandée n'existe pas");
        }
    }

    /**
     *
     * @Route("/contact", name="modal_contact")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method({"GET", "POST"})
     *
     */
    public function modalContactAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->get('form.factory')->create(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Affichage d'un message flash
            $request->getSession()->getFlashBag()->add('success', 'Votre message à bien été envoyé !');
            // Sauvegarder en Base de données
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            // Envoyer le mail
            $this->container->get('app.sendEmail')->sendEmailContact($contact);
            // Vider l'objet contact
            $contact = null;
            // Retour à la page d'accueil
            $em = $this->getDoctrine()->getManager();
            $listLastObservations = $em->getRepository('AppBundle:Observation')->findLastObservations(3);

            return $this->render('AppBundle:Front:index.html.twig', array(
                'listLastObservations' => $listLastObservations
            ));
        }
        return $this->render('AppBundle:Front:modalContact.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/sidebarsearch", name="side_bar_search")
     * @Method({"GET"})
     *
     */
    public function sideBarSearchAction()
    {
        return $this->render('AppBundle:Front:sideBarSearch.html.twig');
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @param Request $request
     * @Route("/sidebarnewsletter", name="side_bar_newsletter")
     *
     */
    public function sideBarNewsletterAction(Request $request)
    {
        $emailNewsletter = new EmailNewsletter();
        // On crée le formulaire
        $form = $this->createForm(EmailNewsletterType::class, $emailNewsletter);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Affichage d'un message flash

            $request->getSession()->getFlashBag()->add('success', 'Vous êtes désormais inscrit à notre Newsletter');

            // Sauvegarder en Base de données
            $em = $this->getDoctrine()->getManager();
            $em->persist($emailNewsletter);
            $em->flush();

            // Retour à la page d'accueil
            $em = $this->getDoctrine()->getManager();
            $listLastObservations = $em->getRepository('AppBundle:Observation')->findLastObservations(3);

            return $this->render('AppBundle:Front:index.html.twig', array(
                'listLastObservations' => $listLastObservations,
                'form' => $form->createView()
            ));
        }
        /*return $this->render('AppBundle:Front:sideBarNewsletter.html.twig', array(
            'form' => $form->createView(),
        ));*/
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/sidebarlast", name="side_bar_last")
     * @Method({"GET"})
     *
     */
    public function sideBarLastAction()
    {
        $em = $this->getDoctrine()->getManager();
        $listLastObservations = $em->getRepository('AppBundle:Observation')->findLastObservations(10);
        return $this->render('AppBundle:Front:sideBarLast.html.twig', array(
            'listLastObservations' => $listLastObservations
        ));
    }


}

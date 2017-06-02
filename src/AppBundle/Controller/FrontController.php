<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Contact;
use AppBundle\Entity\EmailNewsletter;
use AppBundle\Form\Type\EmailNewsletterType;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\ContactType;


class FrontController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method({"GET", "POST"})
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
     * @Route("/fonctionnement", name="fonctionnement")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method({"GET"})
     *
     */
    public function fonctionnementAction()
    {
        return $this->render('AppBundle:Front:fonctionnement.html.twig');
    }

    /**
     *
     * @Route("/legalNotice", name="legal_notice")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method({"GET"})
     *
     */
    public function legalNoticeAction()
    {
        return $this->render('AppBundle:Front:legalNotice.html.twig');
    }

    /**
     *
     * @Route("/landing", name="landing")
     * @return \Symfony\Component\HttpFoundation\Response
     * @param Request $request
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

            return $this->redirectToRoute('homepage');
        }
        return $this->render('AppBundle:Front:modalContact.html.twig', array(
            'form' => $form->createView(),
        ));
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

<?php

namespace AppBundle\Controller;



use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Observation;
use AppBundle\Form\Type\ObservationType;
use AppBundle\Form\Type\ObservationRejectType;



class AdminObservationController extends Controller
{
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
            $query = $em->getRepository('AppBundle:Observation')->findObsWithAllStatus(); /* query NOT result */
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
            'status' => $status,
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

}

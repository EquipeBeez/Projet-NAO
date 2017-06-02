<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\SearchSpeciesType;
use AppBundle\Form\Type\SearchObservationType;

class SearchAdminController extends Controller
{


  /**
   *
   * @Route("/admin/searchObservationForm", name="admin_search_observation_form")
   * @return \Symfony\Component\HttpFoundation\Response
   * @Method({"GET"})
   * @Security("has_role('ROLE_USERNAT')")
   *
   */
  public function searchAdminObservationFormAction()
  {
      $form = $this->createForm(SearchObservationType::class);
      return $this->render('AppBundle:Admin:searchObservation.html.twig', array(
          'form' => $form->createView()
      ));
  }

  /**
   *
   * @Route("/admin/searchObservationResult/{page}", name="admin_search_observation_result")
   * @param Request $request
   * @param $page
   * @param null $status
   * @return \Symfony\Component\HttpFoundation\Response
   * @Method({"GET", "POST"})
   * @Security("has_role('ROLE_USERNAT')")
   *
   */
  public function searchAdminObservationResultAction(Request $request, $page, $status = null)
  {
      $term = $request->get('appbundle_search_observation')['fieldsearch'];
      $em = $this->getDoctrine()->getManager();
      $paginator  = $this->get('knp_paginator');
      $pagination = $paginator->paginate(
          $em->getRepository('AppBundle:Observation')->findObservationByLikeWithoutStatus($term), /* query NOT result */
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
   * @Route("/admin/searchSpeciesForm", name="admin_search_species_form")
   * @return \Symfony\Component\HttpFoundation\Response
   * @Method({"GET", "POST"})
   * @Security("has_role('ROLE_USERNAT')")
   *
   */
  public function searchAdminSpeciesFormAction()
  {
      $form = $this->createForm(SearchSpeciesType::class);
      return $this->render('AppBundle:Admin:searchSpecies.html.twig', array(
          'form' => $form->createView()
      ));
  }

  /**
   *
   * @Route("/admin/searchSpeciesResult/{page}", name="admin_search_species_result")
   * @param Request $request
   * @param $page
   * @return \Symfony\Component\HttpFoundation\Response
   * @Method({"GET", "POST"})
   * @Security("has_role('ROLE_USERNAT')")
   *
   */
  public function searchAdminSpeciesResultAction(Request $request, $page)
  {
      $term = $request->get('appbundle_search_species')['fieldsearch'];
      $em = $this->getDoctrine()->getManager();
      $paginator  = $this->get('knp_paginator');
      $pagination = $paginator->paginate(
          $em->getRepository('AppBundle:Taxrefv10')->findSpeciesByLike($term), /* query NOT result */
          $page/*page number*/,
          25/*limit per page*/
      );
      return $this->render('AppBundle:Admin:viewAllSpecies.html.twig', array(
          'pagination' => $pagination,
      ));

  }



}

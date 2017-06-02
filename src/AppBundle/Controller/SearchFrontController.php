<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\SearchSpeciesType;
use AppBundle\Form\Type\SearchObservationType;

class SearchFrontController extends Controller
{


  /**
   *
   * @Route("/searchSpeciesForm", name="search_species_form")
   * @return \Symfony\Component\HttpFoundation\Response
   * @Method({"GET"})
   *
   */
  public function searchSpeciesFormAction()
  {
      $form = $this->createForm(SearchSpeciesType::class);
      return $this->render('AppBundle:Front:searchSpecies.html.twig', array(
          'form' => $form->createView()
      ));
  }

  /**
   *
   * @Route("/searchSpeciesResult/{page}", name="search_species_result")
   * @param Request $request
   * @param $page
   * @return \Symfony\Component\HttpFoundation\Response
   * @Method({"POST"})
   *
   */
  public function searchSpeciesResultAction(Request $request, $page)
  {
      $term = $request->get('appbundle_search_species')['fieldsearch'];
      $em = $this->getDoctrine()->getManager();
      $paginator  = $this->get('knp_paginator');
      $pagination = $paginator->paginate(
          $em->getRepository('AppBundle:Taxrefv10')->findSpeciesByLike($term), /* query NOT result */
          $page/*page number*/,
          25/*limit per page*/
      );
      return $this->render('AppBundle:Front:viewAllSpecies.html.twig', array(
          'pagination' => $pagination,
      ));
  }

  /**
   *
   * @Route("/searchObservationForm", name="search_observation_form")
   * @return \Symfony\Component\HttpFoundation\Response
   * @Method({"GET"})
   *
   */
  public function searchObservationFormAction()
  {
      $form = $this->createForm(SearchObservationType::class);
      return $this->render('AppBundle:Front:searchObservation.html.twig', array(
          'form' => $form->createView()
      ));
  }

  /**
   *
   * @Route("/searchObservationResult/{page}", name="search_observation_result")
   * @param Request $request
   * @param $page
   * @return \Symfony\Component\HttpFoundation\Response
   * @Method({"POST"})
   *
   */
  public function searchObservationResultAction(Request $request, $page)
  {
      $term = $request->get('appbundle_search_observation')['fieldsearch'];
      $em = $this->getDoctrine()->getManager();
      $paginator  = $this->get('knp_paginator');
      $pagination = $paginator->paginate(
          $em->getRepository('AppBundle:Observation')->findObservationByLike($term), /* query NOT result */
          $page/*page number*/,
          25/*limit per page*/
      );
      return $this->render('AppBundle:Front:viewAllObservations.html.twig', array(
          'pagination' => $pagination,
      ));
  }


}

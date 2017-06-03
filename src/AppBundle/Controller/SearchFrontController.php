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
   * @param Request $request
   * @Method({"GET","POST"})
   *
   */
  public function searchSpeciesFormAction(Request $request)
  {
      $form = $this->createForm(SearchSpeciesType::class);
      $form->handleRequest($request);
      if ($form->isSubmitted()){
          $term = $request->get('appbundle_search_species')['fieldsearch'];

          return $this->redirectToRoute('search_species_result', array(
              'term' => $term,
              'page' => 1 ));
      }
      return $this->render('AppBundle:Front:searchSpecies.html.twig', array(
          'form' => $form->createView()
      ));
  }

    /**
     * @Route("/searchSpeciesResult/{term}/{page}", name="search_species_result")
     * @param $term
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method({"GET"})
     *
     */
  public function searchSpeciesResultAction($term, $page)
  {

      $em = $this->getDoctrine()->getManager();
      $paginator  = $this->get('knp_paginator');
      $pagination = $paginator->paginate(
          $em->getRepository('AppBundle:Taxrefv10')->findSpeciesByLike($term), /* query NOT result */
          $page/*page number*/,
          25/*limit per page*/
      );
      return $this->render('AppBundle:Front:viewAllSpecies.html.twig', array(
          'pagination' => $pagination,
          'term' => $term,
      ));
  }

  /**
   *
   * @Route("/searchObservationForm", name="search_observation_form")
   * @return \Symfony\Component\HttpFoundation\Response
   * @param Request $request
   * @Method({"GET","POST"})
   *
   */
  public function searchObservationFormAction(Request $request)
  {
      $form = $this->createForm(SearchObservationType::class);
      $form->handleRequest($request);
      if ($form->isSubmitted()){
          $term = $request->get('appbundle_search_observation')['fieldsearch'];

          return $this->redirectToRoute('search_observation_result', array(
              'term' => $term,
              'page' => 1 ));
      }
      return $this->render('AppBundle:Front:searchObservation.html.twig', array(
          'form' => $form->createView()
      ));

  }

  /**
   *
   * @Route("/searchObservationResult/{term}/{page}", name="search_observation_result")
   * @param $term
   * @param $page
   * @return \Symfony\Component\HttpFoundation\Response
   * @Method({"GET"})
   *
   */
  public function searchObservationResultAction($term, $page)
  {

      $em = $this->getDoctrine()->getManager();
      $paginator  = $this->get('knp_paginator');
      $pagination = $paginator->paginate(
          $em->getRepository('AppBundle:Observation')->findObservationByLike($term), /* query NOT result */
          $page/*page number*/,
          25/*limit per page*/
      );
      return $this->render('AppBundle:Front:viewAllObservations.html.twig', array(
          'pagination' => $pagination,
          'term' => $term,
      ));
  }


}

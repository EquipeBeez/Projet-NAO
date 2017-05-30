<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\SearchSpeciesType;
use AppBundle\Form\SearchObservationType;
use AppBundle\Entity\Taxrefv10;
use AppBundle\Form\ObservationFrontType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchFrontController extends Controller
{


  /**
   *
   * @Route("/searchSpeciesForm", name="search_species_form")
   * @return \Symfony\Component\HttpFoundation\Response
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
      return $this->render('AppBundle:Front:viewAllObservations.html.twig', array(
          'pagination' => $pagination,
      ));
  }


}

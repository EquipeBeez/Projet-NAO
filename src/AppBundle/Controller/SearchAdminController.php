<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\SearchRegisteredType;
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
   * @param Request $request
   * @Method({"GET","POST"})
   * @Security("has_role('ROLE_USERNAT')")
   *
   */
  public function searchAdminObservationFormAction(Request $request)
  {
      $form = $this->createForm(SearchObservationType::class);
      $form->handleRequest($request);
      if ($form->isSubmitted()){
          $term = $request->get('appbundle_search_observation')['fieldsearch'];

          return $this->redirectToRoute('admin_search_observation_result', array(
              'term' => $term,
              'page' => 1 ));
      }
      return $this->render('AppBundle:Admin:searchObservation.html.twig', array(
          'form' => $form->createView()
      ));
  }

  /**
   *
   * @Route("/admin/searchObservationResult/{term}/{page}", name="admin_search_observation_result")
   * @param $term
   * @param $page
   * @param null $status
   * @return \Symfony\Component\HttpFoundation\Response
   * @Method({"GET"})
   * @Security("has_role('ROLE_USERNAT')")
   *
   */
  public function searchAdminObservationResultAction($term, $page, $status = null)
  {
      $em = $this->getDoctrine()->getManager();
      $query = $em->getRepository('AppBundle:Observation')->findObservationByLikeWithoutStatus($term);
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
   * @Route("/admin/searchSpeciesForm", name="admin_search_species_form")
   * @return \Symfony\Component\HttpFoundation\Response
   * @param Request $request
   * @Method({"GET","POST"})
   * @Security("has_role('ROLE_USERNAT')")
   *
   */
  public function searchAdminSpeciesFormAction(Request $request)
  {
      $form = $this->createForm(SearchSpeciesType::class);
      $form->handleRequest($request);
      if ($form->isSubmitted()){
          $term = $request->get('appbundle_search_species')['fieldsearch'];

          return $this->redirectToRoute('admin_search_species_result', array(
              'term' => $term,
              'page' => 1 ));
      }
      return $this->render('AppBundle:Admin:searchSpecies.html.twig', array(
          'form' => $form->createView()
      ));
  }

  /**
   *
   * @Route("/admin/searchSpeciesResult/{term}/{page}", name="admin_search_species_result")
   * @param $term
   * @param $page
   * @return \Symfony\Component\HttpFoundation\Response
   * @Method({"GET"})
   * @Security("has_role('ROLE_USERNAT')")
   *
   */
  public function searchAdminSpeciesResultAction($term, $page)
  {
      $em = $this->getDoctrine()->getManager();
      $paginator  = $this->get('knp_paginator');
      $pagination = $paginator->paginate(
          $em->getRepository('AppBundle:Taxrefv10')->findSpeciesByLike($term), /* query NOT result */
          $page/*page number*/,
          25/*limit per page*/
      );
      return $this->render('AppBundle:Admin:viewAllSpecies.html.twig', array(
          'pagination' => $pagination,
          'term' => $term,
      ));

  }
    /**
     *
     * @Route("/admin/searchRegisteredForm", name="admin_search_registered_form")
     * @return \Symfony\Component\HttpFoundation\Response
     * @param Request $request
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     *
     */
    public function searchAdminRegisteredFormAction(Request $request)
    {
        $form = $this->createForm(SearchRegisteredType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $term = $request->get('appbundle_search_registered')['fieldsearch'];

            return $this->redirectToRoute('admin_search_registered_result', array(
                'term' => $term,
                'page' => 1 ));
        }
        return $this->render('AppBundle:Admin:searchRegistered.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     *
     * @Route("/admin/searchRegisteredResult/{term}/{page}", name="admin_search_registered_result")
     * @param $term
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method({"GET"})
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     *
     */
    public function searchAdminRegisteredResultAction($term, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $em->getRepository('AppBundle:EmailNewsletter')->findRegisteredByLike($term), /* query NOT result */
            $page/*page number*/,
            25/*limit per page*/
        );
        return $this->render('AppBundle:Admin:viewAllRegistered.html.twig', array(
            'pagination' => $pagination,
            'term' => $term,
        ));

    }

}

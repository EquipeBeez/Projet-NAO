<?php

namespace AppBundle\Controller;



use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\ConfigurationType;
use AppBundle\Entity\Taxrefv10;
use AppBundle\Form\Type\Taxrefv10Type;



class AdminController extends Controller
{
    /**
     *
     * @Route("/admin", name="admin")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USERNAT') or has_role('ROLE_MODERATEUR')")
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
            'form' => $form->createView(),
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
        $paginator = $this->get('knp_paginator');
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
            'form' => $form->createView(),
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
            'form' => $form->createView(),
        ));
    }
}

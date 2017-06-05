<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User as User;
use UserBundle\Form\UserType;
use UserBundle\Form\SearchType;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class UserController extends Controller
{

    /**
     *
     * @Route("/admin/users/{page}", name="admin_users")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param $page
     * @Method({"GET"})
     *
     */
    public function usersAction($page)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $userManager->findUsers(), /* query NOT result */
            $page/*page number*/,
            25/*limit per page*/
        );
        return $this->render('UserBundle:User:users.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     *
     * @Route("/admin/deleteuser/{username}", name="admin_delete_user")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @param $user
     * @param Request $request
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     *
     */
    public function deleteUserAction(User $user, Request $request)
    {
        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);
        if ($request->isMethod('POST'))
        {
            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->deleteUser($user);
            $request->getSession()->getFlashBag()->add("success", "L'utilisateur " . $user->getUserName() . " à été supprimé.");
            return $this->redirectToRoute('admin_users', array('page' => 1));
        }
        return $this->render('UserBundle:User:del_user.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     *
     * @Route("/admin/activateuser/{username}", name="admin_activate_user")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param $username
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     * @Method({"GET", "POST"})
     *
     */
    public function activateUserAction($username, Request $request)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);
        if ($user->isEnabled() === true) {
            $user = $this->get('fos_user.util.user_manipulator')->deactivate($username);
            $request->getSession()->getFlashBag()->add('success', 'Utilisateur a bien été désactivé.');
        } else {
            $user = $this->get('fos_user.util.user_manipulator')->activate($username);
            $request->getSession()->getFlashBag()->add('success', 'Utilisateur a bien été activé.');
        }
        return $this->redirectToRoute('admin_users', array('page' => 1));
    }

    /**
     *
     * @Route("/admin/edituser/{id}", name="admin_edit_user")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     * @Method({"GET", "POST"})
     *
     */
    public function editUserAction(User $user, Request $request)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array('id' => $user->getId()));
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $form = $this->get('form.factory')->create(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->updateUser($user);
            $this->container->get('services.insDesNewsletter')->insDesNewsletter($user);
            $request->getSession()->getFlashBag()->add('success', 'Utilisateur a bien été modifié.');
            return $this->redirectToRoute('admin_users', array('page' => 1));
        }
        return $this->render('UserBundle:Admin:edit_user.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     *
     * @Route("/admin/searchuserform", name="admin_search_user_form")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\Response
     * @param Request $request
     * @Method({"GET","POST"})
     *
     */
    public function searchUserFormAction(Request $request)
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $term = $request->get('userbundle_search')['fieldsearch'];

            return $this->redirectToRoute('admin_search_user_result', array(
                'term' => $term,
                'page' => 1 ));
        }
        return $this->render('UserBundle:User:search.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     *
     * @Route("/admin/searchuserresult/{term}/{page}", name="admin_search_user_result")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Request $request
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method({"GET"})
     *
     */
    public function searchUserResultAction($term, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $em->getRepository('UserBundle:User')->findUserByLike($term), /* query NOT result */
            $page/*page number*/,
            25/*limit per page*/
        );
        return $this->render('UserBundle:User:users.html.twig', array(
            'pagination' => $pagination,
        ));
    }

}

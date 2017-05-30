<?php

namespace UserBundle\Controller;

use AppBundle\Entity\EmailNewsletter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User as User;
use UserBundle\Form\AdminEditProfileType;
use UserBundle\Form\UserType;
use UserBundle\Form\SearchType;

class UserController extends Controller
{

    /**
     *
     * @Route("/admin/users/{page}", name="admin_users")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param $page
     *
     */
    public function usersAction($page)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $users = $userManager->findUsers(), /* query NOT result */
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
            $users = $userManager->findUsers();
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
     *
     */
    public function activateUserAction($username, Request $request)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);
        if ($user->isEnabled() == true) {
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
            $em = $this->getDoctrine()->getManager();
            $email = $user->getEmail();
            $userEmail = $em->getRepository('AppBundle:EmailNewsletter')->findByEmail($email);
            // Ajout de l'adresse mail de l'utilisateur dans la liste de la Newsletter
            if ($user->getNewsletter() == true){
                if ($userEmail == null){
                    $emailNewsletter = new EmailNewsletter();
                    $emailNewsletter->setEmail($email);
                    // Salt Random
                    $salt = $this->container->get('app.saltRandom')->randSalt(10);
                    $emailCrypter = md5($salt.'desinscription'.$email);
                    $emailNewsletter->setEmailCrypter($emailCrypter);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($emailNewsletter);
                    $em->flush();
                }
            }
            // Retrait de l'adresse mail de l'utilisateur de la liste de la Newsletter
            else{
                if ($userEmail != null) {
                    foreach ($userEmail as $value) {
                        $em->remove($value);
                    }
                    $em->flush();
                }
            }
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
     *
     */
    public function searchUserFormAction()
    {
        $form = $this->createForm(SearchType::class);
        return $this->render('UserBundle:User:search.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     *
     * @Route("/admin/searchuserresult/{page}", name="admin_search_user_result")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Request $request
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function searchUserResultAction(Request $request, $page)
    {
        $term = $request->get('userbundle_search')['fieldsearch'];
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
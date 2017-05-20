<?php

namespace UserBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User as User;
use UserBundle\Form\AdminEditProfileType;
use UserBundle\Form\UserType;

class UserController extends Controller {

    /**
     * @Route("/admin/users", name="admin_users")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function usersAction()
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $users = $userManager->findUsers();
        return $this->render('UserBundle:User:users.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @Route("/admin/deleteuser/{username}", name="admin_delete_user")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @param $user
     * @param Request $request
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function deleteUserAction(User $user, Request $request)
    {
        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->deleteUser($user);
            $request->getSession()->getFlashBag()->add('success', 'L\'utilisateur '.$user->getUserName().' à été supprimé.');
            $users = $userManager->findUsers();
            return $this->render('UserBundle:User:users.html.twig', array(
                'users' => $users,
            ));
        }
        return $this->render('UserBundle:User:del_user.html.twig', array(
            'user' => $user,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/activateuser/{username}", name="admin_activate_user")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param $username
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function activateUserAction($username, Request $request)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);
        if ($user->isEnabled() == true) {
            $user = $this->get('fos_user.util.user_manipulator')->deactivate($username);
        } else {
            $user = $this->get('fos_user.util.user_manipulator')->activate($username);
        }
        $users = $userManager->findUsers();
        return $this->redirectToRoute('admin_users', array(
            'users' => $users,
        ));
    }

    /**
     * @Route("/admin/edituser/{id}", name="admin_edit_user")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     */
    public function editUserAction(User $user, Request $request)
        {
            $userManager = $this->container->get('fos_user.user_manager');
            $user = $userManager->findUserBy(array('id'=>$user->getId()));
            if (!is_object($user)) {
                throw new AccessDeniedException('This user does not have access to this section.');
            }
            $form = $this->get('form.factory')->create(UserType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $userManager->updateUser($user);
                $request->getSession()->getFlashBag()->add('success', 'Utilisateur a bien été modifié.');
                return $this->redirectToRoute('admin_users');
            }
            return $this->render('UserBundle:Admin:edit_user.html.twig', array(
                'form' => $form->createView()
            ));
        }
}
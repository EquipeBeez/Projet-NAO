<?php
namespace AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadDashboard
{
    private $em;
    private $container;
    private $dashboard = array();
    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }


    public function loadDashboard()
    {
        $this->dashboard = array(
            'member_user' => count($this->em->getRepository('UserBundle:User')->findUserByRoleUser()),
            'member_user_nat' => count($this->em->getRepository('UserBundle:User')->findUserByRole('ROLE_USERNAT')),
            'member_moderator' => count($this->em->getRepository('UserBundle:User')->findUserByRole('ROLE_MODERATEUR')),
            'member_admin' => count($this->em->getRepository('UserBundle:User')->findUserByRole('ROLE_SUPER_ADMIN')),
            'count_observation' => count($this->em->getRepository('AppBundle:Observation')->findAll()),
            'count_observation_valid' => count($this->em->getRepository('AppBundle:Observation')->findby(array('status' => $this->container->getParameter('var_project')['status_obs_valid']))),
            'count_observation_waiting' => count($this->em->getRepository('AppBundle:Observation')->findby(array('status' => $this->container->getParameter('var_project')['status_obs_waiting']))),
            'count_observation_reject' => count($this->em->getRepository('AppBundle:Observation')->findby(array('status' => $this->container->getParameter('var_project')['status_obs_rejeted']))),
            'count_species' => count($this->em->getRepository('AppBundle:Taxrefv10')->findAll()),
        );
        return $this->dashboard;
    }
}

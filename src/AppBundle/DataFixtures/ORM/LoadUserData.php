<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use FOS\UserBundle\Model\GroupableInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Sets the container.
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setEmail('admin@admin.com');
        $user->setUsername('admin');
        $user->setName('Pierre Admino');
        $user->setPlainPassword('admin');
        $user->setEnabled(true);
        $user->addRole('ROLE_SUPER_ADMIN');
        $userManager->updateUser($user);

        $user = $userManager->createUser();
        $user->setEmail('test3@example.com');
        $user->setUsername('Modo');
        $user->setName('Manon Koude');
        $user->setPlainPassword('manon');
        $user->setEnabled(true);
        $user->addRole('ROLE_MODERATEUR');
        $userManager->updateUser($user);

        $user = $userManager->createUser();
        $user->setEmail('test1@example.com');
        $user->setUsername('Jeannot');
        $user->setName('Jean Voye');
        $user->setPlainPassword('jeannot');
        $user->setEnabled(true);
        $user->addRole('ROLE_USERNAT');
        $userManager->updateUser($user);

        $user = $userManager->createUser();
        $user->setEmail('test2@example.com');
        $user->setUsername('Nathalie');
        $user->setName('Nathalie Sync');
        $user->setPlainPassword('nathalie');
        $user->setEnabled(true);
        $user->addRole('ROLE_USER');
        $userManager->updateUser($user);
    }



    /**
     * Get the order of this fixture
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }

}
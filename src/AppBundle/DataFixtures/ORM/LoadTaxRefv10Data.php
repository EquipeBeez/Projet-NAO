<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

class LoadTaxRefV10Data extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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

    public function load(ObjectManager $manager)
    {
        set_time_limit(0);
        // Bundle to manage file and directories
        $finder = new Finder();
        $finder->in('web/sql');
        $finder->name('taxrefv10.sql');

        foreach( $finder as $file ){
            $content = $file->getContents();
            $stmt = $this->container->get('doctrine.orm.entity_manager')->getConnection()->prepare($content);
            $stmt->execute();
        }
    }


    /**
     * Get the order of this fixture
     * @return integer
     */
    public function getOrder()
    {
        return 10;
    }

}
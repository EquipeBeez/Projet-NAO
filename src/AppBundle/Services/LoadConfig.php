<?php

namespace AppBundle\Services;

use AppBundle\Entity\Configuration;
use Doctrine\ORM\EntityManagerInterface;

class LoadConfig
{

    private $em;

    /**
     * @var Configuration
     */
    private $config = null;

    /**
     * ToolsBox constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->loadConfig();
    }

     /**
     * @return Configuration
     */
    public function loadConfig()
    {
        if (!$this->config)
        {
            $this->config = $this->em->getRepository('AppBundle:Configuration')->findOneByName('config1');
        }
        return $this->config;
    }
}
<?php

namespace CollectiveVotingBundle\Factory\VotingProcess;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\Factory\VotingProcessEntityFactoryInterface;
use CollectiveVotingBundle\Model\Entity\CollectiveVotingSubjectInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * VotingProcessFactory
 * ====================
 *
 * @package CollectiveVotingBundle\Factory\VotingProcess
 */
class VotingProcessFactory
{
    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em        = $em;
        $this->container = $container;
    }

    /**
     * Returns a factory from "collectivevoting.factory.entity.*" namespace
     * ====================================================================
     *  * - a class name with namespace for a subject object eg. appbundle.entity.event
     *
     *  Result object should implement a VotingProcessEntityFactoryInterface
     *  and allow to fetch, create and delete a VotingProcess for selected subject object
     *
     * @param object $object
     *
     * @throws \Exception
     * @return VotingProcessEntityFactoryInterface
     */
    public function getEntityProcessFactory($object)
    {
        return $this->getFactoryByName(
            $this->convertClassToPath(get_class($object))
        );
    }

    /**
     * Get by service name eg. "appbundle.entity.event"
     * ================================================
     *
     * @param string $name
     * @throws \Exception
     * @return VotingProcessEntityFactoryInterface
     */
    public function getFactoryByName($name)
    {
        $factory = $this->container->get('collectivevoting.factory.entity.' . $name);

        if (!$factory instanceof VotingProcessEntityFactoryInterface) {
            throw new \Exception('Factory "' . $name . '" should implement VotingProcessEntityFactoryInterface');
        }

        return $factory;
    }

    /**
     * Get factory having as input VotingProcess
     * =========================================
     *
     * @param VotingProcess $votingProcess
     * @throws \Exception
     * @return VotingProcessEntityFactoryInterface
     */
    public function getProcessFactory(VotingProcess $votingProcess)
    {
        return $this->getFactoryByName(
            $this->convertClassToPath($votingProcess->getSubjectType())
        );
    }

    /**
     * @param string $name
     * @return string
     */
    private function convertClassToPath($name)
    {
        return strtolower(
            str_replace('\\', '.', $name)
        );
    }
}
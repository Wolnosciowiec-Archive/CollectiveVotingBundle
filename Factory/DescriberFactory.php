<?php

namespace CollectiveVotingBundle\Factory;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\Describer\DescriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Describer Factory
 * =================
 *   Describers are services that are able
 *   to take subject entity as input
 *   and return proper information like
 *   "title" or "description"
 *
 * @package CollectiveVotingBundle\Factory
 */
class DescriberFactory
{
    /**
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param VotingProcess $vp
     * @return DescriberInterface
     */
    public function getEntityDescriber(VotingProcess $vp)
    {
        return $this->container->get('collectivevoting.describer.' . $vp->getSubjectType(true));
    }
}
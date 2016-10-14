<?php

namespace CollectiveVotingBundle\DecisionMaker;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Factory\VotingProcess\VotingProcessFactory;
use CollectiveVotingBundle\Model\DecisionMaker\DecisionMakerInterface;
use CollectiveVotingBundle\Model\Processor\DecisionProcessorInterface;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\Serializer;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Decision Maker
 * ==============
 *   Decides about Voting Process
 *   if the count of votes is enough or not
 *
 * @package CollectiveVotingBundle\DecisionMaker
 */
class DecisionMaker
{
    const STATE_READY = 'ready';
    const STATE_DECLINED = 'declined';
    const STATE_NOT_READY   = 'not-ready';

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var VotingProcessFactory $factory
     */
    protected $factory;

    /**
     * @var Serializer $serializer
     */
    protected $serializer;

    /**
     * @param EntityManager $em
     * ContainerInterface $container
     * @param VotingProcessFactory $factory
     * @param ContainerInterface $container
     * @param Serializer $serializer
     */
    public function __construct(
        EntityManager $em,
        VotingProcessFactory $factory,
        ContainerInterface $container,
        Serializer $serializer
    )
    {
        $this->em         = $em;
        $this->factory    = $factory;
        $this->container  = $container;
        $this->serializer = $serializer;
    }

    /**
     * Ask a specified implementation
     *
     * @param VotingProcess $process
     * @return bool
     */
    public function couldBeTaken(VotingProcess $process)
    {
        return $this
            ->getDecisionImplementation()
                ->couldBeTaken($process, $this->getVotesCount($process));
    }

    /**
     * Process a decision
     * ==================
     *
     * @param VotingProcess $process
     * @param string $state
     *
     * @return bool
     */
    public function decide(VotingProcess $process, $state)
    {
        $votes = $this->getVotesCount($process);
        $sourceEntity = $this
            ->factory
            ->getProcessFactory($process)
            ->getSourceEntity($process);

        $originalEntityData = $this->em->getUnitOfWork()->getOriginalEntityData($sourceEntity);

        if ($state === self::STATE_READY) {
            $decision = $this
                ->getDecisionProcessor($process)
                ->processDecision($process, $votes, $sourceEntity, $originalEntityData);
        }
        else {
            $decision = $this
                ->getDecisionProcessor($process)
                ->processNotReadyState($process, $votes, $sourceEntity, $state, $originalEntityData);

            if ($decision === DecisionProcessorInterface::DECISION_RESET_PROCESS) {
                $process->resetState();
            }
        }

        // decision processor can modify process state, eg. after detecting changes in
        // entity process could be reset
        $this->em->persist($sourceEntity);
        $this->em->persist($process);
        $this->em->flush($sourceEntity);
        $this->em->flush($process);

        return $decision;
    }

    /**
     * @param VotingProcess $process
     * @return array
     */
    private function getVotesCount(VotingProcess $process)
    {
        return $this
            ->em
            ->getRepository('CollectiveVotingBundle:VotingProcess')
            ->getVotesCount($process);
    }

    /**
     * @param VotingProcess $process
     * @return DecisionProcessorInterface
     */
    private function getDecisionProcessor(VotingProcess $process)
    {
        return $this->container->get('collectivevoting.processor.entity.' . $process->getSubjectType(true));
    }

    /**
     * @return DecisionMakerInterface
     */
    private function getDecisionImplementation()
    {
        return $this->container->get(
            $this->container->getParameter('collectivevoting_decision_strategy')
        );
    }
}
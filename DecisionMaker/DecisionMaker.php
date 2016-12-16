<?php

namespace CollectiveVotingBundle\DecisionMaker;

use CollectiveVotingBundle\Entity\Decision;
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
 *   and which option is finally selected
 *
 * @package CollectiveVotingBundle\DecisionMaker
 */
class DecisionMaker
{
    const STATE_READY     = 'ready';
    const STATE_NOT_READY = 'not-ready';

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
        $this->fillInStrategy($process);

        return $process->getDecisionStrategy()
                ->couldBeTaken($process, $this->getVotesCount($process));
    }

    /**
     * @param VotingProcess $process
     * @return VotingProcess
     */
    private function fillInStrategy(VotingProcess $process)
    {
        if (!$process->getDecisionStrategy() instanceof DecisionMakerInterface) {
            $process->setDecisionStrategy(
                $this->getDecisionImplementation($process)
            );
        }

        return $process;
    }

    /**
     * Process a decision
     * ==================
     *
     * @param VotingProcess $process
     * @param string        $state
     * @param null|mixed    $overrideFinalOption
     *
     * @return Decision
     */
    public function decide(VotingProcess $process, $state, $overrideFinalOption = null)
    {
        $this->fillInStrategy($process);
        $votes        = $this->getVotesCount($process);
        $sourceEntity = $this
            ->factory
            ->getProcessFactory($process)
            ->getSourceEntity($process);

        $originalEntityData = $this
            ->factory
            ->getProcessFactory($process)
            ->getOriginalEntityData($sourceEntity);

        // when the count of votes is enough
        if ($state === self::STATE_READY) {

            $finalOption = $overrideFinalOption;

            // final option eg. "for" or "against" or "black", "by tram" it's a string
            if ($overrideFinalOption === null) {
                $finalOption = $process->getDecisionStrategy()
                    ->getFinalOption($this->getVotesCount($process));
            }

            $decision = $this
                ->getDecisionProcessor($process)
                ->processDecision(
                    $process,
                    $votes,
                    $sourceEntity,
                    $originalEntityData,
                    $finalOption
                );

            return new Decision($decision, $finalOption);
        }

        // if the count of votes is not enough
        // then the process is NOT YET READY to take action
        $decision = $this
            ->getDecisionProcessor($process)
            ->processNotReadyState(
                $process,
                $votes,
                $sourceEntity,
                $state,
                $originalEntityData
            );

        return new Decision($decision, null);
    }

    /**
     * @param VotingProcess $process
     * @return array
     */
    private function getVotesCount(VotingProcess $process)
    {
        return $process->getVotesCount();
    }

    /**
     * @param VotingProcess $process
     * @return DecisionProcessorInterface
     */
    private function getDecisionProcessor(VotingProcess $process)
    {
        return $this->container->get('collectivevoting.processor.' . $process->getSubjectType(true));
    }

    /**
     * Constructs the decision strategy implementation
     * basing in first priority on decisionStrategyName field in VotingProcess
     * and in the second its checking container parameter "collectivevoting_decision_strategy"
     *
     * @param VotingProcess $process
     * @return DecisionMakerInterface
     */
    private function getDecisionImplementation(VotingProcess $process)
    {
        return $this->container->get(
            $process->getDecisionStrategyName()
                ? $process->getDecisionStrategyName()
                : $this->container->getParameter('collectivevoting_decision_strategy')
        );
    }
}
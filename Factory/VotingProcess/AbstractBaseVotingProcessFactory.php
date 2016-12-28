<?php

namespace CollectiveVotingBundle\Factory\VotingProcess;

use CollectiveVotingBundle\DecisionMaker\Strategy\ChainedStrategy;
use CollectiveVotingBundle\DecisionMaker\Strategy\MinimumVotesStrategy;
use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\DecisionMaker\DecisionMakerInterface;
use CollectiveVotingBundle\Model\Entity\CollectiveVotingSubjectInterface;
use CollectiveVotingBundle\Model\Entity\VotingParticipantInterface;
use CollectiveVotingBundle\Model\Factory\VotingProcessEntityFactoryInterface;
use Doctrine\ORM\EntityManager;

/**
 * AbstractBaseVotingProcessFactory
 * ================================
 *
 * @package CollectiveVotingBundle\Factory\VotingProcess
 */
class AbstractBaseVotingProcessFactory implements VotingProcessEntityFactoryInterface
{
    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get source entity that is a subject in Voting Process
     * =====================================================
     *
     * @param VotingProcess $vp
     * @return CollectiveVotingSubjectInterface|null
     */
    public function getSourceEntity(VotingProcess $vp)
    {
        $entity = $this
            ->em
            ->getRepository($vp->getSubjectType())
            ->find($vp->getSubjectId());

        if (!$entity instanceof CollectiveVotingSubjectInterface) {
            throw new \LogicException('Invalid source entity returned, this does not sound good', self::EXCEPTION_SOURCE_ENTITY_NO_LONGER_AVAILABLE);
        }

        return $entity;
    }

    /**
     * @param object $entity
     * @return array
     */
    public function getOriginalEntityData($entity): array
    {
        return $this->em->getUnitOfWork()->getOriginalEntityData($entity);
    }

    /**
     * @param CollectiveVotingSubjectInterface $object
     * @return VotingProcess|null
     */
    public function constructProcess(CollectiveVotingSubjectInterface $object)
    {
        dump([
            'subjectId'   => $object->getId(),
            'subjectType' => get_class($object),
        ]);

        return $this->em->getRepository(VotingProcess::class)->findOneBy([
            'subjectId'   => $object->getId(),
            'subjectType' => get_class($object),
        ]);
    }

    /**
     * Return a strategy for this voting process type
     *
     * @return DecisionMakerInterface
     */
    public function constructStrategy(): DecisionMakerInterface
    {
        $strategy = new ChainedStrategy();

        $strategy->addStrategy(
            new MinimumVotesStrategy(1)
        );

        return $strategy;
    }

    /**
     * Create a new process
     *
     * @param CollectiveVotingSubjectInterface $object
     * @param VotingParticipantInterface $user
     * @param bool $persist
     *
     * @return VotingProcess|null
     */
    public function createNewProcess(
        CollectiveVotingSubjectInterface $object,
        VotingParticipantInterface $user,
        $persist = false
    )
    {
        dump('creation');
        dump(get_class($object), $object->getId());

        $process = new VotingProcess();
        $process->setSubjectType(get_class($object));
        $process->setSubjectId($object->getId());
        $process->setStartedByUser($user);
        $process->setDateAdded(new \DateTime('now'));

        if ($persist) {
            $this->em->persist($process);
            $this->em->flush($process);
        }

        return $process;
    }
}
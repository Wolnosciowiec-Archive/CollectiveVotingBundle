<?php

namespace CollectiveVotingBundle\Factory\VotingProcess;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\Entity\CollectiveVotingSubjectInterface;
use CollectiveVotingBundle\Model\Factory\VotingProcessEntityFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
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
     * @param CollectiveVotingSubjectInterface $object
     * @return VotingProcess|null
     */
    public function constructProcess(CollectiveVotingSubjectInterface $object)
    {
        return $this->em->getRepository('CollectiveVotingBundle:VotingProcess')->findOneBy([
            'subjectId'   => $object->getId(),
            'subjectType' => get_class($object),
        ]);
    }

    /**
     * Create a new process
     *
     * @param CollectiveVotingSubjectInterface $object
     * @param UserInterface $user
     * @param bool $persist
     *
     * @return VotingProcess|null
     */
    public function createProcess(CollectiveVotingSubjectInterface $object, UserInterface $user, $persist = false)
    {
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
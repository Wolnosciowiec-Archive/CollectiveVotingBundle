<?php

namespace CollectiveVotingBundle\Tests\Data;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Factory\VotingProcess\AbstractBaseVotingProcessFactory;
use CollectiveVotingBundle\Model\Entity\Tests\Subject;
use CollectiveVotingBundle\Model\Factory\VotingProcessEntityFactoryInterface;

/**
 * @package CollectiveVotingBundle\Tests\Data
 */
class SubjectFactory extends AbstractBaseVotingProcessFactory
    implements VotingProcessEntityFactoryInterface
{
    /**
     * @var array $subjects
     */
    private $subjects = [];

    /**
     * @param VotingProcess $vp
     * @return Subject
     */
    public function getSourceEntity(VotingProcess $vp)
    {
        if (!isset($this->subjects[$vp->getId()]))
        {
            $this->subjects[$vp->getId()] = (new Subject())->setId($vp->getSubjectId());
        }

        return $this->subjects[$vp->getId()];
    }
}
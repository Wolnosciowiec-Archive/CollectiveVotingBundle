<?php

namespace CollectiveVotingBundle\Tests\Data;

use CollectiveVotingBundle\Model\Describer\DescriberInterface;
use CollectiveVotingBundle\Model\Entity\CollectiveVotingSubjectInterface;

/**
 * @package CollectiveVotingBundle\Tests\Data
 */
class SubjectDescriber implements DescriberInterface
{
    /**
     * @param CollectiveVotingSubjectInterface $subject
     * @return array
     */
    public function getRouteAndParams(CollectiveVotingSubjectInterface $subject)
    {
        return [
            'route'  => 'homepage',
            'params' => [
                'subjectId' => $subject->getId(),
            ],
        ];
    }

    /**
     * @param CollectiveVotingSubjectInterface $subject
     * @return array
     */
    public function getPermissionParams(CollectiveVotingSubjectInterface $subject)
    {
        return [
            'permission' => 'manageSubject',
            'subject'    => $subject,
        ];
    }

    /**
     * @param CollectiveVotingSubjectInterface $subject
     * @return string
     */
    public function getTitle(CollectiveVotingSubjectInterface $subject)
    {
        return 'Test title';
    }

    /**
     * @param CollectiveVotingSubjectInterface $subject
     * @return string
     */
    public function getDescription(CollectiveVotingSubjectInterface $subject)
    {
        return 'This is a test';
    }
}
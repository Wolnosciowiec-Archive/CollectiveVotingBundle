<?php

namespace CollectiveVotingBundle\Model\Entity;

/**
 * CollectiveVotingSubjectInterface
 * ================================
 *  An entity must implement this interface
 *  to be able to be used as subject in
 *  collective voting process
 *
 * @package CollectiveVotingBundle\Model\Entity
 */
interface CollectiveVotingSubjectInterface
{
    /**
     * @return string|int
     */
    public function getId();

    /**
     * @return bool
     */
    public function isVotingSatisfied();
}
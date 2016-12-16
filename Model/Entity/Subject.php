<?php

namespace CollectiveVotingBundle\Model\Entity\Tests;

use CollectiveVotingBundle\Model\Entity\CollectiveVotingSubjectInterface;

class Subject implements CollectiveVotingSubjectInterface
{
    /**
     * @var bool $approved
     */
    protected $approved;

    /**
     * @var string $id
     */
    protected $id;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    public function isVotingSatisfied()
    {
        return $this->isApproved();
    }

    /**
     * @param boolean $approved
     * @return Subject
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param string $id
     * @return Subject
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
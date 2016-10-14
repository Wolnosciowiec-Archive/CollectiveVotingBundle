<?php

namespace CollectiveVotingBundle\Repository;

use CollectiveVotingBundle\Entity\VotingProcess;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use PaginatorBundle\Repository\PaginatedRepository;

/**
 * VotingProcessRepository
 * =======================
 *
 * @package CollectiveVotingBundle\Repository
 */
class VotingProcessRepository extends EntityRepository
{
    use PaginatedRepository;

    /**
     * Get votes count for a Voting Process
     * ====================================
     *
     *   Returns an example array:
     *     votes_for: 6
     *     votes_against: 5
     *
     *   For non-boolean values:
     *     swimming: 4
     *     walk: 3
     *     flying_on_a_broom: 6
     *
     * @param VotingProcess $votingProcess
     * @return array
     */
    public function getVotesCount(VotingProcess $votingProcess)
    {
        $query = $this->_em->createQuery('
            SELECT vote.voteOption as option
            FROM CollectiveVotingBundle\Entity\Vote vote
            WHERE vote.votingProcess = :votingProcess'
        )->setParameter('votingProcess', $votingProcess);


        // name boolean votes
        $defaults = [
            1 => 'votes_for',
            0 => 'votes_against',
        ];

        $votes = [];

        foreach ($query->getResult(Query::HYDRATE_ARRAY) as $result) {
            $optionName = isset($defaults[$result]) ? $defaults[$result] : $result;
            $votes[$optionName]++;
        }

        return $votes;
    }

    /**
     * Get count of processes in OPEN or CLOSED state
     * ==============================================
     *
     * @param bool $open
     * @return int
     */
    public function getProcessesCount($open)
    {
        $qb = $this->createQueryBuilder('vp');

        $qb->select('COUNT(vp)');
        $qb->where('vp.state IN (:state)');

        if ($open === true) {
            $qb->setParameter('state', [VotingProcess::STATE_OPEN]);
        }
        else {
            $qb->setParameter('state', [
                VotingProcess::STATE_CLOSED_APPROVED,
                VotingProcess::STATE_CLOSED_DECLINED,
                VotingProcess::STATE_CLOSED_CLOSED,
            ]);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}
<?php

namespace CollectiveVotingBundle\Model\Describer;
use CollectiveVotingBundle\Model\Entity\CollectiveVotingSubjectInterface;

/**
 * Describer Interface
 * ===================
 *   Describes an entity
 *
 * @package CollectiveVotingBundle\Model\Describer
 */
interface DescriberInterface
{
    /**
     * Get entity title
     * ================
     *
     * @param CollectiveVotingSubjectInterface $subject
     * @return string
     */
    public function getTitle(CollectiveVotingSubjectInterface $subject);

    /**
     * Get entity description
     * ======================
     *
     * @param CollectiveVotingSubjectInterface $subject
     * @return string
     */
    public function getDescription(CollectiveVotingSubjectInterface $subject);

    /**
     * Get route name and default parameters to apply
     * ==============================================
     *   Used to generate links on page
     *   that will lead directly to the
     *   edit page of selected  element
     *
     * @param CollectiveVotingSubjectInterface $subject
     * @return array
     */
    public function getRouteAndParams(CollectiveVotingSubjectInterface $subject);

    /**
     * Get parameters to pass to the decision maker
     * of the security layer (Voters)
     * ============================================
     *
     *    permission: managePostWall
     *    subject: $subject
     *
     * @param CollectiveVotingSubjectInterface $subject
     * @return array
     */
    public function getPermissionParams(CollectiveVotingSubjectInterface $subject);
}
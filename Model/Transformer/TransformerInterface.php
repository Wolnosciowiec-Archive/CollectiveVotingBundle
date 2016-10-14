<?php

namespace CollectiveVotingBundle\Model\Transformer;

use Symfony\Component\HttpFoundation\Request;

/**
 * Transformer Interface
 * =====================
 *
 * @package CollectiveVotingBundle\Model\Formatter
 */
interface TransformerInterface
{
    /**
     * @param mixed $data
     * @param Request $request
     *
     * @return array
     */
    public function transformToArray($data, Request $request);
}
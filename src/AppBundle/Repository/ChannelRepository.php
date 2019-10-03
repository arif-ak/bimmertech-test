<?php

namespace AppBundle\Repository;

use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository as BaseChannelRepository;
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * Class ChannelRepository
 * @package AppBundle\Repository
 */
class ChannelRepository extends BaseChannelRepository
{
    /**
     * {@inheritdoc}
     */
    public function findOneByCode(string $code): ?ChannelInterface
    {
        return parent::findOneByCode($code);
    }
}
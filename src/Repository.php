<?php

namespace Drupal\ParseComposer;

use Composer\Repository\VcsRepository;
use Composer\EventDispatcher\EventDispatcher;
use Composer\IO\IOInterface;
use Composer\Config;

/**
 * Drupal.org specific Repository.
 */
class Repository extends VcsRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        array $repoConfig,
        IOInterface $io,
        Config $config,
        EventDispatcher $dispatcher = null,
        array $drivers = null
    )
    {
        $drivers = array('git' => 'Drupal\ParseComposer\GitDriver');
        $repoConfig['type'] = 'git';
        parent::__construct($repoConfig, $io, $config, $dispatcher, $drivers);
        $parts = preg_split('{[/:]}', $this->url);
        $last = end($parts);
        $this->repoConfig['drupalProjectName'] = current(explode('.', $last));
    }

    /**
     * Create repository out of existing repository information.
     *
     * @param VcsRepository $repository
     *
     * @return Repository
     */
    public static function create(VcsRepository $repository)
    {
        return new static(
            $repository->repoConfig,
            $repository->io,
            $repository->config
        );
    }

    /**
     * {@inheritdoc}
     */
    public function hadInvalidBranches()
    {
        return false;
    }
}

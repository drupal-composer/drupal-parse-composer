<?php

namespace Drupal\ParseComposer;

/**
 * Drupal project.
 */
class Project
{

    private $makeFiles  = [];
    private $infoFiles  = [];
    private $isTheme    = false;
    private $hasDrush   = false;

    /**
     * @param string              $name
     * @param FileFinderInterface $finder
     * @param string              $core
     * @param array               $releases
     */
    public function __construct(
        $name,
        FileFinderInterface $finder,
        $core,
        array $releases = array()
    )
    {
        $this->name     = $name;
        $this->finder   = $finder;
        $this->core     = $core;
        $this->releaseFactory = new ReleaseInfoFactory;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get composer information for Drupal project.
     *
     * @return array|null
     */
    public function getDrupalInformation()
    {
        $projectMap = $composerMap = $make = array();
        $this->finder->pathMatch($this->getPathMatcher());
        if (empty($this->projectMap) && !$this->hasDrush) {
            return;
        }
        if ('drupal' === $this->name) {
            $projectMap['drupal'] = clone($projectMap['system']);
        }
        foreach ($this->projectMap as $name => $info) {
            $composerMap[$name] = $info;
        }
        $top = isset($composerMap[$this->name])
          ? $this->name
          : current(array_keys($composerMap));
        foreach ($this->makeFiles as $makefile) {
            foreach (($makefile->getDrupalProjects()) as $name => $project) {
                $composerMap[$top]['require']['drupal/'.$name] = $makefile->getConstraint($name);
            }
        }
        if (!isset($composerMap[$top]['name'])) {
            $composerMap[$top]['name'] = $this->getName();
        }
        $composerMap[$top]['type'] = $this->getProjectType($composerMap[$top]);
        if ($composerMap[$top]['type'] === 'drupal-drush') {
            $composerMap[$top]['require']['drush/drush'] = '>=6';
        }

        return $composerMap;
    }

    /**
     * @return a function for use with FileFinderInterface::pathMatch()
     */
    private function getPathMatcher()
    {
        return function($path) {
            if (strpos($path, 'test') !== false) {
                return false;
            }
            $parts = explode('.', basename($path));
            if (end($parts) === 'info'
                || array_slice($parts, -2) == ['info', 'yml']
            ) {
                $info = new InfoFile(
                    basename($path),
                    $this->finder->fileContents($path),
                    $this->core
                );
                $this->projectMap[$info->getProjectName()] = $info->packageInfo();
            } elseif (end($parts) === 'make') {
                $this->makeFiles[] = new Makefile(
                    $this->finder->fileContents($path)
                );
            } elseif (end($parts) === 'module') {
                $this->hasModule = true;
            } elseif (basename($path) === 'template.php') {
                $this->isTheme = true;
            } elseif (array_slice($parts, -2) == ['drush', 'inc']
            ) {
                $this->hasDrush = true;
            }
        };
    }

    /**
     * @return the type for the composer project
     */
    private function getProjectType()
    {
        if ('drupal' === $this->name) {
            return 'drupal-core';
        } elseif ($releaseInfo = $this->getReleaseInfo($this->core)) {
            $type = $releaseInfo->getProjectType();
            if ($type === 'drupal-module'
                && !$this->hasModule && !$this->isTheme && $this->hasDrush
            ) {
                $type = 'drupal-drush';
            }

            return $type;
        }
    }

    /**
     * Get release information for current project in given core version.
     *
     * @param string $core
     *
     * @return ReleaseInfo
     */
    public function getReleaseInfo($core)
    {
        return $this->releaseFactory->getReleasesForCore($this->name, $core);
    }
}

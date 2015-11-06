<?php

namespace Drupal\ParseComposer;

/**
 * Drupal project.
 */
class Project
{

    private $makeFiles = [];
    private $infoFiles = [];
    private $isTheme;
    private $hasDrush;

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
     * @return array|void
     */
    public function getDrupalInformation()
    {
        $projectMap = $composerMap = $make = array();
        $this->hasDrush = $this->hasModule = false;
        $this->finder->pathMatch(
            function($path) {
                if (strpos($path, 'test') !== false) {
                    return false;
                }
                $parts = explode('.', basename($path));
                if (($this->core == 7 && end($parts) === 'info')
                  || ($this->core == 8 && array_slice($parts, -2) == ['info', 'yml'])
                ) {
                    $this->infoFiles[] = $path;

                    return true;
                } elseif (end($parts) === 'make') {
                    $this->makeFiles[] = $path;

                    return true;
                } elseif (end($parts) === 'module'
                ) {
                    $this->hasModule = true;
                } elseif (basename($path) === 'template.php') {
                    $this->isTheme = true;
                } elseif (array_slice($parts, -2) == ['drush', 'inc']
                ) {
                    $this->hasDrush = true;
                }
            }
        );
        foreach ($this->infoFiles as $infoPath) {
            $info = new InfoFile(
                basename($infoPath),
                $this->finder->fileContents($infoPath),
                $this->core
            );
            $projectMap[$info->getProjectName()] = $info;
        }
        foreach ($this->makeFiles as $makePath) {
            $make[] = new Makefile(
                $this->finder->fileContents($makePath)
            );
        }
        if (empty($projectMap) && !$this->hasDrush) {
            return;
        }
        if ('drupal' === $this->name) {
            $projectMap['drupal'] = clone($projectMap['system']);
        }
        foreach ($projectMap as $name => $info) {
            $composerMap[$name] = $info->packageInfo();
            foreach ($make as $makefile) {
                foreach (($makefile->getDrupalProjects()) as $name => $project) {
                    $composerMap[$this->name]['require']['drupal/'.$name] = $makefile->getConstraint($name);
                }
            }
        }
        if (empty($composerMap)) {
            return $composerMap;
        }
        $top = isset($composerMap[$this->name])
          ? $this->name
          : current(array_keys($composerMap));
        $info = isset($projectMap[$top]) ? $projectMap[$top] : null;
        if ('drupal' === $this->name) {
            $composerMap[$top]['type'] = 'drupal-core';
        }
        if (empty($composerMap[$top]['type']) && $this->core == 8 && isset($info) && isset($info->drupalInfo()['type'])) {
            $composerMap[$top]['type'] = 'drupal-' . $info->drupalInfo()['type'];
        }
        if (empty($composerMap[$top]['type']) && $releaseInfo = $this->getReleaseInfo($this->core)) {
            $composerMap[$top]['type'] = $releaseInfo->getProjectType();
            if ($composerMap[$top]['type'] === 'drupal-module'
                && !$this->hasModule && !$this->isTheme && $this->hasDrush
            ) {
                if (!isset($composerMap[$top]['name'])) {
                    $composerMap[$top]['name'] = $this->getName();
                }
                $composerMap[$top]['type'] = 'drupal-drush';
                $composerMap[$top]['require']['drush/drush'] = '>=6';
            }
        }

        return $composerMap;
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

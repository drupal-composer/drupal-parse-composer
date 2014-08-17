<?php

namespace Drupal\ParseComposer;

class Project
{

    private $makeFiles = [];
    private $infoFiles = [];

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

    public function getName()
    {
        return $this->name;
    }

    public function getDrupalInformation()
    {
        $projectMap = $make = array();
        $this->hasDrush = $this->hasModule = false;
        $this->finder->pathMatch(
            function($path) {
                if (strpos($path, 'test') !== false) {
                    return false;
                }
                $parts = explode('.', basename($path));
                if (
                    end($parts) === 'info'
                    || array_slice($parts, -2) == ['info', 'yml']
                ) {
                    $this->infoFiles[] = $path;
                    return true;
                }
                elseif (end($parts) === 'make') {
                    $this->makeFiles[] = $path;
                    return true;
                }
                elseif (
                    end($parts) === 'module'
                ) {
                    $this->hasModule = true;
                }
                elseif (basename($path) === 'template.php') {
                    $this->isTheme = true;
                }
                elseif (
                    array_slice($parts, -2) == ['drush', 'inc']
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
        if ('drupal' === $this->name) {
            $composerMap[$top]['type'] = 'drupal-core';
        }
        elseif ($releaseInfo = $this->getReleaseInfo($this->core)) {
            $top = isset($composerMap[$this->name])
                ? $this->name
                : current(array_keys($composerMap));
            $composerMap[$top]['type'] = $releaseInfo->getProjectType();
            if (
                $composerMap[$top]['type'] === 'drupal-module'
                && !$this->hasModule && !$this->isTheme && $this->hasDrush
            )
            {
                $composerMap[$top]['type'] = 'drupal-drush';
                $composerMap[$top]['require']['drush/drush'] = '6.*';
            }
        }
        return $composerMap;
    }

    public function getReleaseInfo($core)
    {
        return $this->releaseFactory->getReleasesForCore($this->name, $core);
    }
}

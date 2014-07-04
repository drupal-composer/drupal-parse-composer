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
        $this->releases = $releases;
        $this->core     = $core;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDrupalInformation()
    {
        $projectMap = $projectNames = $paths = $make = array();
        $this->hasDrush = $this->hasModule = false;
        $paths = $this->finder->pathMatch(
            function($path) {
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
            $make[$projectName] = new Makefile(
                $this->finder->fileContents($makePath)
            );
        }
        if (empty($projectMap) && !$this->hasDrush) {
            return;
        }
        if ('drupal' == $this->name) {
            $projectMap['drupal'] = clone($projectMap['system']);
        }
        foreach ($projectMap as $name => $info) {
            $composerMap[$name] = $info->packageInfo();
            foreach ($make as $makefile) {
                foreach (($makefile->getMakeInfo('projects') ?: []) as $name => $project) {
                    $composerMap[$this->name]['require']['drupal/'.$name] = $makefile->getConstraint($name);
                }
            }
        }
        if ($releaseInfo = $this->getReleaseInfo($this->core)) {
            if (!$this->hasModule && $this->hasDrush) {
                $composerMap[$this->name]['type'] = 'drupal-drush';
                    $composerMap[$this->name]['require']['drush/drush'] = '6.*';
            }
            else {
                $composerMap[$this->name]['type'] = $releaseInfo->getProjectType();
            }
        }
        return $composerMap;
    }

    public function getReleaseInfo($core)
    {
        if (($core > 6) && ($this->name !== 'drupal')) {
            if (!isset($this->releases[$core])) {
                $this->releases[$core] = new ReleaseInfo($this->name, $core);
            }
            return $this->releases[$core];
        }
    }
}

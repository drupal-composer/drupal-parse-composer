<?php

namespace Drupal\ParseComposer;

class Project
{

    public function __construct($name, FileFinderInterface $finder)
    {
        $this->name = $name;
        $this->finder = $finder;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDrupalInformation()
    {
        $projectMap = $projectNames = $paths = $make = array();
        $paths = $this->finder->pathMatch(
            function($path) {
                $parts = explode('.', basename($path));
                return in_array(end($parts), ['info', 'make']);
            }
        );
        foreach ($paths as $path) {
            $parts = explode('.', $path);
            $projectName = @current(explode('.', end(explode('/', $path))));
            if (end($parts) === 'info' && !strpos($projectName, 'test')) {
                $projectMap[$projectName] = new InfoFile(
                    $projectName,
                    $this->finder->fileContents($path)
                );
            }
            if (end($parts) === 'make') {
                $make[$projectName] = new Makefile(
                    $this->finder->fileContents($path)
                );
            }
        }
        if (empty($projectMap)) {
            return;
        }
        if ('drupal' == $this->name) {
            $projectMap['drupal'] = clone($projectMap['system']);
        }
        foreach ($projectMap as $name => $info) {
            $composerMap[$name] = $info->packageInfo();
            foreach ($make as $makefile) {
                foreach ($makefile->getMakeInfo('projects') as $name => $project) {
                    $composerMap[$this->name]['require']['drupal/'.$name] = $makefile->getConstraint($name);
                }
            }
        }
        if (
            $releaseInfo = $this->getReleaseInfo(
                $projectMap[$this->name]->drupalInfo()['core'][0]
            )
        ) {
            $composerMap[$this->name]['type'] = $releaseInfo->getProjectType();
            $composerMap[$this->name]['require']['composer/installers'] = '~1.0';
        }
        return $composerMap;
    }

    public function getReleaseInfo($core)
    {
        if (($core  > 6) && ($this->name !== 'drupal')) {
            return new ReleaseInfo($this->name, $core);
        }
    }
}

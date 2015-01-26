<?php

namespace Drupal\ParseComposer;

/**
 * Representation of a drush make file.
 */
class Makefile
{

    /**
     * @param string $data Content from makefile.
     */
    public function __construct($data)
    {
        $this->makeInfo = \drupal_parse_info_format($data);
    }

    /**
     * Get specific nested information from the makefile.
     *
     * @param string[] $path Array of keys to retrieve value from.
     *
     * @return array|bool
     */
    public function getMakeInfo($path = array())
    {
        $path = is_array($path) ? $path : func_get_args();
        $info = $this->makeInfo;
        foreach ($path as $key) {
            if (isset($info[$key])) {
                $info = $info[$key];
            } else {
                return false;
            }
        }

        return $info;
    }

    /**
     * Get list of drupal project informated defined in the makefile.
     *
     * @return array
     */
    public function getDrupalProjects()
    {
        $drupalProjects = [];
        foreach ($this->getMakeInfo('projects') ?: [] as $projectName => $project) {
            $url = $this->getMakeInfo(
                ['projects', $projectName, 'download', 'url']
            );
            if ($url
                && strpos(parse_url($url, PHP_URL_HOST), 'drupal.org') !== false
            ) {
                $drupalProjects[$projectName] = $project;
            }
        }

        return $drupalProjects;
    }

    /**
     * Retrieve version of given project from makefile.
     *
     * @param string $project Project defined in makefile.
     *
     * @return bool|AbstractVersion|string
     *
     * @todo Specify return type
     */
    public function getVersion($project)
    {
        return $this->getVersionFromPath(
            array('projects', $project, 'version')
        );
    }

    /**
     * Retrieve version information for project downloaded by vcs tag.
     *
     * @param string $project Project defined in makefile.
     *
     * @return bool|AbstractVersion|string
     *
     * @todo Specify return type
     */
    public function getVersionFromTag($project)
    {
        return $this->getVersionFromPath(
            array('projects', $project, 'download', 'tag')
        );
    }

    /**
     * Get version information from project downloaded by VCS branch.
     *
     * @param string $project Project defined in makefile.
     *
     * @return bool|string
     *
     * @todo Specify return type
     */
    public function getVersionFromBranch($project)
    {
        if ($branch = $this->getMakeInfo(
            array('projects', $project, 'download', 'branch')
        )) {
            return "dev-$branch";
        }

        return false;
    }

    /**
     * Build constraint from core version.
     *
     * @return string
     */
    public function coreConstraint()
    {
        return $this->makeInfo['core'][0].'.*';
    }

    /**
     * Build version constraint for project.
     *
     * @param string $project Project defined in the makefile.
     *
     * @return bool|AbstractVersion|string
     *
     * @todo Specify return type
     */
    public function getConstraint($project)
    {
        switch (true) {
            case ($constraint = $this->getVersion($project)):
            case ($constraint = $this->getVersionFromTag($project)):
            case ($constraint = $this->getVersionFromBranch($project)):
            case ($constraint = $this->coreConstraint()):
            default:
                return $constraint;
        }
    }

    /**
     * Retrieve version information from nested makefile value.
     *
     * @param array $path Nested keys to retrieve value from.
     *
     * @return bool|AbstractVersion|string
     *
     * @todo Specify return type
     */
    private function getVersionFromPath(array $path)
    {
        if ($versionString = $this->getMakeInfo($path)) {
            return $this->makeVersion($versionString, $path[1]);
        }

        return false;
    }

    /**
     * @param string $versionString
     * @param string $name
     * @return AbstractVersion|string
     *
     * @todo: Better description.
     * @todo Specify return type
     */
    private function makeVersion($versionString, $name)
    {
        $versionFactory = new VersionFactory();
        $version = $versionFactory->create(
            [$this->makeInfo['core'][0], $versionString],
            ($name == 'drupal')
        );

        return $version ? $version->getSemVer() : $version;
    }
}

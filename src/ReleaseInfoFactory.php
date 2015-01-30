<?php

namespace Drupal\ParseComposer;

/**
 * Factory for building release information for core or project.
 */
class ReleaseInfoFactory
{
    private $releases = [
        '4'   => [],
        '4.7' => [],
        '5'   => [],
        '6'   => [],
        '7'   => [],
        '8'   => [],
        '9'   => [],
    ];

    /**
     * Helper to convert composer name to drupal project name.
     *
     * @param string $name
     *
     * @return string
     */
    private function drupalizeName($name)
    {
        if (strpos($name, '/')) {
            list(, $name) = explode('/', $name);
        }

        return $name;
    }

    /**
     * Retrieve release information for project in given core.
     *
     * @param string $name
     * @param string $core
     *
     * @return ReleaseInfo
     */
    public function getReleasesForCore($name, $core)
    {
        $core = "$core";
        $name = $this->drupalizeName($name);
        if (isset($this->releases[$core])) {
            if (!isset($this->releases[$core][$name])) {
                $release = new ReleaseInfo(
                    $name,
                    $core
                );
                $this->releases[$core][$name] = $release->exists()
                    ? $release
                    : false;
            }

            return $this->releases[$core][$name];
        }

        return false;
    }

    /**
     * Retrieve release information for project in multiple cores.
     *
     * @param string $name  Drupal project name
     * @param array  $cores Array of core versions, defaults to all verions.
     *
     * @return ReleaseInfo[string]
     */
    public function getReleaseInfo($name, array $cores = [])
    {
        $info   = [];
        $cores  = empty($cores) ? array_keys($this->releases) : $cores;
        $name = $this->drupalizeName($name);
        foreach ($cores as $core) {
            if ($releaseInfo = $this->getReleasesForCore($name, $core)) {
                $info[$core] = $releaseInfo;
            }
        }

        return $info;
    }
}

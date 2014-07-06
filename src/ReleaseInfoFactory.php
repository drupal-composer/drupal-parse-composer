<?php

namespace Drupal\ParseComposer;

class ReleaseInfoFactory{

    private $releases = [
        '4'   => [],
        '4.7' => [],
        '5'   => [],
        '6'   => [],
        '7'   => [],
        '8'   => [],
        '9'   => [],
    ];

    public function getReleasesForCore($name, $core)
    {
        if (isset($this->releases[$core])) {
            if (!isset($this->releases[$core][$name])) {
                $release = new ReleaseInfo(
                    $this->name,
                    $core
                );
                $this->releases[$core][$name] = $release->exists()
                    ? $release
                    : false;
            }
            return $this->releases[$core][$name];
        }
    }

    public function getReleaseInfo($name, array $cores = [])
    {
        $info   = [];
        $cores  = empty($cores) ? array_keys($this->releases) : $cores;
        foreach ($cores as $core) {
            if ($releaseInfo = $this->getReleasesForCore($name, $core)) {
                $info[$core] = $releaseInfo;
            }
        }
        return $info;
    }
}

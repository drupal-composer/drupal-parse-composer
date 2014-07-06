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

    private function drupalizeName($name) {
        if (strpos($name, '/')) {
            list(, $name) = explode('/', $name);
        }
        return $name;
    }

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

<?php

namespace Drupal\ParseComposer;

use Symfony\Component\Yaml\Yaml;

class InfoFile
{
    private $name;
    private $info;

    /**
     * @param string $name machine name of Drupal project
     * @param string $info valid Drupal .info file contents
     */
    public function __construct($filename, $info, $core)
    {
        $this->filename = $filename;
        list($this->name, , $isYaml) = array_pad(
            explode('.', $this->filename),
            3,
            false
        );
        $this->info = $isYaml
            ? Yaml::parse($info)
            : \drupal_parse_info_format($info);
        $this->core = $core;
    }

    public function getProjectName()
    {
        return $this->name;
    }

    /**
     * @param string $dependency a valid .info dependencies value
     */
    public function constraint($dependency)
    {
        $matches = array();
        preg_match(
            '/([a-z0-9_]*)\s*(\(([^\)]+)*\))*/',
            $dependency,
            $matches
        );
        list($all, $project, $v, $versionConstraints) = array_pad($matches, 4, '');
        $project = trim($project);
        if (empty($versionConstraints)) {
            return array(
                'drupal/'.$project => Constraint::loose(new Version($this->core))
            );
        }
        foreach (preg_split('/[, ]+/', $versionConstraints) as $versionConstraint) {
            preg_match(
                '/([><=]*)([0-9a-z\.\-]*)/',
                $versionConstraint,
                $matches
            );
            list($all, $symbols, $version) = $matches;
            if (
                strpos($version, "{$this->core}.x") !== 0
                && strpos($version, '-') === false
            ) {
                $version = "{$this->core}.x-$version";
            }
            $versionString = (string) new Version($version);
            $version = str_replace('unstable', 'patch', $versionString);
            $constraints[] = $symbols.$version;
        }
        return array('drupal/'.$project => implode(',', $constraints));
    }

    /**
     * @return array $info composer-compatible info for the info file
     */
    public function packageInfo()
    {
        $deps = isset($this->info['dependencies']) ? $this->info['dependencies'] : array();
        $deps = is_array($deps) ? $deps : array($deps);
        $info = array(
          'name' => 'drupal/'.$this->name,
          'require' => $this->constraint('drupal'),
        );
        if (isset($this->info['description'])) {
          $info['description'] = $this->info['description'];
        }
        foreach($deps as $dep) {
            $info['require'] += $this->constraint($dep);
        }
        return $info;
    }

    public function drupalInfo()
    {
        return $this->info;
    }
}

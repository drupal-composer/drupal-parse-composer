<?php

namespace Drupal\ParseComposer;

use Symfony\Component\Yaml\Yaml;

/**
 * Representation of a Drupal project's .info(.yml) file.
 */
class InfoFile
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $info;

    /**
     * @param string  $filename File name of Drupal project main file
     * @param string  $info     Valid Drupal .info file contents
     * @param integer $core     Drupal core version.
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
        $this->versionFactory = new VersionFactory();
    }

    /**
     * @return string
     */
    public function getProjectName()
    {
        return $this->name;
    }

    /**
     * Build composer constraint out of given dependency.
     *
     * @param string $dependency A valid .info dependency value
     *
     * @return array
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
            $constraint = "{$this->core}.*";

            return array(
                'drupal/'.$project => $constraint
            );
        }

        foreach (preg_split('/(,\s*)+/', $versionConstraints) as $versionConstraint) {
            preg_match(
                '/([><!=]*)\s*([0-9a-z\.\-]*)/',
                $versionConstraint,
                $matches
            );
            list($all, $symbols, $version) = $matches;
            $versionString = $this->versionFactory
                ->create([$this->core, $version], $project === 'drupal')
                ->getSemVer();
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
        foreach ($deps as $dep) {
            $info['require'] += $this->constraint($dep);
        }

        return $info;
    }

    /**
     * @return array
     */
    public function drupalInfo()
    {
        return $this->info;
    }
}

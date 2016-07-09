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
     * @var array
     */
    protected $coreComponents = [
      7 => [
        'aggregator',
        'block',
        'blog',
        'book',
        'color',
        'comment',
        'contact',
        'contextual',
        'dashboard',
        'dblog',
        'field',
        'field_sql_storage',
        'list',
        'number',
        'options',
        'text',
        'field_ui',
        'file',
        'filter',
        'forum',
        'help',
        'image',
        'locale',
        'menu',
        'node',
        'openid',
        'overlay',
        'path',
        'php',
        'poll',
        'profile',
        'rdf',
        'search',
        'shortcut',
        'statistics',
        'syslog',
        'system',
        'taxonomy',
        'toolbar',
        'tracker',
        'translation',
        'trigger',
        'update',
        'user',
        'minimal',
        'standard',
        'bartik',
        'garland',
        'seven',
        'stark',
      ],
      8 => [
        'system',
      ],
    ];

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
        $this->core = (int) $core;
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
            '/([a-z0-9_:]*)\s*(\(([^\)]+)*\))*/',
            $dependency,
            $matches
        );
        list($all, $project, $v, $versionConstraints) = array_pad(
            $matches,
            4,
            ''
        );
        $project = $this->extractPackageName($project);
        if (empty($versionConstraints)) {
            $constraint = "{$this->core}.*";

            return array(
              'drupal/'.$project => $constraint,
            );
        }

        foreach (preg_split('/(,\s*)+/', $versionConstraints) as $versionConstraint) {
            preg_match(
                '/([><!=]*)\s*([0-9a-z\.\-]*)/',
                $versionConstraint,
                $matches
            );
            list($all, $symbols, $version) = $matches;

            // Version: 1.x, > 1.x
            preg_match('/^([0-9]+)\.x$/', $version, $matches);
            if (!empty($matches)) {
                $version = $matches[1];
                if (empty($symbols)) {
                    $constraints[] = $symbols.$this->core.'.'.$version.'.*';
                } else {
                    $constraints[] = $symbols.$this->core.'.'.$version.'.0';
                }
                continue;
            }

            // Version: 7.x-1.x, > 7.x-1.x
            preg_match('/^([0-9]+)\.x-([0-9]+)\.x$/', $version, $matches);
            if (!empty($matches)) {
                $version = $matches[2];
                if (empty($symbols)) {
                    $constraints[] = $symbols.$this->core.'.'.$version.'.*';
                } else {
                    $constraints[] = $symbols.$this->core.'.'.$version.'.0';
                }
                continue;
            }

            // Version: 8.0.1, > 8.0.1, 8.0.1-beta1
            if ($this->core >= 8) {
                preg_match('/^([0-9]+.[0-9]+.[0-9]+)(-.*)?$/', $version, $matches);
                if (!empty($matches)) {
                    $constraints[] = $symbols.$version;
                    continue;
                }
            }

            try {
                $versionString = $this->versionFactory
                  ->create([$this->core, $version], $this->isCoreComponent($project))
                  ->getSemver();
                $version = str_replace('unstable', 'patch', $versionString);
                $constraints[] = $symbols.$version;
            } catch (InvalidVersionException $e) {
                $constraints[] =  '*';
            }
        }

        return array('drupal/'.$project => implode(', ', $constraints));
    }

    /**
     * @return array $info composer-compatible info for the info file
     */
    public function packageInfo()
    {
        $info = array(
          'name' => 'drupal/'.$this->name,
          'require' => $this->getRequirements(),
        );

        if (isset($this->info['description'])) {
            $info['description'] = $this->info['description'];
        }

        return $info;
    }

    /**
     * @return array
     */
    public function getRequirements()
    {
        $requirements = array();
        $deps = isset($this->info['dependencies']) ? $this->info['dependencies'] : array();
        $deps = is_array($deps) ? $deps : array($deps);

        switch ($this->core) {
            case 7:
                $requirements += $this->constraint('drupal');
                break;
            case 8:
                $requirements += $this->constraint('core');
                break;
        }

        foreach ($deps as $dep) {
            $requirements += $this->constraint($dep);
        }

        return $requirements;
    }

    /**
     * @return array
     */
    public function drupalInfo()
    {
        return $this->info;
    }

    /**
     * Checks if the given project is a Drupal core component.
     *
     * @param string $name Machine name of the project
     *
     * @return bool Returns TRUE if its part of the given core.
     */
    protected function isCoreComponent($name)
    {
        if ($this->core === 7 && $name === 'drupal') {
            return true;
        } elseif ($this->core === 8 && $name === 'core') {
            return true;
        }

        if (!isset($this->coreComponents[$this->core])) {
            return false;
        }
        $components = array_flip($this->coreComponents[$this->core]);

        return isset($components[$name]);
    }

    /**
     * Get project namespace from dependency.
     *
     * @param string $dependency Machine name of the project (with project namespace)
     *
     * @return string Namespace of the project
     *
     * @throws \InvalidArgumentException
     */
    protected function extractPackageName($dependency)
    {
        $package = explode(':', trim($dependency));
        if (count($package) === 1 || count($package) === 2) {
            $namespace = $package[0];
            // Rewrite all packages with namespace drupal: to drupal/core
            if ($this->core === 8 && $namespace === 'drupal') {
                return 'core';
            }

            return $namespace;
        }
        throw new \InvalidArgumentException('Invalid dependency name');
    }
}

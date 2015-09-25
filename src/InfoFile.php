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
        // Get the match string for each core version.
        $match_string = $this->getMatchStrings();
        $version_match_string = $match_string[$this->core];
        preg_match(
            $version_match_string['match_string'],
            $dependency,
            $matches
        );
        list($all, $project, $v, $versionConstraints) = array_pad($matches, 4,
            '');
        // Parse the structure and test the matches.
        foreach ($version_match_string['keys'] as $key => $value) {
            if ($value) {
                preg_match(
                    $value,
                    $$key,
                    $$key
                );
                list($all, $$key) = array_pad($$key, 2, '');
            }
        }
        $project = trim($project);
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

            $versionString = $this->versionFactory
              ->create([$this->core, $version], $this->isCoreComponent($project))
              ->getSemVer();
            $version = str_replace('unstable', 'patch', $versionString);
            $constraints[] = $symbols.$version;
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
        //todo: for drupal 8 there have to be checked if are core or non-core modules.
        if ($this->core == 8) {
            return null;
        }
        $components = array_flip($this->coreComponents[$this->core]);

        return isset($components[$name]);
    }

    /**
     * Returns match string for core versions.
     */
    protected function getMatchStrings()
    {
        // For each component of the matched string there attached another string for matching in case is required or
        // false if it shouldn't use another match string.
        return array(
            7 => [
                'match_string' => '/([a-z0-9_]*)\s*(\(([^\)]+)*\))*/',
                'keys' => [
                    'all' => FALSE,
                    'project' => FALSE,
                    'v' => FALSE,
                    'versionConstraint' => FALSE,
                ],
            ],
            8 => [
                'match_string' => '/([a-z0-9_:]*)\s*(\(([^\)]+)*\))*/',
                'keys' => [
                    'all' => FALSE,
                    'project' => '/([a-z0-9_]*)\s*(\(([^\)]+)*\))*/',
                    'v' => FALSE,
                    'versionConstraint' => FALSE,
                ],
            ],
        );
    }
}

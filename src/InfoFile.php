<?php

namespace Drupal\ParseComposer;

use Symfony\Component\Yaml\Yaml;

class InfoFile
{
    private $name;
    private $info;

    protected $core_components = [
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
        $this->versionFactory = new VersionFactory();
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
        list($all, $project, $v, $versionConstraints) = array_pad($matches, 4,
          '');
        $project = trim($project);
        if (empty($versionConstraints)) {
            $constraint = "{$this->core}.*";

            return array(
              'drupal/'.$project => $constraint,
            );
        }
        foreach (preg_split('/(,\s*)+/',
          $versionConstraints) as $versionConstraint) {
            preg_match(
              '/([><!=]*)\s*([0-9a-z\.\-]*)/',
              $versionConstraint,
              $matches
            );
            list($all, $symbols, $version) = $matches;

            preg_match('/^([0-9]+)\.x$/', $version, $matches);
            if (!empty($matches)) {
                $version = $matches[1];
                $constraints[] = $symbols.$this->core.'.'.$version.'.*';
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

    public function drupalInfo()
    {
        return $this->info;
    }

    protected function isCoreComponent($name)
    {
        $components = array_flip($this->core_components[$this->core]);

        return isset($components[$name]);
    }
}

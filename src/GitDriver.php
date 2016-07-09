<?php

namespace Drupal\ParseComposer;

use Composer\Downloader\TransportException;
use Composer\Repository\Vcs\GitDriver as BaseDriver;
use Composer\Package\Version\VersionParser;
use Composer\Util\ProcessExecutor;
use Drupal\ParseComposer\DrupalOrg\DistInformation;
use Drupal\ParseComposer\FileFinder\FileFinderTrait;

/**
 * Drupal.org specific Git driver.
 */
class GitDriver extends BaseDriver implements FileFinderInterface
{

    use FileFinderTrait;

    /**
     * @var VersionFactory
     *
     * @todo Fix default and implementations below.
     */
    private $versionFactory = false;

    /**
     * @var AbstractVersion
     */
    private $drupalProjectVersion;

    /**
     * @var string
     */
    private $drupalProjectName;

    /**
     * @var string The identifier to a specific branch/tag/commit
     */
    private $identifier;

    /**
     * {@inheritDoc}
     */
    public function getComposerInformation($identifier)
    {
        $this->identifier = $identifier;
        try {
            $composer = parent::getComposerInformation($identifier);
        } catch (TransportException $e) {
            // There is not composer.json file in the root
        }
        $composer = is_array($composer) ? $composer : array();

        $ref = strlen($this->identifier) == 40
            ? $this->lookUpRef($this->identifier)
            : $this->identifier;
        if ($version = $this->getVersion($ref)) {
            $core = $version->getCore();
            $this->drupalProjectVersion = $version;
        } else {
            return [];
        }
        // TODO: make configurable?
        if ($core < 7) {
            return [];
        }
        $project = new Project($this->drupalProjectName, $this, $core);
        if (null != ($drupalInformation = $project->getDrupalInformation())) {
            if (isset($drupalInformation[$this->drupalProjectName])) {
                $topInformation = $drupalInformation[$this->drupalProjectName];
            } else {
                $topInformation = current($drupalInformation);
            }
            foreach (array('replace', 'require', 'suggest') as $link) {
                $composer[$link] = isset($composer[$link])
                    ? $composer[$link]
                    : array();
            }
            foreach (array('replace', 'require', 'suggest') as $link) {
                if (isset($topInformation[$link])) {
                    foreach ($topInformation[$link] as $package => $version) {
                        if (!isset($composer[$link][$package])) {
                            $composer[$link][$package] = $version;
                        }
                    }
                }
            }
            foreach (array_keys($drupalInformation) as $name) {
                if ($name != $this->drupalProjectName) {
                    $composer['replace']["drupal/$name"] = 'self.version';
                }
            }
            foreach ($drupalInformation as $info) {
                if ($info['name'] != $topInformation['name']) {
                    foreach ($info['require'] as $package => $version) {
                        if (!isset($composer['suggest'][$package])) {
                            $composer['suggest'][$package] = 'Required by ' . $info['name'];
                        } else {
                            $composer['suggest'][$package] .= ', ' . $info['name'];
                        }
                    }
                }
            }
            $keys = array_diff(
                array_keys($composer['require']),
                array_keys($composer['replace'])
            );
            $composer['require'] = array_intersect_key(
                $composer['require'],
                array_combine($keys, $keys)
            );
            foreach ($composer['require'] as $name => $constraint) {
                if (preg_match('/^\d+\.\d+\.\d+$/', $constraint)) {
                    $composer['require'][$name] = "~$constraint";
                }
            }
            $composer += array(
                'description' => null,
                'require' => array(),
                'type' => 'library'
            );
            foreach (array('description', 'type') as $top) {
                $composer[$top] = isset($topInformation[$top]) ? $topInformation[$top] : $composer[$top];
            }

            $composer['name'] = 'drupal/' . $this->drupalProjectName;
            $composer = $this->mergeDefaultMetadata($composer, $project, $identifier);
            unset($composer['require'][$composer['name']]);
            unset($composer['suggest'][$composer['name']]);
            unset($composer['suggest']['drupal/drupal']);
        }

        return $composer;
    }

    /**
     * Get version object from reference.
     *
     * @param string $ref
     *
     * @return AbstractVersion
     *
     * @todo Specify parameters.
     */
    private function getVersion($ref)
    {
        $version = false;
        if (!$this->versionFactory) {
            $this->versionFactory = new VersionFactory();
        }
        if ($this->validateTag($ref)) {
            $version = $this->versionFactory->fromSemVer($ref, $this->isCore);
        } else {
            $version = $this->versionFactory->create($ref, $this->isCore);
        }

        return $version;
    }

    /**
     * Retrieve commit reference from any branch or tag reference.
     *
     * @param string $ref Branch or tag reference:
     *   Defaults to the current identifier.
     *
     * @return string|null
     */
    public function lookUpRef($ref = null)
    {
        $refMap = array_flip(array_merge(
            $this->getBranches(),
            $this->getTags()
        ));
        $ref = $ref ?: $this->identifier;

        return isset($refMap[$ref]) ? $refMap[$ref] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getTags()
    {
        if (null === $this->tags) {
            $this->tags = parent::getTags();

            // Remove invalid tags.
            foreach ($this->tags as $tag => $hash) {
                if (!$this->getVersion($tag)) {
                    unset($this->tags[$tag]);
                }
            }
        }

        $tags = [];
        foreach ($this->tags as $tag => $hash) {
            if ($version = $this->getVersion($tag)) {
                $tags[$version->getSemVer()] = $hash;
            }
        }

        return $tags;
    }

    /**
     * {@inheritdoc}
     *
     * Overrides parent to get all branches and filter out those without version.
     */
    public function getBranches()
    {
        if (null === $this->branches) {
            $branches = array();

            $this->process->execute('git branch -a --no-color --no-abbrev -v', $output, $this->repoDir);
            foreach ($this->process->splitLines($output) as $branch) {
                if ($branch
                    && !preg_match('{^ *[^/]+/HEAD }', $branch)
                    && preg_match('{^(?:\* )? *(\S+) *([a-f0-9]+) .*$}', $branch, $match)
                    && $this->getVersion($name = @end(explode('/', $match[1])))
                ) {
                    $branches[$name] = $match[2];
                }
            }
            $this->branches = $branches;
        }

        return $this->branches;
    }

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        $this->drupalProjectName = $this->repoConfig['drupalProjectName'];
        $this->isCore = ($this->drupalProjectName === 'drupal');
        parent::initialize();
    }

    /**
     * {@inheritDoc}
     */
    public function getDist($identifier)
    {
        $distVersion = false;
        foreach (array('tags', 'branches') as $refs) {
            $map = array_flip($this->$refs);
            if (!$distVersion) {
                $distVersion = isset($map[$identifier]) ? $map[$identifier] : false;
            }
        }
        if ($distVersion) {
            $dist = new DistInformation(
                $this->drupalProjectName,
                $distVersion
            );

            $info = $dist->toArray();
            if ($info) {
                return $info;
            }
        }

        return null;
    }

    /**
     * Check if the given version string can be parsed by composer.
     *
     * @param string $version Version string
     *
     * @return bool|string
     *
     * @todo: change name and return value.
     */
    private function validateTag($version)
    {
        if (is_numeric($version[0])) {
            $parser = new VersionParser();
            try {
                return $parser->normalize($version);
            } catch (\Exception $e) {
            }
        }

        return false;
    }

    /**
     * Retrieve list of paths for the current commit.
     *
     * @return string[]
     */
    private function getPaths()
    {
        if (!isset($this->paths[$this->identifier])) {
            $this->process->execute(
                sprintf('git ls-tree -r %s --name-only', $this->identifier),
                $out,
                $this->repoDir
            );
            $this->paths[$this->identifier] = $this->process->splitLines($out);
        }

        return $this->paths[$this->identifier];
    }

    /**
     * {@inheritdoc}
     */
    public function fileContents($path)
    {
        $resource = sprintf("%s:%s", escapeshellarg($this->identifier), $path);
        $this->process->execute(
            "git show $resource",
            $out,
            $this->repoDir
        );

        return $out;
    }

    /**
     * Add default metadata from drupal.org to the package definition.
     *
     * @param array   $package
     * @param Project $project
     * @param string  $identifier
     * @return array
     */
    public function mergeDefaultMetadata($package, Project $project, $identifier)
    {
        $version = $this->drupalProjectVersion;
        $core = $this->drupalProjectVersion->getCore();
        $majorSlug = $this->isCore ? '' : "{$version->getMajor()}.x";
        $devBranch = "dev-$core.x-$majorSlug";
        $package['extra']['branch-alias'][$devBranch] = $core . '.' . ($majorSlug ?: 'x') . '-dev';

        if (!isset($package['homepage'])) {
            $package['homepage'] = 'https://www.drupal.org/project/' . $project->getName();
        }
        if (!isset($package['support']['issues'])) {
            $package['support']['issues'] = 'https://www.drupal.org/project/issues/' . $project->getName();
        }
        if (!isset($package['support']['source'])) {
            $package['support']['source'] = 'http://cgit.drupalcode.org/' . $project->getName();
        }
        if (!isset($package['time'])) {
            $output = null;
            $this->process->execute(sprintf('git log -1 --format=%%at %s', ProcessExecutor::escape($identifier)), $output, $this->repoDir);
            $date = new \DateTime('@'.trim($output), new \DateTimeZone('UTC'));
            $package['time'] = $date->format('Y-m-d H:i:s');
        }
        if (!isset($package['license'])) {
            $package['license'] = [
                'GPL-2.0+'
            ];
        }

        return $package;
    }
}

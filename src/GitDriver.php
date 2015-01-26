<?php

namespace Drupal\ParseComposer;

use Composer\Repository\Vcs\GitDriver as BaseDriver;
use Composer\Package\Version\VersionParser;

/**
 * Drupal.org specific Git driver.
 */
class GitDriver extends BaseDriver implements FileFinderInterface
{

    /**
     * @var VersionFactory
     *
     * @todo Fix default and implementations below.
     */
    private $versionFactory = false;

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
            $majorSlug = $this->isCore ? '' : "{$version->getMajor()}.x";
            $devBranch = "dev-$core.x-$majorSlug";
            $composer['extra']['branch-alias'][$devBranch] = $core.'.'
                .($majorSlug ?: 'x').'-dev';
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
            foreach (array_keys($drupalInformation) as $name) {
                if ($name != $this->drupalProjectName) {
                    $composer['replace']["drupal/$name"] = 'self.version';
                }
            }
            $composer['require'] = $topInformation['require'];
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
        $tags = [];
        foreach (parent::getTags() as $tag => $hash) {
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
        $this->drupalDistUrlPattern = 'http://ftp.drupal.org/files/projects/%s-%s.zip';
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
            return array(
                'type' => 'zip',
                'url' => sprintf($this->drupalDistUrlPattern, $this->drupalProjectName, $distVersion)
            );
        }
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
    public function pathMatch($pattern)
    {
        $paths = array();
        foreach ($this->getPaths() as $path) {
            if (is_callable($pattern)) {
                if ($pattern($path)) {
                    $paths[] = $path;
                }
            } elseif (preg_match($pattern, $path)) {
                $paths[] = $path;
            }
        }

        return $paths;
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
}

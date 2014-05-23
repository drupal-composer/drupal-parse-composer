<?php

namespace Drupal\ParseComposer;

use Composer\Repository\Vcs\GitDriver as BaseDriver;
use Composer\Package\Version\VersionParser;

class GitDriver extends BaseDriver implements FileFinderInterface
{

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

        $version = $this->lookUpRef();
        if (Version::valid($version)) {
            $version = new Version(
                $version,
                $this->drupalProjectName === 'drupal'
            );
            $core = $version->getCore();
        }
        elseif ($this->validateTag($version)) {
            $core = $version[0];
        }
        else {
            return [];
        }
        $project = new Project($this->drupalProjectName, $this, $core);
        if (NULL != ($drupalInformation = $project->getDrupalInformation())) {
            $topInformation = $drupalInformation[$this->drupalProjectName];
            foreach (array('replace', 'require') as $link) {
                $composer[$link] = isset($composer[$link])
                    ? $composer[$link]
                    : array();
            }
            foreach (array_keys($drupalInformation) as $name) {
                if ($name != $this->drupalProjectName) {
                    $composer['replace']["drupal/$name"] = 'self.version';
                }
            }
            foreach ($drupalInformation as $info) {
                $composer['require'] = array_merge($composer['require'], $info['require']);
            }
            $keys = array_diff(
                array_keys($composer['require']),
                array_keys($composer['replace'])
            );
            $composer['require'] = array_intersect_key(
                $composer['require'],
                array_combine($keys, $keys)
            );
            $composer += array(
                'description' => null,
                'require' => array(),
                'type' => 'library'
            );
            foreach (array('description', 'type') as $top) {
                $composer[$top] = isset($topInformation[$top]) ? $topInformation[$top] : $composer[$top];
            }
            $composer['name'] = 'drupal/'.$this->drupalProjectName;
            unset($composer['require'][$composer['name']]);
        }
        return $composer;
    }

    public function lookUpRef($ref = null)
    {
        $refMap = array_flip(
            array_merge(
                $this->getBranches(),
                $this->getTags()
            )
        );
        $ref = $ref ?: $this->identifier;
        return isset($refMap[$ref]) ? $refMap[$ref] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getTags()
    {
        foreach (parent::getTags() as $tag => $hash) {
            if (Version::valid($tag)) {
                $version = (string) new Version(
                    $tag,
                    $this->drupalProjectName === 'drupal'
                );
                $tags[] = $hash;
            }
        }
        return $tags;
    }

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        $this->drupalDistUrlPattern = 'http://ftp.drupal.org/files/projects/%s-%s.zip';
        parent::initialize();
    }

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

    public function pathMatch($pattern)
    {
        $paths = array();
        foreach ($this->getPaths() as $path) {
            if (is_callable($pattern)) {
                if ($pattern($path)) {
                    $paths[] = $path;
                }
            }
            elseif (preg_match($pattern, $path)) {
                $paths[] = $path;
            }
        }
        return $paths;
    }

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
     * {@inheritDoc}
     */
    public function getDist($identifier)
    {
        $distVersion = FALSE;
        foreach (array('tags', 'branches') as $refs) {
            $map = array_flip($this->$refs);
            if (!$distVersion) {
                $distVersion = isset($map[$identifier]) ? $map[$identifier] : FALSE;
            }
        }
        if ($distVersion) {
            return array(
                'type' => 'zip',
                'url' => sprintf($this->drupalDistUrlPattern, $this->drupalProjectName, $distVersion)
            );
        }
    }

    private function validateTag($version)
    {
        $parser = new VersionParser();
        try {
            return $parser->normalize($version);
        } catch (\Exception $e) {
        }

        return false;
    }
}

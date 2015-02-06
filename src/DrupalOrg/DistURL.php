<?php

namespace Drupal\ParseComposer\DrupalOrg;

/**
 * @class DistUrl
 * Value class that provides the dist URL for a given Drupal.org project and
 * version.
 */
class DistURL
{
    /**
     * @var String
     */
    private $url;

    /**
     * @param string $projectName the name of the project
     * @param string $refName     a tag or branch name
     */
    public function __construct($projectName, $refName)
    {
        $this->url = sprintf(
            'http://ftp.drupal.org/files/projects/%s-%s.zip',
            $projectName,
            preg_replace('/(\.x)$/', '$1-dev', $refName)
        );
    }

    /**
     * @return String the url to use for the package dist metadata
     */
    public function __toString()
    {
        return $this->url;
    }
}

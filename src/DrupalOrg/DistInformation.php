<?php

namespace Drupal\ParseComposer\DrupalOrg;

/**
 * @class DistInformation
 * Value class that provides the dist URL for a given Drupal.org project and
 * version.
 */
class DistInformation
{
    /**
     * @var String
     */
    private $url;

    /**
     * @var String
     */
    private $type;

    /**
     * @var String
     */
    private $hash;

    /**
     * @param string $projectName the name of the project
     * @param string $refName     a tag or branch name
     */
    public function __construct($projectName, $refName)
    {
        $version = preg_replace('/(\.x)$/', '$1-dev', $refName, -1, $count);
        if ($count == 0) {
            $this->url = sprintf(
                'http://ftp.drupal.org/files/projects/%s-%s.zip',
                $projectName,
                $version
            );
            $this->type = 'zip';
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if (isset($this->url) && isset($this->type)) {
            return [
                'type' => $this->type,
                'url' => $this->url,
            ];
        }

        return [];
    }

}

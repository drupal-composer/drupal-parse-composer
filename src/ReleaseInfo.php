<?php

namespace Drupal\ParseComposer;

/**
 * Wrapper for drupal.org release information,
 */
class ReleaseInfo
{
    private $releaseUrl = 'https://updates.drupal.org/release-history';
    private $projectName;
    private $client;
    private $version;
    private $xml = false;

    /**
     * @param string $projectName Drupal org project name.
     * @param string $version     Drupal project version string.
     * @param Client $client      Optionally you can pass a specific guzzle
     *                            client to fetch data with.
     */
    public function __construct($projectName, $version, Client $client = null)
    {
        $this->projectName  = $projectName;
        $this->version      = $version;
        $this->client       = $client ?: new Client();
        $this->load($projectName, $version);
    }

    /**
     * Loads necessary data for the given project and version.
     *
     * @param string $projectName
     * @param string $version
     *
     * @todo Params not needed anymore.
     */
    public function load($projectName, $version)
    {
        if (!$this->xml) {
            $this->xml = $this->fetch();
        }
    }

    /**
     * Checks if release exists.
     *
     * @return bool
     */
    public function exists()
    {
        $test = $this->xml->xpath('/error');

        return empty($test);
    }

    /**
     * Fetch release history for given project information from drupal.org.
     *
     * @return \Guzzle\Http\Message\RequestInterface|\SimpleXMLElement
     */
    private function fetch()
    {
        return $this->client->get(
            sprintf(
                '%s/%s/%d.x',
                $this->releaseUrl,
                $this->projectName,
                $this->version
            )
        );
    }

    /**
     * Retrieve project type from xml.
     *
     * @return string
     */
    public function getProjectType()
    {
        $projectTypes = array(
            'profile'         => 'Distributions',
            'profile-legacy'  => 'Installation profiles',
            'module'          => 'Modules',
            'theme'           => 'Themes'
        );
        $typesXpath = '/project/terms/term[name="Projects"]';
        $type       = 'module';
        if ($types = $this->xml->xpath($typesXpath)) {
            $type = array_search($types[0]->value, $projectTypes);
            $type = ($type == 'profile-legacy') ? 'profile' : $type;
        }

        return "drupal-$type";
    }
}

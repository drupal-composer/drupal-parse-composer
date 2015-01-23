<?php

namespace Drupal\ParseComposer;

abstract class AbstractVersion implements VersionInterface
{

    const CORE_PATTERN  = '\d+\.([0-9]+|x)';
    const EXTRA_PATTERN = '(-[a-z]+\d*)?';

    /**
     * @var int  Drupal core version like "7" in 7.x-2.1-rc2
     */
    protected $core;

    /**
     * @var int  Major version like "2" in 7.x-2.1-rc2
     */
    protected $major = 0;

    /**
     * @var int  Minor version like "1" in 7.x-2.1-rc2
     */
    protected $minor = 0;

    /**
     * @var string  Addition to a minor version, like "rc2" in 7.x-2.1-rc2
     */
    protected $extra;

    /**
     * Constructor.
     *
     * @param string $version A full version string.
     */
    public function __construct($version)
    {
        $this->parse($version);
    }

    /**
     * Represents the object as semver string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getSemver();
    }

    /**
     * Gets core version.
     *
     * @return int
     */
    public function getCore()
    {
        return $this->core;
    }

    /**
     * Gets major version.
     *
     * @return int
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * Gets minor version.
     *
     * @return int
     */
    public function getMinor()
    {
        return $this->minor;
    }

    /**
     * Builds semver string from version information.
     *
     * @return string
     */
    public function getSemver()
    {
        return sprintf('%d.%d.%s', $this->core, $this->major, $this->minor)
            . ($this->extra ? "-{$this->extra}" : '');
    }

    /**
     * Tests if the given version string will construct a valid version string.
     *
     * @param string $version A version string
     *
     * @return bool
     */
    public static function valid($version)
    {
        return true;
    }

    /**
     * Builds up properties for the current object out of a version string.
     *
     * @param string $versionString A version string
     */
    abstract public function parse($versionString);
}

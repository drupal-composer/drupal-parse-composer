<?php

namespace Drupal\ParseComposer;

/**
 * Abstract definition of version as base for CoreVersion and Version.
 */
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
     * {@inheritdoc}
     */
    public function getCore()
    {
        return $this->core;
    }

    /**
     * {@inheritdoc}
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinor()
    {
        return $this->minor;
    }

    /**
     * {@inheritdoc}
     */
    public function getSemver()
    {
        return sprintf('%d.%d.%s', $this->core, $this->major, $this->minor)
            . ($this->extra ? "-{$this->extra}" : '');
    }

    /**
     * {@inheritdoc}
     */
    public static function valid($version)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function parse($versionString);
}

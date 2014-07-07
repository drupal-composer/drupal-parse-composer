<?php

namespace Drupal\ParseComposer;

abstract class AbstractVersion implements VersionInterface
{

    const CORE_PATTERN  = '\d+\.([0-9]+|x)';
    const EXTRA_PATTERN = '(-[a-z]+\d*)?';

    protected $core;
    protected $major = 0;
    protected $minor = 0;
    protected $extra;

    public function __construct($version)
    {
        $this->parse($version);
    }

    public function __toString()
    {
        return $this->getSemver();
    }

    public function getCore()
    {
        return $this->core;
    }

    public function getMajor()
    {
        return $this->major;
    }

    public function getMinor()
    {
        return $this->minor;
    }

    public function getSemver()
    {
        return sprintf('%d.%d.%s', $this->core, $this->major, $this->minor)
            . ($this->extra ? "-{$this->extra}" : '');
    }

    public static function valid($version)
    {
        return true;
    }

    abstract public function parse($versionString);
}

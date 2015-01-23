<?php

namespace Drupal\ParseComposer;

class Constraint
{

    /**
     * Constructor.
     *
     * @param VersionInterface $version Version object
     */
    public function __construct(VersionInterface $version)
    {
        $this->version = $version;
    }

    /**
     * Provides a loose constraint, holding only core version.
     *
     * @return string
     */
    public function getLoose()
    {
        return "{$this->version->getCore()}.*";
    }

    /**
     * Static wrapper for getting loose version string from an existing version.
     *
     * @param VersionInterface $version Version object
     *
     * @return mixed
     */
    public static function loose(VersionInterface $version)
    {
        $constraint = new static($version);
        return $constraint->getLoose();
    }
}

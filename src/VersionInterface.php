<?php

namespace Drupal\ParseComposer;

/**
 * Interface for version object.
 */
interface VersionInterface
{
    /**
     * Gets core version.
     *
     * @return int
     */
    public function getCore();

    /**
     * Gets major version.
     *
     * @return int
     */
    public function getMajor();

    /**
     * Gets minor version.
     *
     * @return int
     */
    public function getMinor();

    /**
     * Builds semver string from version information.
     *
     * @return string
     */
    public function getSemver();

    /**
     * Tests if the given version string will construct a valid version object.
     *
     * @param string $version A version string
     *
     * @return bool
     */
    public static function valid($version);

    /**
     * Builds up properties for the current object out of a version string.
     *
     * @param string $versionString A version string
     */
    public function parse($versionString);
}

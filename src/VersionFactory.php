<?php

namespace Drupal\ParseComposer;

class VersionFactory
{
    /**
     * Creates a version object from version information.
     *
     * @param string|array $versionInfo Either a version string or an array of
     *   core version and rest of version string.
     * @param bool $isCore
     *
     * @return AbstractVersion
     *
     * @todo Replace array syntax with constraint?
     */
    public function create($versionInfo, $isCore = false)
    {
        if (is_array($versionInfo)) {
            list($core, $fragment) = $versionInfo;
            if (strpos($fragment, "$core.x") === 0 || $isCore) {
                $drupalVersion = $fragment;
            } else {
                $fragment = strpos($fragment, '.') ? $fragment : "$fragment.x";
                $drupalVersion = "$core.x-$fragment";
            }
        } else {
            $drupalVersion = $versionInfo;
        }
        if ($isCore && CoreVersion::valid($drupalVersion)) {
            return new CoreVersion($drupalVersion);
        } elseif (!$isCore && Version::valid($drupalVersion)) {
            return new Version($drupalVersion);
        }
    }

    /**
     * Creates a version object from semver string.
     *
     * @param string $semver Version string formatted in semantic version.
     * @param bool $isCore Indicator if version is core.
     *
     * @return AbstractVersion
     */
    public function fromSemVer($semver, $isCore = false)
    {
        list($core, $major, $minor, $extra) = array_pad(
            preg_split('/[\.-]/', $semver),
            4,
            ''
        );
        if ($isCore) {
            $versionString = "$core.$major" . ($extra ? "-$extra" : '');
            if (CoreVersion::valid($versionString)) {
                return new CoreVersion($versionString);
            }
        } else {
            $versionString = "$core.x-$major.$minor" . ($extra ? "-$extra" : '');
            if (Version::valid($versionString)) {
                return new Version($versionString);
            }
        }
    }
}

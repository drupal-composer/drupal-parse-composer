<?php

namespace Drupal\ParseComposer;

use Composer\Semver\VersionParser;

/**
 * Version representation for Drupal core.
 */
class CoreVersion extends AbstractVersion
{

    /**
     * {@inheritdoc}
     */
    public static function valid($version)
    {
        if (!static::isSupportedCoreVersion($version)) {
            return false;
        }

        $parser = new VersionParser();
        try {
            $parser->normalize($version);

            return true;
        } catch (\UnexpectedValueException $e) {
            // Invalid version.
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($versionString)
    {
        $this->core = null;
        $this->major = 0;
        $this->minor = 0;
        $this->extra = null;

        list($version, $extra) = array_pad(explode('-', $versionString), 2, '');
        list($this->core, $this->major, $this->minor) = array_pad(explode('.', $version), 3, 0);
        if ($this->major === 'x' || $this->minor === 'x') {
            $this->extra = 'dev';
        } elseif ($extra) {
            $this->extra = $extra;
        }
        $this->core = intval($this->core);
    }

    /**
     * @param string $version A version string
     * @return bool
     */
    protected static function isSupportedCoreVersion($version)
    {
        $core = intval($version[0]);

        return ($core === 8 || $core === 7);
    }

    /**
     * @param array $regex An array of regex strings.
     * @return string
     */
    protected static function buildRegex(array $regex)
    {
        return '/' . implode('|', $regex) . '/';
    }

}

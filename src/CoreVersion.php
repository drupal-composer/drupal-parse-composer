<?php

namespace Drupal\ParseComposer;

/**
 * Version representation for Drupal core.
 */
class CoreVersion extends AbstractVersion
{
    const D8_CORE_PATTERN = [
        '(8)\.([[:digit:]]+)\.([[:digit:]]+|x)(?:-([[:alnum:]]+))?',
        '(8)\.([[:digit:]]+|x)(?:-([[:alnum:]]+))?'
    ];

    const D7_CORE_PATTERN = [
        '(7)\.([[:digit:]]+)\.([[:digit:]]|x)(?:-([[:alnum:]]+))?',
        '(7)\.([[:digit:]]+|x)(?:-([[:alnum:]]+))?'
    ];


    /**
     * {@inheritdoc}
     */
    public static function valid($version)
    {
        if (!static::isSupportedCoreVersion($version)) {
            return false;
        }

        // D8
        $match = preg_match(static::buildRegex(static::D8_CORE_PATTERN), $version);
        if ($match) {
            return true;
        }

        // D7
        $match = preg_match(static::buildRegex(static::D7_CORE_PATTERN), $version);
        if ($match) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($versionString)
    {
        $this->core = NULL;
        $this->major = 0;
        $this->minor = 0;
        $this->extra = NULL;

        list($version, $extra) = array_pad(explode('-', $versionString), 2, '');
        list($this->core, $this->major, $this->minor) = array_pad(explode('.', $version), 3, 0);
        if ($this->major === 'x' || $this->minor === 'x') {
            $this->extra = 'dev';
        }
        elseif ($extra) {
            $this->extra = $extra;
        }
        $this->core = intval($this->core);
    }

    /**
     * @param $version
     * @return bool
     */
    protected static function isSupportedCoreVersion($version)
    {
        $core = intval($version[0]);
        return ($core === 8 || $core === 7);
    }

    protected static function buildRegex($regex) {
        return '/' . implode('|', $regex) . '/';
    }

}

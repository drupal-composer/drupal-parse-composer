<?php

namespace Drupal\ParseComposer;

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
        // D8
        $match = preg_match('/(8)\.([[:digit:]])\.([[:digit:]]|x)(?:-([[:alnum:]]+))?/',$version);
        if ($match) {
            return true;
        }

        // D7
        return !!preg_match(
            sprintf('/^%s%s$/', static::CORE_PATTERN, static::EXTRA_PATTERN),
            $version
        );
    }

    /**
     * {@inheritdoc}
     */
    public function parse($versionString)
    {
        // D8
        if (preg_match('/(8)\.([[:digit:]])\.([[:digit:]]|x)(?:-([[:alnum:]]+))?/', $versionString, $match)) {
            $this->core = intval($match[1]);
            $this->major = $match[2];
            $this->minor = $match[3];
            if ($this->minor === 'x') {
                $this->extra = 'dev';
            }
            else {
                $this->extra = isset($match[4]) ? $match[4] : NULL;
            }
            return;
        }

        // D7
        list($version, $extra) = array_pad(explode('-', $versionString), 2, '');
        list($this->core, $this->major) = explode('.', $version);
        if ($this->major === 'x') {
            $this->extra = 'dev';
        }
        $this->core = intval($this->core);
    }
}

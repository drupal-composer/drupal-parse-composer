<?php

namespace Drupal\ParseComposer;

/**
 * Version information for a Drupal project.
 */
class Version extends AbstractVersion
{
    /**
     * {@inheritdoc}
     */
    public static function valid($version)
    {
        return !!preg_match(
            sprintf(
                '/^%s(-\d+\.[0-9x]+)%s$/',
                static::CORE_PATTERN,
                static::EXTRA_PATTERN
            ),
            $version
        );
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

        switch (count($parts = explode('-', $versionString))) {
            case 2:
                list($this->core, $version) = $parts;
                break;
            case 3:
                list($this->core, $version, $this->extra) = $parts;
                break;
        }
        list($this->major, $this->minor) = explode('.', $version);
        if ($this->minor === 'x') {
            $this->extra = 'dev';
        }
        $this->core = intval($this->core);
    }
}

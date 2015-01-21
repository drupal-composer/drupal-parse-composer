<?php

namespace Drupal\ParseComposer;

class Version extends AbstractVersion
{
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

    public function parse($versionString)
    {
        $this->core = NULL;
        $this->major = 0;
        $this->minor = 0;
        $this->extra = NULL;

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

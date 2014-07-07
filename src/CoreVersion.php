<?php

namespace Drupal\ParseComposer;

class CoreVersion extends AbstractVersion
{
    public static function valid($version)
    {
        return !!preg_match(
            sprintf('/^%s%s$/', static::CORE_PATTERN, static::EXTRA_PATTERN),
            $version
        );
    }

    public function parse($versionString)
    {
        list($version, $extra) = array_pad(explode('-', $versionString), 2, '');
        list($this->core, $this->major) = explode('.', $version);
        if ($this->major === 'x') {
            $this->extra = 'dev';
        }
        $this->core = intval($this->core);
    }
}

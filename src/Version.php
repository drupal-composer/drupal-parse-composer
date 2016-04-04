<?php

namespace Drupal\ParseComposer;

use Naneau\SemVer\Parser;

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
        try {
            if ($version[0] == 8) {
                Parser::parse($version);
                return true;
            }
        } catch (\InvalidArgumentException $e) {

        }

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
        if ($versionString[0] == 8) {
            if (preg_match('/([[:digit:]])\.([[:digit:]])\.([[:digit:]]|x)(?:-([[:alnum:]]+))?/', $versionString, $match)) {
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
        }

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

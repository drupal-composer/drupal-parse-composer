<?php

namespace Drupal\ParseComposer;

class Constraint
{

    public function __construct(VersionInterface $version)
    {
        $this->version = $version;
    }

    public function getLoose()
    {
        return "{$this->version->getCore()}.*";
    }

    public static function loose(VersionInterface $version)
    {
        $constraint = new static($version);
        return $constraint->getLoose();
    }
}

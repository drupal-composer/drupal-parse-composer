<?php

namespace Drupal\ParseComposer;

interface VersionInterface
{
    public function getCore();

    public function getMajor();

    public function getMinor();

    public function getSemver();

    public static function valid($version);

    public function parse($versionString);
}

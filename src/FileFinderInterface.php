<?php

namespace Drupal\ParseComposer;

interface FileFinderInterface
{
    public function pathMatch($pattern);
    public function fileContents($pattern);
}

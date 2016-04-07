<?php

/**
 * @file
 * Contains \Drupal\ParseComposer\FileFinder\DummyFileFinder.
 */

namespace Drupal\ParseComposer\FileFinder;

use Drupal\ParseComposer\FileFinderInterface;

class DummyFileFinder implements FileFinderInterface
{

    use FileFinderTrait;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    protected function getPaths() {
        return array_keys($this->data);
    }

    public function fileContents($path)
    {
        return $this->data[$path];
    }

}

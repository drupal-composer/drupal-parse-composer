<?php

namespace Drupal\ParseComposer\FileFinder;

trait FileFinderTrait
{

    /**
     * {@inheritdoc}
     */
    public function pathMatch($pattern)
    {
        $paths = array();
        foreach ($this->getPaths() as $path) {
            if (is_callable($pattern)) {
                if ($pattern($path)) {
                    $paths[] = $path;
                }
            } elseif (preg_match($pattern, $path)) {
                $paths[] = $path;
            }
        }

        return $paths;
    }

}

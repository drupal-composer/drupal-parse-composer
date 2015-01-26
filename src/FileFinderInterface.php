<?php

namespace Drupal\ParseComposer;

/**
 * Interface for retrieving information from a list of files.
 */
interface FileFinderInterface
{
    /**
     * Matches or walks a defined set of paths with given pattern or callable.
     *
     * @param callable|string $pattern Callable
     *
     * @return null|string[]
     *
     * @todo Fix implementation
     */
    public function pathMatch($pattern);

    /**
     * Provides the content of a file on the given path.
     *
     * @param string $path Path to the file to get content from
     *
     * @return string
     */
    public function fileContents($path);
}

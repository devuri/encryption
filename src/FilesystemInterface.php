<?php

namespace Urisoft;

/**
 * Interface for managing Filesystem.
 */
interface FilesystemInterface
{
    /**
     * Atomically dumps content into a file.
     *
     * @param resource|string $content The data to write into the file
     *
     * @return mixed
     *
     * @see https://github.com/symfony/filesystem/blob/6.3/Filesystem.php#L659
     */
    public function dumpFile( string $filename, $content );
}

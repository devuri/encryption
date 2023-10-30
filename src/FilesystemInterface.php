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
     * @param string|resource $content The data to write into the file
     *
     * @return void
     *
     * @throws IOException if the file cannot be written to
     *
     * @see https://github.com/symfony/filesystem/blob/6.3/Filesystem.php#L659
     */
    public function dumpFile(string $filename, $content);
}

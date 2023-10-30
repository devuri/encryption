<?php

namespace Urisoft;

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;;

class Filesystem extends SymfonyFilesystem implements FilesystemInterface
{
	protected $system = null;
}

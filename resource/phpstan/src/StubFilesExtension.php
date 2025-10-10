<?php

namespace Dev\PHPStan;

use SplFileInfo;
use Symfony\Component\Finder\Finder;

final class StubFilesExtension implements \PHPStan\PhpDoc\StubFilesExtension
{
    public function getFiles(): array
    {
        $files = Finder::create()
            ->files()
            ->name('*.stub')
            ->in(__DIR__ . '/../stubs');

        return array_map(fn(SplFileInfo $file) => $file->getRealPath(), iterator_to_array($files));
    }
}

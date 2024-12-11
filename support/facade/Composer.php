<?php

namespace support\facade;

class Composer
{
    public static function postAutoloadDump()
    {
        $autoloadFile = __DIR__ . '/../../vendor/autoload.php';
        $content = file_get_contents($autoloadFile);

        $replace = <<<TXT
<?php

require __DIR__ . '/../support/functions.php';

TXT;

        $content = str_replace('<?php', $replace, $content);
        file_put_contents($autoloadFile, $content);
    }
}

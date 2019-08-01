<?php

namespace Medz\Component\Installer\ThinkSNS\Composer;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;

/**
 * ThinkSNS-4应用安装器
 *
 * @package Medz\Component\Installer\ThinkSNS\Composer\Installer
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class Installer extends LibraryInstaller
{
    public function supports($packageType)
    {
        $packageType = strtolower($packageType);
        return $packageType == 'thinksns-app';
    }

    public function getInstallPath(PackageInterface $package)
    {
        $extra = $package->getExtra();

        if (!$extra['install-name']) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The "%s" application is not set "installer-name" field.' . PHP_EOL
                    . 'Using the following config within your package composer.json will allow this:' . PHP_EOL
                    . '{' . PHP_EOL
                    . '    "name": "vendor/name",' . PHP_EOL
                    . '    "type": "thinksns-app",' . PHP_EOL
                    . '    "extra": {' . PHP_EOL
                    . '        "installer-name": "Demo-Name"' . PHP_EOL
                    . '    }' . PHP_EOL
                    . '}' . PHP_EOL
                ),
                $package->getName()
            );
        }

        return 'apps/' . $extra['install-name'];
    }

} // END class Installer extends LibraryInstaller

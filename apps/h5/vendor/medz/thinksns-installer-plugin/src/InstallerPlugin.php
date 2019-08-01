<?php

namespace Medz\Component\Installer\ThinkSNS\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Medz\Component\Installer\ThinkSNS\Composer\Installer;

/**
 * ThinkSNS-4 Installed soft entry.
 *
 * @package Medz\Component\Installer\ThinkSNS\Composer\InstallerPlugin
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class InstallerPlugin implements PluginInterface
{
    /**
     * 插件入口方法
     *
     * @param Composer $composer 安装器基类
     * @param IOInterface $io IO接口
     * @return void
     * @author Seven Du <lovevipdsw@outlook.com>
     * @datetime 2016-04-02T15:54:15+0800
     * @homepage http://medz.cn
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = new Installer($io, $composer);
        $composer
            ->getInstallationManager()
            ->addInstaller($installer)
        ;
    }

} // END class InstallerPlugin implements PluginInterface

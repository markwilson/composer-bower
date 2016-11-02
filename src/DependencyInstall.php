<?php

namespace ComposerBower;

use Composer\Script\CommandEvent;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Bower dependency installer
 *
 * @package ComposerBower
 * @author  Mark Wilson <mark@89allport.co.uk>
 */
class DependencyInstall
{
    /**
     * Execute the Bower installer
     *
     * @param CommandEvent $event Command event
     *
     * @return void
     */
    public static function execute(CommandEvent $event)
    {
        // TODO: load options from CLI
        // TODO: allow custom binary path/name

        $processBuilder = new ProcessBuilder(['bower', 'install']);
        $processBuilder->getProcess()->mustRun();
    }
}

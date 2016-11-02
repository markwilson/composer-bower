<?php

namespace ComposerBower;

use Composer\Script\Event;
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
     * Key used in composer.json extras array
     *
     * @const string
     */
    const EXTRA_OPTIONS_KEY = 'composer-bower';

    /**
     * Execute the Bower installer
     *
     * @param Event $event Event
     *
     * @return void
     */
    public static function execute(Event $event)
    {
        // TODO: load options from CLI
        // TODO: allow custom binary path/name
        // TODO: allow custom working directory
        // TODO: allow working directory to be defined with a package prefix

        $processBuilder = new ProcessBuilder(['bower', 'install']);
        $processBuilder->getProcess()->mustRun();
    }

    /**
     * Get the composer.json extra options
     *
     * @param Event $event Event
     *
     * @return array
     */
    private static function getOptions(Event $event)
    {
        $extras = $event->getComposer->getPackage()->getExtra();
        if (empty($extras[self::EXTRA_OPTIONS_KEY])) {
            return [];
        }

        return $extras[self::EXTRA_OPTIONS_KEY];
    }
}

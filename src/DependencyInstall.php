<?php

namespace ComposerBower;

use Composer\Script\Event;
use Symfony\Component\Process\Process;
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
        (new self(self::getOptions($event)))->run();
    }

    /**
     * DependencyInstall constructor.
     *
     * @param array $options Extra options
     */
    public function __construct(array $options)
    {
        // TODO: validate $options
        // TODO: detect bower.json before trying to execute

        $this->options = $options;
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
        $extras = $event->getComposer()->getPackage()->getExtra();
        if (empty($extras[self::EXTRA_OPTIONS_KEY])) {
            return [];
        }

        return $extras[self::EXTRA_OPTIONS_KEY];
    }

    /**
     * Run the process
     *
     * @return void
     */
    private function run()
    {
        $process = self::buildProcess();

        $process->mustRun();
    }

    /**
     * Build the `bower install` process
     *
     * @return Process
     */
    private function buildProcess()
    {
        // TODO: allow custom binary path/name
        // TODO: allow custom working directory
        // TODO: allow working directory to be defined with a package prefix
        // TODO: provide output based on composer verbosity setting

        $processBuilder = new ProcessBuilder(['bower', 'install']);
        $processBuilder->setWorkingDirectory('');

        return $processBuilder->getProcess();
    }
}

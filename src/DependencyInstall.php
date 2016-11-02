<?php

namespace ComposerBower;

use Composer\Composer;
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
     * Options
     *
     * @var array
     */
    private $options;

    /**
     * Composer
     *
     * @var Composer
     */
    private $composer;

    /**
     * Execute the Bower installer
     *
     * @param Event $event Event
     *
     * @return void
     */
    public static function execute(Event $event)
    {
        $options = self::getOptions($event);

        // TODO: check if options is an array of arrays so bower can be executed in multiple locations
        (new self($event, $options))->run();
    }

    /**
     * DependencyInstall constructor.
     *
     * @param Event $event   Composer scripts event
     * @param array $options Configuration options
     */
    public function __construct(Event $event, array $options)
    {
        $this->options = $options;

        // TODO: validate $options
        // TODO: detect bower.json before trying to execute

        $this->composer = $event->getComposer();
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
        // TODO: provide output based on composer verbosity setting

        $processBuilder = new ProcessBuilder(['bower', 'install']);
        $processBuilder->setWorkingDirectory($this->getWorkingDirectory());

        return $processBuilder->getProcess();
    }

    /**
     * Get the working directory for executing bower install
     *
     * @param boolean $realPath Use the realpath?
     *
     * @return string
     */
    private function getWorkingDirectory($realPath = true)
    {
        if (!empty($this->options['package'])) {
            $cwd = $this->getPackageDirectory();
        } else {
            $cwd = getcwd();
        }

        $suffix = !empty($this->options['working-directory']) ? $this->options['working-directory'] : '';

        $path = $cwd . '/' . $suffix;

        if ($realPath) {
            return realpath($path);
        }

        return $path;
    }

    /**
     * Get a package directory
     *
     * @return string
     */
    private function getPackageDirectory()
    {
        return $this->composer->getConfig()->get('vendor-dir') . '/' . $this->options['package'];
    }
}

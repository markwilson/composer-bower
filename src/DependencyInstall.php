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

        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        require_once $vendorDir . '/autoload.php';

        if (self::containsMultipleConfigurations($options)) {
            $configurations = $options;
        } else {
            $configurations = [$options];
        }

        foreach ($configurations as $options) {
            (new self($event, $options))->run();
        }
    }

    /**
     * DependencyInstall constructor.
     *
     * @param Event $event   Composer scripts event
     * @param array $options Configuration options
     */
    public function __construct(Event $event, array $options)
    {
        $this->composer = $event->getComposer();
        $this->options = $options;

        // TODO: validate $options
        // TODO: detect bower.json before trying to execute
        if ('' === $this->getWorkingDirectory()) {
            throw new \RuntimeException(sprintf('Working directory does not exist: %s', $this->getWorkingDirectory(false)));
        }
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
     * Contains multiple configurations
     *
     * @param array $options Configuration options
     *
     * @return boolean
     */
    private static function containsMultipleConfigurations(array $options)
    {
        return \array_is_indexed($options);
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

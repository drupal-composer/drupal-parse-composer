<?php

namespace Drupal\ParseComposer;

use Symfony\Component\Process\ExecutableFinder;
use Composer\Package\Dumper\ArrayDumper;
use Composer\Util\Filesystem;
use Composer\Util\ProcessExecutor;
use Composer\IO\NullIO;
use Composer\Config;

/**
 * Copied from Composer\Test\Repository\VcsRepositoryTest
 */

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    private static $composerHome;
    private static $gitRepo;
    private $skipped;

    protected function initialize()
    {
        self::$composerHome = sys_get_temp_dir() . '/composer-home-'.mt_rand().'/';
        self::$gitRepo = sys_get_temp_dir() . '/composer-git-'.mt_rand().'/omega';

        $locator = new ExecutableFinder();
        if (!$locator->find('git')) {
            $this->skipped = 'This test needs a git binary in the PATH to be able to run';

            return;
        }
        if (!@mkdir(self::$gitRepo, 0777, true) || !@chdir(self::$gitRepo)) {
            $this->skipped = 'Could not create and move into the temp git repo '.self::$gitRepo;
            return;
        }
        $process = new ProcessExecutor;
        $exec = function ($command) use ($process) {
            $cwd = getcwd();
            if ($process->execute($command, $output, $cwd) !== 0) {
                throw new \RuntimeException('Failed to execute '.$command.': '.$process->getErrorOutput());
            }
        };
        $exec('git clone http://git.drupal.org/project/omega '.self::$gitRepo);
    }

    public function setUp()
    {
        if (!self::$gitRepo) {
            $this->initialize();
        }
        if ($this->skipped) {
            $this->markTestSkipped($this->skipped);
        }
        $this->dumper = new ArrayDumper();
    }

    public static function tearDownAfterClass()
    {
        $fs = new Filesystem;
        $fs->removeDirectory(self::$composerHome);
        $fs->removeDirectory(self::$gitRepo);
    }

    public function testLoadVersions()
    {
        $expected = array(
            '7.4.2' => array(
                'type' => 'drupal-theme'
            ),
            '7.3.1' => true,
            'dev-7.x-4.x' => true,
        );

        $config = new Config();
        $config->merge(array(
            'config' => array(
                'home' => self::$composerHome,
            ),
        ));
        $repo = new Repository(
            array('url' => self::$gitRepo, 'type' => 'vcs'),
            new NullIO,
            $config
        );
        $packages = $repo->getPackages();

        foreach ($packages as $package) {
            if (isset($expected[$package->getPrettyVersion()])) {
                if (is_array($p = $expected[$package->getPrettyVersion()])) {
                    $this->assertEmpty(array_diff_assoc(
                        $p, $this->dumper->dump($package)
                    ));
                }
                $this->assertEquals($package->getPrettyName(), 'drupal/omega');
                unset($expected[$package->getPrettyVersion()]);
            }
        }
        $this->assertEmpty($expected, 'Missing versions: '.implode(', ', array_keys($expected)));
    }
}

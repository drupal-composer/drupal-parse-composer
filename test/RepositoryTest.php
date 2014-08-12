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
    private static $gitRepoDir;
    private static $projects;
    private $skipped;

    protected function initialize()
    {
        self::$composerHome = sys_get_temp_dir() . '/composer-home-'.mt_rand().'/';
        self::$gitRepoDir = sys_get_temp_dir() . '/composer-git-'.mt_rand();

        $locator = new ExecutableFinder();
        if (!$locator->find('git')) {
            $this->skipped = 'This test needs a git binary in the PATH to be able to run';

            return;
        }
        if (!@mkdir(self::$gitRepoDir, 0777, true) || !@chdir(self::$gitRepoDir)) {
            $this->skipped = 'Could not create and move into the temp git repo '.self::$gitRepoDir;
            return;
        }
        $process = new ProcessExecutor;
        $exec = function ($command) use ($process) {
            $cwd = getcwd();
            if ($process->execute($command, $output, $cwd) !== 0) {
                throw new \RuntimeException('Failed to execute '.$command.': '.$process->getErrorOutput());
            }
        };
        self::$projects = array(
            array(
                'url' => 'http://git.drupal.org/project/omega',
                'name' => 'omega'
            ),
            array(
                'url' => 'http://git.drupal.org/project/flood_sem',
                'name' => 'flood_sem'
            )
        );
        foreach (self::$projects as $project) {
            $exec("git clone ".$project['url'].' '.self::$gitRepoDir.'/'.$project['name']);
        }
    }

    public function setUp()
    {
        if (!self::$gitRepoDir) {
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
        $fs->removeDirectory(self::$gitRepoDir);
    }

    public function testLoadVersions()
    {
        $projects = array(
            'omega' => array(
                '7.4.2' => array(
                    'type' => 'drupal-theme'
                ),
                '7.3.1' => true,
                'dev-7.x-4.x' => true,
            ),
            'flood_sem' => array(
                'dev-7.x-1.x' => array(
                    'type' => 'drupal-module'
                )
            )
        );

        $config = new Config();
        $config->merge(array(
            'config' => array(
                'home' => self::$composerHome,
            ),
        ));
        foreach ($projects as $name => $expected) {
            $repo = new Repository(
                array('url' => self::$gitRepoDir.'/'.$name, 'type' => 'vcs'),
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
                    $this->assertEquals($package->getPrettyName(), 'drupal/'.$name);
                    unset($expected[$package->getPrettyVersion()]);
                }
            }
            $this->assertEmpty($expected, 'Missing versions: '.implode(', ', array_keys($expected)));
        }

    }
}

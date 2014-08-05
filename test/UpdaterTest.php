<?php

namespace Drupal\ParseComposer;

use Composer\Factory;
use Composer\IO\BufferIO;
use Composer\Repository\VcsRepository;
use Doctrine\ORM\EntityManager;
use Packagist\WebBundle\Entity\Package;
use Symfony\Component\Console\Output\OutputInterface;

class UpdaterTest extends \PHPUnit_Framework_TestCase
{
    public function testDoesNotCrash()
    {
        $doctrine = $this->getMock('Symfony\Bridge\Doctrine\RegistryInterface');
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $doctrine->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($em));
        \date_default_timezone_set('UTC');
        $updater = new Updater($doctrine);
        $collection = new \Doctrine\Common\Collections\ArrayCollection();
        $config = Factory::createConfig();
        $io = new BufferIO('', OutputInterface::VERBOSITY_VERBOSE);
        $projects = [
            'libraries',
            'views',
            'panopoly',
            'omega',
            'drupal',
            'apps',
            'entity',
            'node_clone'
        ];
        foreach ($projects as $project) {
            $repository = new VcsRepository(
                ['url' => "http://git.drupal.org/project/$project"],
                $io,
                $config
            );
            $package = new Package();
            $updater->update($package, $repository);
            print $io->getOutput();
        }
    }
}

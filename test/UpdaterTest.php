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
    /**
     * Tests if basic functional runs without crash.
     *
     * @param string $project
     *  Machine name of a specific drupal.org project.
     *
     * @dataProvider projectProvider
     */
    public function testDoesNotCrash($project)
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

        $repository = new VcsRepository(
          ['url' => "http://git.drupal.org/project/$project"],
          $io,
          $config
        );
        $package = new Package();
        $updater->update($package, $repository);
        print $io->getOutput();
    }

    public function projectProvider()
    {
        return array(
          array('libraries'),
          array('views'),
          array('panopoly'),
          array('omega'),
          array('drupal'),
          array('apps'),
          array('entity'),
          array('node_clone'),
          array('flood_sem'),
        );
    }
}

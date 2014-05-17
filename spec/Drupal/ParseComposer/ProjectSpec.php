<?php

namespace spec\Drupal\ParseComposer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Drupal\ParseComposer\FileFinderInterface as Finder;
use Drupal\ParseComposer\ReleaseInfo;
use Composer\IO\IOInterface;
use Composer\IO\BufferIO;
use Composer\Config;

class ProjectSpec extends ObjectBehavior
{
    function it_knows_its_name(
        ReleaseInfo $release,
        Finder $finder
    )
    {
        $release->getProjectType()->willReturn('drupal-module');
        $this->beConstructedWith('foo', $finder, [6 => $release, 7 => $release]);
        $this->getName()->shouldReturn('foo');
    }
}

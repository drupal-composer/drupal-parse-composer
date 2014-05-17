<?php

namespace spec\Drupal\ParseComposer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Drupal\ParseComposer\GitDriver;
use Composer\IO\IOInterface;
use Composer\IO\BufferIO;
use Composer\Config;

class ProjectSpec extends ObjectBehavior
{
    function let(
        BufferIO $io,
        Config $config,
        GitDriver $driver
    )
    {
        $driver->beConstructedWith(
            array(),
            $io,
            $config->getWrappedObject()
        );
        $this->beConstructedWith('foo', $driver);
    }

    function it_knows_its_name($driver)
    {
        $this->getName()->shouldReturn('foo');
    }
}

<?php

namespace spec\Drupal\ParseComposer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class VersionSpec extends ObjectBehavior
{

    function it_has_reasonable_defaults()
    {
        $this->beConstructedWith('7');
        $this->getCore()->shouldReturn(7);
        $this->getSemver()->shouldReturn('7.0.0');
    }

    function it_understands_minimal_versions()
    {
        $this->beConstructedWith('7');
        $this->parse('2.2');
        $this->getCore()->shouldReturn(7);
        $this->getSemver()->shouldReturn('7.2.2');
    }

    function it_understands_full_versions()
    {
        $this->beConstructedWith('7.x-2.4-beta');
        $this->getSemver()->shouldReturn('7.2.4-beta');
        $this->parse('7.x-2.x-dev');
        $this->getSemver()->shouldReturn('7.2.x-dev');
    }
}

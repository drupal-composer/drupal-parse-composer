<?php

namespace spec\Drupal\ParseComposer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class VersionSpec extends ObjectBehavior
{
    function it_understands_validation()
    {
        $this->beConstructedWith('7.x-1.1');

        $this->valid('7.x-2.x-dev')->shouldReturn(true);
        $this->valid('7.x-2.4-beta3')->shouldReturn(true);

        $this->valid('7.x-2.4-rc1')->shouldReturn(true);
        $this->valid('7.x-2.5')->shouldReturn(true);

        $this->valid('8.x-1.5')->shouldReturn(true);
    }

    function it_understands_full_versions()
    {
        $this->beConstructedWith('7.x-2.4-beta');
        $this->getSemver()->shouldReturn('7.2.4-beta');

        $this->parse('7.x-2.x-dev');
        $this->getSemver()->shouldReturn('7.2.x-dev');

        $this->parse('7.x-2.4-beta3');
        $this->getSemver()->shouldReturn('7.2.4-beta3');

        $this->parse('7.x-2.4-rc1');
        $this->getSemver()->shouldReturn('7.2.4-rc1');

        $this->parse('7.x-2.5');
        $this->getSemver()->shouldReturn('7.2.5');

        $this->parse('6.x-2.16-rc1');
        $this->getSemver()->shouldReturn('6.2.16-rc1');
    }

}

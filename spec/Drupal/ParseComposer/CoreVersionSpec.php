<?php

namespace spec\Drupal\ParseComposer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CoreVersionSpec extends ObjectBehavior
{
    function it_understands_validation() {
        $this->beConstructedWith('8.0.0');

        $this::valid('7.41')->shouldReturn(true);
        $this::valid('7.41.0')->shouldReturn(true);
        $this->valid('8.0.0')->shouldReturn(true);
        $this->valid('8.0.0')->shouldReturn(true);
        $this->valid('8.1.0-beta1')->shouldReturn(true);
        $this->valid('8.2.0-rc10')->shouldReturn(true);
        $this->valid('8.3.0-alpha1')->shouldReturn(true);
        $this->valid('8.3.2-alpha5')->shouldReturn(true);
        $this->valid('8.10.3-rc2')->shouldReturn(true);
        $this->valid('8.10.34-rc2')->shouldReturn(true);
        $this->valid('8.42-rc2')->shouldReturn(true);

        // D9 is intentionally disabled.
        $this->valid('9.0.0')->shouldReturn(false);
    }

    function it_understands_full_versions()
    {
        $this->beConstructedWith('7.41');

        $this->parse('7.41');
        $this->getSemver()->shouldReturn('7.41.0');

        $this->parse('7.41.0');
        $this->getSemver()->shouldReturn('7.41.0');

        $this->parse('8.0');
        $this->getSemver()->shouldReturn('8.0.0');

        $this->parse('8.0.0');
        $this->getSemver()->shouldReturn('8.0.0');

        $this->parse('8.1');
        $this->getSemver()->shouldReturn('8.1.0');

        $this->parse('8.1.0');
        $this->getSemver()->shouldReturn('8.1.0');

        $this->parse('8.1.0-beta1');
        $this->getSemver()->shouldReturn('8.1.0-beta1');

        $this->parse('8.2.x');
        $this->getSemver()->shouldReturn('8.2.x-dev');

        $this->parse('8.10.1');
        $this->getSemver()->shouldReturn('8.10.1');

        $this->parse('8.10.x');
        $this->getSemver()->shouldReturn('8.10.x-dev');

        $this->parse('8.42');
        $this->getSemver()->shouldReturn('8.42.0');

        $this->parse('8.42-alpha10');
        $this->getSemver()->shouldReturn('8.42.0-alpha10');
    }

}

<?php

namespace spec\Drupal\ParseComposer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Drupal\ParseComposer\Version;

class ConstraintSpec extends ObjectBehavior
{
    function it_makes_loose_constraints(Version $version)
    {
        $version->getCore()->willReturn(7);
        $this->beConstructedWith($version);
        $this->getLoose()->shouldReturn('7.*');
    }
}

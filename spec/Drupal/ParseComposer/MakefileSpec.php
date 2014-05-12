<?php

namespace spec\Drupal\ParseComposer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MakefileSpec extends ObjectBehavior
{
    function let($info)
    {
        $info = file_get_contents(__DIR__.'/../../../res/example.make');
        $this->beConstructedWith($info);
    }

    function it_takes_input($info)
    {
        $this->getMakeInfo()->shouldBeArray();
    }

    function it_understands_versions($info)
    {
        $this->getVersion('kalatheme')->shouldReturn('7.1.2');
    }

    function it_understands_tags($info)
    {
        $this->getVersionFromTag('panopoly_theme')->shouldReturn('7.1.0-rc5');
    }

    function it_understands_normal_branches($info)
    {
        $this->getConstraint('drupal')->shouldReturn('dev-master');
        $this->getConstraint('kw_manifests')->shouldReturn('dev-master');
    }

    function it_uses_the_most_specific_info($info)
    {
        $this->getConstraint('kalatheme')->shouldReturn('7.1.2');
        $this->getConstraint('flexslider')->shouldReturn('dev-7.x-2.x');
        $this->getConstraint('panopoly_theme')->shouldReturn('7.1.0-rc5');
    }

    function it_provides_a_loose_constraint_when_there_is_no_version($info)
    {
        $this->getConstraint('composer_manager')->shouldReturn('7.*');
    }
}

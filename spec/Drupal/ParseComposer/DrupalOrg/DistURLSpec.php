<?php

namespace spec\Drupal\ParseComposer\DrupalOrg;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DistURLSpec extends ObjectBehavior
{
    function it_translates_core_dev_branches()
    {
        $this->beConstructedWith('drupal', '7.x');
        $this->__toString()->shouldReturn(
            'http://ftp.drupal.org/files/projects/drupal-7.x-dev.zip'
        );
    }
    function it_translates_tagged_core_releases()
    {
        $this->beConstructedWith('drupal', '7.34');
        $this->__toString()->shouldReturn(
            'http://ftp.drupal.org/files/projects/drupal-7.34.zip'
        );
    }
    function it_translates_contrib_dev_branches()
    {
        $this->beConstructedWith('views', '7.x-3.x');
        $this->__toString()->shouldReturn(
            'http://ftp.drupal.org/files/projects/views-7.x-3.x-dev.zip'
        );
    }
    function it_translates_tagged_contrib_releases()
    {
        $this->beConstructedWith('views', '7.x-3.8');
        $this->__toString()->shouldReturn(
            'http://ftp.drupal.org/files/projects/views-7.x-3.8.zip'
        );
    }
}

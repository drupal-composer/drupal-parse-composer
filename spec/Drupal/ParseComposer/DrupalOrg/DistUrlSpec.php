<?php

namespace spec\Drupal\ParseComposer\DrupalOrg;

use PhpSpec\ObjectBehavior;

class DistUrlSpec extends ObjectBehavior
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
    function it_translates_tagged_core_releases_with_suffix(){
        $this->beConstructedWith('drupal', '8.0.0-beta6');
        $this->__toString()->shouldReturn(
            'http://ftp.drupal.org/files/projects/drupal-8.0.0-beta6.zip'
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
    function it_translates_tagged_contrib_releases_with_suffix(){
        $this->beConstructedWith('backup_migrate', '7.x-3.0-alpha1');
        $this->__toString()->shouldReturn(
            'http://ftp.drupal.org/files/projects/backup_migrate-7.x-3.0-alpha1.zip'
        );
    }
}

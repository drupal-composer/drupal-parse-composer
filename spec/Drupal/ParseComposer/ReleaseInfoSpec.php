<?php

namespace spec\Drupal\ParseComposer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Drupal\ParseComposer\Client;

class ReleaseInfoSpec extends ObjectBehavior
{
    function it_downloads_release_info(Client $client)
    {
        $get = $client->get('https://updates.drupal.org/release-history/foo/7.x');
        $get->shouldBeCalled();
        $get->willReturn(file_get_contents(__DIR__.'/../../../res/update.xml'));
        $this->beConstructedWith('foo', 7, $client);
        $this->load('foo', 7);
    }

    function it_translates_module_type_projects(Client $client)
    {
        $client->get('https://updates.drupal.org/release-history/foo/7.x')
            ->willReturn(
                new \SimpleXMLElement(
                    file_get_contents(__DIR__.'/../../../res/update.xml')
                )
            );
        $this->beConstructedWith('foo', 7, $client);
        $this->getProjectType()->shouldReturn('drupal-module');
    }

    function it_translates_theme_type_projects(Client $client)
    {
        $client->get('https://updates.drupal.org/release-history/foo-theme/7.x')
            ->willReturn(
                new \SimpleXMLElement(
                    file_get_contents(__DIR__.'/../../../res/update-theme.xml')
                )
            );
        $this->beConstructedWith('foo-theme', 7, $client);
        $this->getProjectType()->shouldReturn('drupal-theme');
    }
}

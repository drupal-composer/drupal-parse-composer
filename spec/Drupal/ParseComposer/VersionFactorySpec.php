<?php

namespace spec\Drupal\ParseComposer;

use Drupal\ParseComposer\CoreVersion;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class VersionFactorySpec extends ObjectBehavior
{

    function it_understands_semver_d7() {
        $this->fromSemVer('7.41', true)->shouldReturnSemVer('7.41.0');
        $this->fromSemVer('7.41.x', true)->shouldReturn(null);
    }

    function it_understands_semver_d8()
    {
        $this->fromSemVer('8.0.0', true)->shouldReturnSemVer('8.0.0');
        $this->fromSemVer('8.0.5', true)->shouldReturnSemVer('8.0.5');
        $this->fromSemVer('8.1.0-rc1', true)->shouldReturnSemVer('8.1.0-rc1');
        $this->fromSemVer('8.2.0-alpha2', true)->shouldReturnSemVer('8.2.0-alpha2');
        $this->fromSemVer('8.0.x', true)->shouldReturn(null);
    }

    public function getMatchers() {
        return [
           'returnSemVer' => function (CoreVersion $version, $versionString) {
               return (bool) ($version->getSemver() === $versionString);
           }
       ];
    }

}

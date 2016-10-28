<?php

namespace spec\Drupal\ParseComposer;

use Drupal\ParseComposer\FileFinder\DummyFileFinder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Drupal\ParseComposer\FileFinderInterface as Finder;
use Drupal\ParseComposer\ReleaseInfo;
use Composer\Config;

class ProjectSpec extends ObjectBehavior
{
    function it_knows_its_name(ReleaseInfo $release, Finder $finder)
    {
        $release->getProjectType()->willReturn('drupal-module');
        $this->beConstructedWith('foo', $finder, [6 => $release, 7 => $release]);
        $this->getName()->shouldReturn('foo');
    }

    function it_know_drupal_modules_d8(ReleaseInfo $release, Finder $finder)
    {
        $data = [];
        $data['foo.info.yml'] = <<<'EOF'
name: foo
core: 8
type: module
EOF;
        $finder = new DummyFileFinder($data);
        $this->beConstructedWith('foo', $finder, 8);
        $this->getDrupalInformation()->shouldHaveComposerValue('foo', ['type'], 'drupal-module');
    }

    function it_knows_drush_extensions(ReleaseInfo $release, Finder $finder)
    {
        $data = [];
        $data['foo.drush.inc'] = '<?php // A drush command file';
        $finder = new DummyFileFinder($data);

        $this->beConstructedWith('foo', $finder, 8);
        $this->getDrupalInformation()->shouldHaveComposerValue('foo', ['type'], 'drupal-drush');
        $this->getDrupalInformation()->shouldHaveComposerKey('foo', ['require', 'drush/drush']);
    }

    public function getMatchers() {
        return [
            'haveComposerKey' => function($subject, $package, $key) {
                return !is_null(\igorw\get_in($subject[$package], $key));
            },
            'haveComposerValue' => function($subject, $package, $key, $value) {
                return \igorw\get_in($subject[$package], $key) == $value;
            },
        ];
    }


}

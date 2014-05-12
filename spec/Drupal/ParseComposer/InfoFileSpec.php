<?php

namespace spec\Drupal\ParseComposer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InfoFileSpec extends ObjectBehavior
{
    function it_defaults_to_reasonable_constraint()
    {
        $viewsUiInfo = <<<'EOF'
name = Views UI
description = Administrative interface to views. Without this module, you cannot create or edit your views.
package = Views
core = 7.x
configure = admin/structure/views
dependencies[] = views
files[] = views_ui.module
files[] = plugins/views_wizard/views_ui_base_views_wizard.class.php
EOF;
        $this->beConstructedWith('views_ui', $viewsUiInfo);
        $this->constraint('views')->shouldReturn(['drupal/views' => '7.*']);
    }

    function it_understands_full_versions_in_constraints()
    {
        $fooInfo = <<<'EOF'
name = Foo
description = Without this module, you cannot create or edit your foo.
package = Foo
core = 7.x
configure = admin/structure/foo
dependencies[] = bar (7.x-2.x-dev)
files[] = plugins/foo_wizard/foo_base_foo_wizard.class.php
EOF;
        $this->beConstructedWith('foo', $fooInfo);
        $this->constraint('bar (7.x-2.x-dev)')->shouldReturn(['drupal/bar' => '7.2.x-dev']);
    }
}

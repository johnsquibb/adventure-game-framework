<?php

namespace AdventureGame\Test\Platform;

use AdventureGame\Platform\PlatformController;
use AdventureGame\Platform\PlatformRegistry;
use AdventureGame\Test\FrameworkTest;

class PlatformControllerTest extends FrameworkTest
{
    public function testPlatformControllerCreate()
    {
        $platformRegistry = new PlatformRegistry();
        $platformController = new PlatformController($platformRegistry);

        $this->assertEquals($platformRegistry, $platformController->platformRegistry);
    }
}

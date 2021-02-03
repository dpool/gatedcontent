<?php

declare(strict_types=1);

namespace Dpool\Gatedcontent\Tests\Acceptance\Support;

/*
 * This file is part of TYPO3 CMS-based extension "container" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Codeception\Actor;
use Dpool\Gatedcontent\Tests\Acceptance\Support\_generated\FrontendTesterActions;
use Codeception\Util\Locator;

class FrontendTester extends Actor
{
    use FrontendTesterActions;
}

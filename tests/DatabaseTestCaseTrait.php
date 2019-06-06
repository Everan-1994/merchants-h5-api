<?php

namespace Tests;

use PHPUnit\DbUnit\TestCaseTrait;

trait DatabaseTestCaseTrait
{
    use TestCaseTrait {
        tearDown as DbTearDown;
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->dbTearDown();
    }
}

<?php

declare(strict_types=1);

/*
 * Copyright (c) the Contributors as noted in the AUTHORS file.
 *
 * This file is part of the Park-Manager project.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Module\Webhosting\Tests\Infrastructure\Symfony\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use ParkManager\Module\Webhosting\Infrastructure\Symfony\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function it_works_with_empty_config()
    {
        $this->assertProcessedConfigurationEquals([[]], ['capability' => ['command_mapping' => []]]);
    }

    /** @test */
    public function it_checks_capability_command_mapping_values_are_only_arrays()
    {
        $this->assertConfigurationIsInvalid(
            [['capability' => ['command_mapping' => ['']]]],
            'Invalid type for path "webhosting.capability.command_mapping.0". Expected array, but got string'
        );

        $this->assertConfigurationIsInvalid(
            [['capability' => ['command_mapping' => ['he' => 'nope']]]],
            'webhosting.capability.command_mapping.he". Expected array, but got string'
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}

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

namespace ParkManager\Component\Model\Tests\LogMessage;

use ParkManager\Component\Model\LogMessage\LogMessage;
use ParkManager\Component\Model\LogMessage\LogMessages;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class LogMessagesTest extends TestCase
{
    /** @test */
    public function its_constructable()
    {
        $messages = new LogMessages();

        self::assertCount(0, $messages);
        self::assertEquals([], $messages->all());
        self::assertEquals([], $messages->allOf('error'));
        self::assertFalse($messages->hasErrors());
    }

    /** @test */
    public function it_supports_adding_messages()
    {
        $messages = new LogMessages();
        $messages->add($message1 = LogMessage::error('Whoops'));
        $messages->add($message2 = LogMessage::error('Whoops again'));
        $messages->add($message3 = LogMessage::notice('Whoops again'));

        self::assertCount(3, $messages);
        self::assertEquals(['error' => [$message1, $message2], 'notice' => [$message3]], $messages->all());
        self::assertEquals([$message1, $message2], $messages->allOf('error'));
        self::assertEquals([$message3], $messages->allOf('notice'));
        self::assertTrue($messages->hasErrors());
    }
}

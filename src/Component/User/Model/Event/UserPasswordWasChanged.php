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

namespace ParkManager\Component\User\Model\Event;

use ParkManager\Component\Model\DomainEvent;
use ParkManager\Component\User\Model\Command\UserIdTrait;
use ParkManager\Component\User\Model\UserId;

/**
 * @author Sebastiaan Stok <s.stok@rollerworks.net>
 */
final class UserPasswordWasChanged extends DomainEvent
{
    use UserIdTrait;

    public static function withData(UserId $id): self
    {
        /** @var self $event */
        $event = self::occur($id->toString());
        $event->id = $id;

        return $event;
    }
}

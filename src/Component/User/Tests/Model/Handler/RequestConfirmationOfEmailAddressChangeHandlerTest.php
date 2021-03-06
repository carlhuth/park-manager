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

namespace ParkManager\Component\User\Tests\Model\Handler;

use ParkManager\Component\Security\Token\SplitToken;
use ParkManager\Component\User\Model\Command\RequestConfirmationOfEmailAddressChange;
use ParkManager\Component\User\Model\Handler\RequestConfirmationOfEmailAddressChangeHandler;
use ParkManager\Component\User\Model\Service\EmailAddressChangeConfirmationMailer;
use ParkManager\Component\User\Model\User;
use ParkManager\Component\User\Model\UserCollection;
use ParkManager\Component\User\Model\UserId;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @internal
 */
final class RequestConfirmationOfEmailAddressChangeHandlerTest extends TestCase
{
    private const USER_ID = '01dd5964-5426-11e7-be03-acbc32b58315';

    /** @test */
    public function it_handles_emailAddress_change_request()
    {
        $handler = new RequestConfirmationOfEmailAddressChangeHandler(
            $this->expectUserSaved('john2@example.com', $this->expectUserConfirmationTokenIsSet()),
            $this->createConfirmationMailer('John2@example.com')
        );

        $command = new RequestConfirmationOfEmailAddressChange(self::USER_ID, 'John2@example.com', 'john2@example.com');
        $handler($command);
    }

    /** @test */
    public function it_handles_emailAddress_change_request_with_emailAddress_already_in_use()
    {
        $handler = new RequestConfirmationOfEmailAddressChangeHandler(
            $this->expectUserNotSaved('john2@example.com'),
            $this->createConfirmationMailer(null)
        );

        $command = new RequestConfirmationOfEmailAddressChange(self::USER_ID, 'John2@example.com', 'john2@example.com');
        $handler($command);
    }

    private function existingId(): UserId
    {
        return UserId::fromString(self::USER_ID);
    }

    private function expectUserConfirmationTokenIsSet(): User
    {
        $userProphecy = $this->prophesize(User::class);
        $userProphecy->id()->willReturn($this->existingId());
        $userProphecy->canonicalEmail()->willReturn('john@example.com');
        $userProphecy->setConfirmationOfEmailAddressChange(Argument::cetera())->willReturn(true);

        return $userProphecy->reveal();
    }

    private function expectUserNotSaved(string $email): UserCollection
    {
        $userProphecy = $this->prophesize(User::class);
        $userProphecy->id()->willReturn($this->existingId());

        $repositoryProphecy = $this->prophesize(UserCollection::class);
        $repositoryProphecy->getByEmailAddress($email)->willReturn($userProphecy->reveal());
        $repositoryProphecy->save(Argument::any())->shouldNotBeCalled();

        return $repositoryProphecy->reveal();
    }

    private function expectUserSaved(string $email, User $user): UserCollection
    {
        $repositoryProphecy = $this->prophesize(UserCollection::class);
        $repositoryProphecy->getByEmailAddress($email)->willReturn(null);
        $repositoryProphecy->get($user->id())->willReturn($user);
        $repositoryProphecy->save($user)->shouldBeCalledTimes(1);

        return $repositoryProphecy->reveal();
    }

    private function createConfirmationMailer(?string $email): EmailAddressChangeConfirmationMailer
    {
        $confirmationMailerProphecy = $this->prophesize(EmailAddressChangeConfirmationMailer::class);

        if ($email) {
            $confirmationMailerProphecy->send(
                $email,
                Argument::that(
                    function (SplitToken $splitToken) {
                        return '' !== $splitToken->token();
                    }
                ),
                Argument::any()
            )->shouldBeCalledTimes(1);
        } else {
            $confirmationMailerProphecy->send(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();
        }

        return $confirmationMailerProphecy->reveal();
    }
}

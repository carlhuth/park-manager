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

namespace ParkManager\Module\Webhosting\Model\DomainName;

use ParkManager\Module\Webhosting\Model\Account\WebhostingAccount;
use ParkManager\Module\Webhosting\Model\DomainName;
use ParkManager\Module\Webhosting\Model\DomainName\Exception\CannotTransferPrimaryDomainName;

/**
 * @author Sebastiaan Stok <s.stok@rollerworks.net>
 */
class WebhostingDomainName
{
    /**
     * @var WebhostingAccount
     */
    protected $account;

    /**
     * @var DomainName
     */
    protected $domainName;

    /**
     * @var bool
     */
    protected $primary = false;

    /**
     * @var WebhostingDomainNameId|null
     */
    protected $id;

    /**
     * ID for storage (do not use directly).
     *
     * @var string
     */
    private $idString;

    public function __construct(WebhostingAccount $account, DomainName $domainName)
    {
        $this->account = $account;
        $this->domainName = $domainName;
        $this->generateId();
    }

    /**
     * @param WebhostingAccount $account
     * @param DomainName        $domainName
     *
     * @return static
     */
    public static function registerPrimary(WebhostingAccount $account, DomainName $domainName)
    {
        $instance = new static($account, $domainName);
        $instance->primary = true;

        return $instance;
    }

    /**
     * @param WebhostingAccount $account
     * @param DomainName        $domainName
     *
     * @return static
     */
    public static function registerSecondary(WebhostingAccount $account, DomainName $domainName)
    {
        return new static($account, $domainName);
    }

    public function id(): WebhostingDomainNameId
    {
        if (null === $this->id) {
            $this->id = WebhostingDomainNameId::fromString($this->idString);
        }

        return $this->id;
    }

    public function domainName(): DomainName
    {
        return $this->domainName;
    }

    public function account(): WebhostingAccount
    {
        return $this->account;
    }

    public function markPrimary(): void
    {
        $this->primary = true;
    }

    public function isPrimary(): bool
    {
        return $this->primary;
    }

    public function transferToAccount(WebhostingAccount $account): void
    {
        // It's still possible the primary marking was given directly before
        // issuing the transfer, meaning the primary marking was not persisted
        // yet for the old owner. But checking this further is not worth it.
        if ($this->isPrimary()) {
            throw CannotTransferPrimaryDomainName::of($this->id, $this->account->id(), $account->id());
        }

        $this->account = $account;
    }

    public function changeName(DomainName $domainName): void
    {
        $this->domainName = $domainName;
    }

    final protected function generateId(): void
    {
        $this->id = WebhostingDomainNameId::create();
        $this->idString = $this->id->toString();
    }
}

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

namespace ParkManager\Module\Webhosting\Model\Package;

use ParkManager\Component\Model\EventsRecordingAggregateRoot;
use ParkManager\Module\Webhosting\Model\Package\Event\WebhostingPackageCapabilitiesWasChanged;
use ParkManager\Module\Webhosting\Model\Package\Event\WebhostingPackageWasCreated;

/**
 * @author Sebastiaan Stok <s.stok@rollerworks.net>
 */
class WebhostingPackage extends EventsRecordingAggregateRoot
{
    /**
     * @var Capabilities
     */
    protected $capabilities;

    /**
     * @var WebhostingPackageId
     */
    protected $id;

    /**
     * ID for storage (do not use directly).
     *
     * @var string
     */
    private $idString;

    /**
     * @var array
     */
    private $metadata = [];

    protected function __construct(WebhostingPackageId $id, Capabilities $capabilities)
    {
        $this->id = $id;
        $this->idString = $id->toString();
        $this->capabilities = $capabilities;
    }

    /**
     * @param WebhostingPackageId $id
     *
     * @return static
     */
    public static function create(WebhostingPackageId $id, Capabilities $capabilities)
    {
        $instance = new static($id, $capabilities);
        $instance->recordThat(WebhostingPackageWasCreated::withData($id, $capabilities));

        return $instance;
    }

    public function id(): WebhostingPackageId
    {
        if (null === $this->id) {
            $this->id = WebhostingPackageId::fromString($this->idString);
        }

        return $this->id;
    }

    public function capabilities(): Capabilities
    {
        return $this->capabilities;
    }

    public function changeCapabilities(Capabilities $capabilities): void
    {
        if ($capabilities->equals($this->capabilities)) {
            return;
        }

        $this->capabilities = $capabilities;
        $this->recordThat(WebhostingPackageCapabilitiesWasChanged::withData($this->id, $capabilities));
    }

    /**
     * Set some (scalar) metadata information for the webhosting package.
     *
     * This information should only contain informational values
     * (eg. the label, description, etc).
     *
     * Not something that be used as a Domain policy. either,
     * don't use this for pricing or storing user-type limitations.
     *
     * Changing the metadata doesn't dispatch a Domain event.
     *
     * @param array $metadata
     */
    public function withMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }
}

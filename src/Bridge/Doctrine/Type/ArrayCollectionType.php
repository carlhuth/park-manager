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

namespace ParkManager\Bridge\Doctrine\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonArrayType;

/**
 * @author Sebastiaan Stok <s.stok@rollerworks.net>
 */
final class ArrayCollectionType extends JsonArrayType
{
    /**
     * @param Collection       $value
     * @param AbstractPlatform $platform
     *
     * @return null|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        return json_encode($value->toArray());
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ArrayCollection
    {
        if ($value === null || $value === '') {
            return new ArrayCollection();
        }

        $value = is_resource($value) ? stream_get_contents($value) : $value;

        return new ArrayCollection(json_decode($value, true));
    }

    public function getName(): string
    {
        return 'array_collection';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}

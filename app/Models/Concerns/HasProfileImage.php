<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasProfileImage
{
    /**
     * @return Attribute<non-falsy-string, never>
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(
            fn (): string => $this->image
                ? '/storage/'.$this->image
                : $this->defaultProfileImage(),
        );
    }

    /**
     * @return non-falsy-string
     */
    abstract protected function defaultProfileImage(): string;
}

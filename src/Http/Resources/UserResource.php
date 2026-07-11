<?php

declare(strict_types=1);

namespace ChrisReedIO\Socialment\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $name
 * @property string $email
 */
class UserResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            // Not returning the id by default at this time for extra security
            // 'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            // 'avatar' => $this->avatar,
            // 'email_verified_at' => $this->email_verified_at,
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
        ];
    }
}

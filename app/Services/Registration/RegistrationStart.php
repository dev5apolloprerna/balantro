<?php

namespace App\Services\Registration;

use App\Models\RegistrationRequest;

/** Result of starting a registration. `token` is shown to the client once. */
final class RegistrationStart
{
    public function __construct(
        public readonly RegistrationRequest $request,
        public readonly string $token,
        public readonly bool $smsSent,
    ) {
    }

    /** Opaque handle for the client: "<id>.<token>". */
    public function handle(): string
    {
        return $this->request->id . '.' . $this->token;
    }
}

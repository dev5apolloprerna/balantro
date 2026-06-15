<?php

namespace App\Http\Constants;

class HttpStatus
{
    public const OK = 200; // success response
    public const CREATED = 201; // Already Exists
    public const BAD_REQUEST = 400; // Bad Request
    public const UNAUTHORIZED = 401; // Un-authorized request
    public const FORBIDDEN = 403; // Already exists or Exception found
}

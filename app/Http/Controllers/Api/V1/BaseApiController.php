<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;

class BaseApiController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;
    use \App\Http\Concerns\ResponseConcern;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    protected function recordNotFound()
    {
        return $this->error("Record not found", 422);
    }

    protected function handleStandardError(\Exception $exception)
    {
        \Log::error($exception->getMessage());
        \Log::error($exception->getTraceAsString());

        return $this->error("Internal server error", 500);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BannedRequest;
use App\Http\Requests\MakeAdminRequest;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse as JsonResponse;

class AdminController extends Controller
{
    public function makeAdmin(AdminService $service, MakeAdminRequest $request): JsonResponse
    {
        return $service->makeAdmin($request->validated());
    }
    public function ban(AdminService $service,BannedRequest $request): JsonResponse
    {
        return $service->banned($request->validated());
    }

    public function unban(AdminService $service,BannedRequest $request): JsonResponse
    {
        return $service->unbanned($request->validated());
    }
}

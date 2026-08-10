<?php

namespace App\Http\Middleware;

use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    public function __construct(private AuditService $auditService) {}

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if ($request->user() && $request->method() !== 'GET') {
            $this->auditService->log(
                action: strtolower($request->method()) . '_' . $this->getRouteName($request),
                entityType: $this->getEntityType($request),
                entityId: $request->route('id') ?? $request->route(str_replace('App\Models\\', '', $this->getEntityType($request))),
                description: $request->user()->name . ' realizou ' . $request->method() . ' em ' . $request->path(),
                ipAddress: $request->ip(),
                userAgent: $request->userAgent(),
            );
        }
    }

    private function getRouteName(Request $request): string
    {
        return $request->route()?->getName() ?? 'unknown';
    }

    private function getEntityType(Request $request): string
    {
        $segments = explode('/', $request->path());
        return $segments[0] ?? 'unknown';
    }
}

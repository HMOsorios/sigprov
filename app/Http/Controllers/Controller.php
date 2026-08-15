<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    protected function success(string $message, array $data = [], int $status = 200)
    {
        if (request()->wantsJson()) {
            return response()->json(array_merge(['success' => true, 'message' => $message], $data), $status);
        }
        return redirect()->back()->with('success', $message);
    }

    protected function error(string $message, int $status = 400)
    {
        if (request()->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }
        return redirect()->back()->with('error', $message);
    }

    protected function redirectWith(string $route, string $message, string $type = 'success')
    {
        return redirect()->route($route)->with($type, $message);
    }
}

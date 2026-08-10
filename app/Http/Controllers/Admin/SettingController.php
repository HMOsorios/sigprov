<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index');
    }

    public function system(): View
    {
        $phpVersion = phpversion();
        $laravelVersion = app()->version();
        $environment = app()->environment();
        $debugMode = config('app.debug');
        $dbConnection = config('database.default');

        return view('admin.settings.system', compact(
            'phpVersion', 'laravelVersion', 'environment', 'debugMode', 'dbConnection'
        ));
    }

    public function audit(): View
    {
        $logs = AuditLog::with('user')
            ->latest()
            ->paginate(50);

        return view('admin.settings.audit', compact('logs'));
    }

    public function notifications(): View
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return view('admin.settings.notifications', compact('notifications', 'unreadCount'));
    }

    public function markNotification(Notification $notification): RedirectResponse
    {
        $notification->markAsRead();
        return redirect()->back();
    }

    public function markAllNotifications(): RedirectResponse
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return redirect()->back()->with('success', 'Todas as notificações marcadas como lidas.');
    }

    public function clearCache(): RedirectResponse
    {
        Artisan::call('optimize:clear');

        return redirect()->back()->with('success', 'Cache limpo com sucesso!');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:100'],
            'app_url' => ['required', 'url'],
        ]);

        $this->updateEnv('APP_NAME', $validated['app_name']);
        $this->updateEnv('APP_URL', $validated['app_url']);

        return redirect()->back()->with('success', 'Configurações atualizadas!');
    }

    private function updateEnv(string $key, string $value): void
    {
        $envFile = base_path('.env');
        $content = file_get_contents($envFile);
        $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        file_put_contents($envFile, $content);
    }
}

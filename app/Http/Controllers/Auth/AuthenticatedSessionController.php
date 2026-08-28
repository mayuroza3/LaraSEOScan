<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $scanUuid = $request->input('scan_uuid') ?: session('guest_scan_uuid');
        if ($scanUuid) {
            $scan = \App\Models\SeoScan::where('uuid', $scanUuid)->whereNull('user_id')->first();
            if ($scan) {
                $scan->update(['user_id' => Auth::id()]);
            }
            session()->forget('guest_scan_uuid');
            return redirect(route('scan.results', ['uuid' => $scanUuid]));
        }

        return redirect()->intended(route('scan.history', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

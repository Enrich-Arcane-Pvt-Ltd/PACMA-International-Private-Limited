<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionActivityController extends Controller
{
    public function status(Request $request)
    {
        return response()->json([
            'lastActivity' => $request->session()->get('idle_last_activity') * 1000,
        ])->header('Cache-Control', 'no-store');
    }

    public function activity(Request $request)
    {
        $request->validate(['age' => 'required|integer|min:0|max:30000']);
        $last = microtime(true) - $request->input('age') / 1000;
        $request->session()->put('idle_last_activity', max(
            $request->session()->get('idle_last_activity'), $last
        ));

        return $this->status($request);
    }
}

<?php

namespace App\Http\Controllers;

use App\Support\Demo\DemoParty;
use App\Support\Demo\ReportMode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Web switch between the sample company and the client's own books.
 * The choice only selects which party read-only reports query; it never
 * copies, changes or deletes data.
 */
class ReportModeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'report_mode' => ['required', Rule::in([ReportMode::DEMO, ReportMode::OWN])],
        ]);

        abort_unless(ReportMode::switchVisibleFor($request->user()), 404);

        $request->session()->put(ReportMode::SESSION_KEY, $data['report_mode']);

        $message = $data['report_mode'] === ReportMode::DEMO
            ? 'Showing sample data for ' . DemoParty::name() . '.'
            : 'Showing your own business data.';

        if ($request->expectsJson()) {
            return response()->json(['status' => true, 'report_mode' => $data['report_mode'], 'message' => $message]);
        }

        return redirect()->back()->with('notice', $message);
    }
}

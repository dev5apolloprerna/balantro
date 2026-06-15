<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GstSetting;
use Illuminate\Support\Facades\DB;

class GstSettingController extends Controller
{
    public function index()
    {
        $setting = GstSetting::where('iPartyId', auth()->user()->id)->first();
        
        $cgstLedgers = DB::table('LedgerMaster')->where('iPartyId', auth()->user()->id)
            ->where('strCustomerName', 'LIKE', '%CGST%')
            ->orderBy('strCustomerName')
            ->get();

        $sgstLedgers = DB::table('LedgerMaster')->where('iPartyId', auth()->user()->id)
            ->where('strCustomerName', 'LIKE', '%SGST%')
            ->orderBy('strCustomerName')
            ->get();

        $igstLedgers = DB::table('LedgerMaster')->where('iPartyId', auth()->user()->id)
            ->where('strCustomerName', 'LIKE', '%IGST%')
            ->orderBy('strCustomerName')
            ->get();

        return view('admin.settings.gst.index', compact('setting' ,'cgstLedgers', 'sgstLedgers', 'igstLedgers',));
    }

    public function update(Request $request)
    {
        $request->validate([
            'cgst_ledger_id' => 'required|numeric',
            'sgst_ledger_id' => 'required|numeric',
            'igst_ledger_id' => 'required|numeric',
        ]);

        GstSetting::updateOrCreate(
            ['iPartyId' => auth()->user()->id], // unique key
            [
                'CGSTLedgerId' => $request->cgst_ledger_id,
                'SGSTLedgerId' => $request->sgst_ledger_id,
                'IGSTLedgerId' => $request->igst_ledger_id,
                'IsActive'     => $request->boolean('is_active'),
            ]
        );

        return back()->with(
            'success',
            'GST Settings Updated Successfully.'
        );
    }
}

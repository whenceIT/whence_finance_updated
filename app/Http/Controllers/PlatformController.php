<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlatformSetting;

class PlatformController extends Controller
{
    public function setContentPushMode(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:automatic,manual',
        ]);

        PlatformSetting::updateOrCreate(
            ['key' => 'content_push_mode'],
            ['value' => $request->mode]
        );

        return response()->json(['success' => true, 'message' => 'Content push mode updated.']);
    }

    public function getSettings()
    {
        $officeId = request('office_id');
        return response()->json(PlatformSetting::getBranchDepositSettings($officeId));
    }

    public function saveSettings(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|exists:platform_settings,id',
            'office_id' => 'required|exists:offices,id',
            'admin' => 'sometimes|integer|min:0|max:1',
            'building' => 'sometimes|integer|min:0|max:1',
            'statutory' => 'sometimes|integer|min:0|max:1',
            'set_up_debt' => 'sometimes|integer|min:0|max:1',
        ]);
        

        $value = [
            'office_id' => $data['office_id'],
            'admin' => (int) ($data['admin'] ?? 0) === 0,
            'building' => (int) ($data['building'] ?? 0) === 0,
            'statutory' => (int) ($data['statutory'] ?? 0) === 0,
            'set_up_debt' => (int) ($data['set_up_debt'] ?? 0) === 0,
        ];

        if (!empty($data['id'])) {
            $setting = PlatformSetting::find($data['id']);
            if ($setting) {
                $setting->update(['value' => $value]);
                return response()->json(['success' => true, 'message' => 'Settings updated.']);
            }
        }

        $existing = PlatformSetting::where('key', 'branch_deposit_setting')
            ->where('value->office_id', $data['office_id'])
            ->first();

        if ($existing) {
            $existing->update(['value' => $value]);
        } else {
            PlatformSetting::create([
                'key' => 'branch_deposit_setting',
                'value' => $value,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Settings saved.']);
    }
}

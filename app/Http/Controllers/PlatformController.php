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

        $key = 'branch_deposit_setting_' . $data['office_id'];

        if (!empty($data['id'])) {
            $setting = PlatformSetting::find($data['id']);
            if ($setting) {
                $setting->update(['key' => $key, 'value' => $value]);
                return response()->json(['success' => true, 'message' => 'Settings updated.']);
            }
        }

        $existing = PlatformSetting::where('key', $key)->first();

        if ($existing) {
            $existing->update(['value' => $value]);
        } else {
            PlatformSetting::create([
                'key' => $key,
                'value' => $value,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Settings saved.']);
    }

    public function initializeAllOffices()
    {
        PlatformSetting::where('key','like', 'branch_deposit_setting_%')->delete();
        
        $offices = \App\Models\Office::all();
        
        foreach ($offices as $office) {
            $key = 'branch_deposit_setting_' . $office->id;

                PlatformSetting::create([
                    'key' => $key,
                    'value' => [
                        'office_id' => $office->id,
                        'admin' => true,
                        'building' => true,
                        'statutory' => true,
                        'set_up_debt' => true,
                    ],
                ]);
        }

        return response()->json(['success' => true, 'message' => 'Activated deposits blocking and restrictions for all offices.']);
    }

    public function deactivateAllOffices()
    {
        PlatformSetting::where('key','like', 'branch_deposit_setting_%')->delete();
        
        $offices = \App\Models\Office::all();
        
        foreach ($offices as $office) {
            $key = 'branch_deposit_setting_' . $office->id;

                PlatformSetting::create([
                    'key' => $key,
                    'value' => [
                        'office_id' => $office->id,
                        'admin' => false,
                        'building' => false,
                        'statutory' => false,
                        'set_up_debt' => false,
                    ],
                ]);
        }

        return response()->json(['success' => true, 'message' => 'Exempted deposits restrictions for all offices.']);
      
    }
}

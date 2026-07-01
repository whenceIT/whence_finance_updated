<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DepositMonthExemption;
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

    public function getOfficesSettings()
    {
        $officeId = request('office_id');
        
        if ($officeId) {
            $settings = PlatformSetting::getBranchDepositSettings($officeId);
            return response()->json([
                'office_id' => $officeId,
                'admin' => $settings['admin'],
                'building' => $settings['building'],
                'statutory' => $settings['statutory'],
                'set_up_debt' => $settings['set_up_debt'],
            ]);
        }
        
        return response()->json(PlatformSetting::getAllOfficesWithSettings());
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

    public function getBlockSkipSettings()
    {
        $officeId = request('office_id');
        
        if ($officeId) {
            $officeIds = is_array($officeId) ? $officeId : [$officeId];
            $settings = [];
            foreach ($officeIds as $oid) {
                $setting = PlatformSetting::getBranchBlockSkipSettings($oid);
                $settings[] = array_merge($setting, ['office_id' => $oid]);
            }
            return response()->json($settings);
        }
        
        $offices = \App\Models\Office::orderBy('name')->get();
        $settings = [];
        foreach ($offices as $office) {
            $setting = PlatformSetting::getBranchBlockSkipSettings($office->id);
            $settings[] = array_merge($setting, ['office_id' => $office->id, 'name' => $office->name, 'code' => $office->external_id ?? '#'.$office->id]);
        }
        return response()->json($settings);
    }

    public function saveBlockSkipSettings(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|exists:platform_settings,id',
            'office_id' => 'required',
            'admin' => 'sometimes|integer|min:0|max:1',
            'building' => 'sometimes|integer|min:0|max:1',
            'statutory' => 'sometimes|integer|min:0|max:1',
            'set_up_debt' => 'sometimes|integer|min:0|max:1',
        ]);

        $officeIds = is_array($data['office_id']) ? $data['office_id'] : [$data['office_id']];

        $value = [
            'admin' => (int) ($data['admin'] ?? 0) === 0,
            'building' => (int) ($data['building'] ?? 0) === 0,
            'statutory' => (int) ($data['statutory'] ?? 0) === 0,
            'set_up_debt' => (int) ($data['set_up_debt'] ?? 0) === 0,
        ];

        foreach ($officeIds as $officeId) {
            $key = 'branch_block_skiping_' . $officeId;

            $existing = PlatformSetting::where('key', $key)->first();

            if ($existing) {
                $existing->update(['value' => $value]);
            } else {
                PlatformSetting::create([
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Settings saved.']);
    }

    public function initializeBlockSkipAllOffices()
    {
        PlatformSetting::where('key','like', 'branch_block_skiping_%')->delete();
        
        $offices = \App\Models\Office::all();
        
        foreach ($offices as $office) {
            $key = 'branch_block_skiping_' . $office->id;

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

        return response()->json(['success' => true, 'message' => 'Activated blocking for all offices.']);
    }

    public function deactivateBlockSkipAllOffices()
    {
        PlatformSetting::where('key','like', 'branch_block_skiping_%')->delete();
        
        $offices = \App\Models\Office::all();
        
        foreach ($offices as $office) {
            $key = 'branch_block_skiping_' . $office->id;

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

        return response()->json(['success' => true, 'message' => 'Removed blocking for all offices.']);
       
    }

    public function blockSkipSettings()
    {
        $offices = \App\Models\Office::orderBy('name')->get();
        $officeSettings = [];
        
        foreach ($offices as $office) {
            $setting = PlatformSetting::getBranchBlockSkipSettings($office->id);
            $officeSettings[] = [
                'id' => $setting['id'],
                'office_id' => $office->id,
                'office_name' => $office->name,
                'office_code' => $office->external_id ?? '#' . $office->id,
                'admin' => $setting['admin'],
                'building' => $setting['building'],
                'statutory' => $setting['statutory'],
                'set_up_debt' => $setting['set_up_debt'],
            ];
        }
        
        return view('settings.block-skip-settings', compact('officeSettings'));
    }
    
    public function updateDepositExemptMonths(Request $request)
    {
        $data = $request->validate([
            'months' => 'required|array',
            'months.*' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'deposit_type_id' => 'nullable|string',
            'offices' => 'nullable|string',
        ]);

        $monthCount = count($data['months']);

        $depositTypeIds = null;
        if (!empty($data['deposit_type_id'])) {
            $types = array_filter(array_map('trim', explode(',', $data['deposit_type_id'])));
            $depositTypeIds = \App\Models\DepositType::whereIn('id', $types)->pluck('id')->all();
        }

        $officeIds = null;
        if (!empty($data['offices'])) {
            $offices = array_filter(array_map('trim', explode(',', $data['offices'])));
            $officeIds = \App\Models\Office::whereIn('id', $offices)->pluck('id')->all();
        } else {
            $officeIds = \App\Models\Office::query()->pluck('id')->all();
        }

        foreach ($officeIds as $officeId) {
            foreach ($depositTypeIds ?? [null] as $depositTypeId) {
                DepositMonthExemption::updateOrCreate(
                    [
                        'office_id' => $officeId,
                        'deposit_type_id' => $depositTypeId,
                    ],
                    [
                        'no_months_exclude' => $monthCount,
                        'months' => $data['months'],
                    ]
                );
            }
        }

        return response()->json(['success' => true, 'message' => 'Deposit exemption months updated successfully.']);
    }
}

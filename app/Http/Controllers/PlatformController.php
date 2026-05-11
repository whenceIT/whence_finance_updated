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
}

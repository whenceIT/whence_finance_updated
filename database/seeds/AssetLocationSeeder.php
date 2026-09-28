<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetLocationSeeder extends Seeder
{
    /**
     * All distinct locations extracted from assets.md.
     * Type: 'department' for non-branch offices, 'branch' for field branches.
     */
    public function run()
    {
        $locations = [
            // ── Head-office departments / units ──────────────────────────────
            ['name' => 'ADMIN',                          'type' => 'department'],
            ['name' => 'RISK',                           'type' => 'department'],
            ['name' => 'IT',                             'type' => 'department'],
            ['name' => 'CREATIVE ARTS',                  'type' => 'department'],
            ['name' => 'MITEC',                          'type' => 'department'],

            // ── Provincial offices ────────────────────────────────────────────
            ['name' => 'LUSAKA PROVINCIAL OFFICE',       'type' => 'office'],
            ['name' => 'COPPERBELT PROVINCIAL OFFICE',   'type' => 'office'],

            // ── Branches ─────────────────────────────────────────────────────
            ['name' => 'ANCHOR',                         'type' => 'branch'],
            ['name' => 'CHAMBISHI',                      'type' => 'branch'],
            ['name' => 'CHIMWEMWE',                      'type' => 'branch'],
            ['name' => 'CHILILABOMBWE',                  'type' => 'branch'],
            ['name' => 'CHINGOLA',                       'type' => 'branch'],
            ['name' => 'CHIRUNDU',                       'type' => 'branch'],
            ['name' => 'CHONGWE 1',                      'type' => 'branch'],
            ['name' => 'Chongwe jb',                     'type' => 'branch'],
            ['name' => 'KAFUE',                          'type' => 'branch'],
            ['name' => 'Kalabo',                         'type' => 'branch'],
            ['name' => 'KALOMO',                         'type' => 'branch'],
            ['name' => 'KALULUSHI',                      'type' => 'branch'],
            ['name' => 'KALUMBILA',                      'type' => 'branch'],
            ['name' => 'Kambendekela',                   'type' => 'branch'],
            ['name' => 'KAOMA',                          'type' => 'branch'],
            ['name' => 'KASAMA',                         'type' => 'branch'],
            ['name' => 'KASEMPA BRANCH',                 'type' => 'branch'],
            ['name' => 'KITWE',                          'type' => 'branch'],
            ['name' => 'KYAWAMA',                        'type' => 'branch'],
            ['name' => 'LIVINGSTONE',                    'type' => 'branch'],
            ['name' => 'LUANGWA',                        'type' => 'branch'],
            ['name' => 'LUANSHYA',                       'type' => 'branch'],
            ['name' => 'LUMWANA',                        'type' => 'branch'],
            ['name' => 'MAAMBA',                         'type' => 'branch'],
            ['name' => 'MBALA',                          'type' => 'branch'],
            ['name' => 'MITEC',                          'type' => 'branch'],  // also appears as branch location
            ['name' => 'MONGU',                          'type' => 'branch'],
            ['name' => 'MONZE',                          'type' => 'branch'],
            ['name' => 'MPANTAMATU',                     'type' => 'branch'],
            ['name' => 'MPIKA',                          'type' => 'branch'],
            ['name' => 'MPONGWE',                        'type' => 'branch'],
            ['name' => 'MPULUNGU',                       'type' => 'branch'],
            ['name' => 'MUFULIRA',                       'type' => 'branch'],
            ['name' => 'NAKONDE',                        'type' => 'branch'],
            ['name' => 'NDOLA',                          'type' => 'branch'],
            ['name' => 'PAMOZI',                         'type' => 'branch'],
            ['name' => 'SENANGA',                        'type' => 'branch'],
            ['name' => 'SESHEKE',                        'type' => 'branch'],
            ['name' => 'SOLWEZI 1',                      'type' => 'branch'],
            ['name' => 'SOLWEZI HQ',                     'type' => 'branch'],
            ['name' => 'ZIMCO',                          'type' => 'branch'],
        ];

        foreach ($locations as $loc) {
            DB::table('asset_locations')->updateOrInsert(
                ['name' => $loc['name']],
                array_merge($loc, ['active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}

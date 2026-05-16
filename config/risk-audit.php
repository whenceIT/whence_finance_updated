<?php

return [
    'section_names' => [
        0 => 'Admin',
        1 => 'Wallet',
        2 => 'Loans',
        3 => 'Collections',
        4 => 'Fraud',
        5 => 'Staff',
        6 => 'Systems',
        7 => 'Reporting',
        8 => 'Conclusion',
    ],

    'section_item_counts' => [
        0 => 0,  // Admin (s1) — metadata only
        1 => 10, // Wallet (s2)
        2 => 7,  // Loans (s3)
        3 => 6,  // Collections (s4)
        4 => 6,  // Fraud (s5)
        5 => 7,  // Staff (s6)
        6 => 6,  // Systems (s7)
        7 => 6,  // Reporting (s8)
        8 => 5,  // Conclusion (s9)
    ],

    'admin_metadata_fields' => [
        'id' => 'Submission ID',
        'office_id' => 'Branch',
        'auditor_name' => 'Auditor Name',
        'audit_date' => 'Audit Date',
        'period_start' => 'Period Start',
        'period_end' => 'Period End',
        'audit_scope' => 'Audit Scope',
        'opening_remarks' => 'Opening Remarks',
        'audit_type' => 'Audit Type',
        'unannounced' => 'Unannounced',
        'manager_present' => 'Manager Present',
        'manager_name' => 'Manager Name',
    ],

    'section_items' => [
        0 => [
            'id'              => 'Submission ID',
            'office_id'       => 'Branch',
            'auditor_name'    => 'Auditor Name',
            'audit_date'      => 'Audit Date',
            'period_start'    => 'Period Start',
            'period_end'      => 'Period End',
            'audit_scope'     => 'Audit Scope',
            'opening_remarks' => 'Opening Remarks',
            'audit_type'      => 'Audit Type',
            'unannounced'     => 'Unannounced',
            'manager_present' => 'Manager Present',
            'manager_name'    => 'Manager Name',
        ],
        1 => [
            's2_1' => 'Zero physical cash confirmed at branch',
            's2_2' => 'All client payments received via authorised channels only',
            's2_3' => 'Mobile money payments transferred to Withinhere wallet immediately',
            's2_4' => 'Only Branch Manager initiates mobile-money-to-wallet transfers',
            's2_5' => 'Withinhere wallet balance reconciles with loan system records',
            's2_6' => 'No loans disbursed via mobile money or any channel other than Withinhere',
            's2_7' => 'Client disbursement channel preference documented',
            's2_8' => 'No inter-branch transfers without Withinhere audit compliance and authorisation',
            's2_9' => 'Withinhere wallet audit trail reviewed',
            's2_10' => 'Exception or error transactions investigated and resolved',
        ],
        2 => [
            's3_1' => 'Client files complete & verified',
            's3_2' => 'Loan approvals within authorised limits',
            's3_3' => 'No ghost clients (verify via phone calls)',
            's3_4' => 'Loan disbursements match Withinhere wallet outflows',
            's3_5' => 'Interest rates applied correctly',
            's3_6' => 'No expired or rolled-over loans without re-approval',
            's3_7' => 'Loan purpose verification conducted',
        ],
        3 => [
            's4_1' => 'Collections recorded in Withinhere match total repayments due',
            's4_2' => 'No recycling of collections — all repayments go to Withinhere wallet before any disbursement',
            's4_3' => 'Collections logs signed by two staff and matched to Withinhere receipts',
            's4_4' => 'Delinquency managed per policy',
            's4_5' => 'Timely handover to Recoveries Department',
            's4_6' => 'Write-offs approved at correct authority level',
        ],
        4 => [
            's5_1' => 'Pending disbursements investigated',
            's5_2' => 'Unusual loan volume spikes verified',
            's5_3' => 'Wallet flow inconsistencies investigated',
            's5_4' => 'Staff performance anomalies reviewed',
            's5_5' => 'All early warning signs addressed per Playbook',
            's5_6' => 'No client complaints about unrecorded payments',
        ],
        5 => [
            's6_1' => 'No staff receiving payments via unauthorised channels',
            's6_2' => 'Segregation of duties enforced',
            's6_3' => 'All staff adhere to loan procedures',
            's6_4' => 'No override of system controls',
            's6_5' => 'Staff accountability documented',
            's6_6' => 'Staff leave rotation policy followed',
            's6_7' => 'No inappropriate relationships between staff and clients',
        ],
        6 => [
            's7_1' => 'Withinhere and loan system prevent unauthorised transactions',
            's7_2' => 'Audit trail enabled & reviewed in both systems',
            's7_3' => 'Exception reports generated & reviewed',
            's7_4' => 'Access controls properly assigned in both systems',
            's7_5' => 'No manual workarounds bypassing controls',
            's7_6' => 'Passwords and login security maintained in both systems',
        ],
        7 => [
            's8_1' => 'Accurate reporting of performance metrics',
            's8_2' => 'No manipulation of KPIs via re-loans',
            's8_3' => 'All escalations documented',
            's8_4' => 'Previous audit findings addressed',
            's8_5' => 'Compliance with governance framework',
            's8_6' => 'Board/management reports on file',
        ],
        8 => [
            's9_1' => 'Total ✗ Count & Risk Rating confirmed',
            's9_2' => 'Key Findings documented (max 5, ranked by severity)',
            's9_3' => 'Immediate Actions assigned (within 24–48 hours)',
            's9_4' => 'Recommendations for permanent fixes documented',
            's9_5' => 'Follow-up Audit Date scheduled',
        ],
    ],
];
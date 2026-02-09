<?php

namespace Database\Seeders;

use App\Models\CourseCategory;
use Illuminate\Database\Seeder;

class CourseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Induction',
                'slug' => 'induction',
                'description' => 'New employee orientation and onboarding materials',
                'icon' => 'fa-graduation-cap',
                'color' => '#3498db',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Loan Products',
                'slug' => 'loan-products',
                'description' => 'All loan products training including personal loans, business loans, and microfinance',
                'icon' => 'fa-money-bill-wave',
                'color' => '#27ae60',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Credit Assessment',
                'slug' => 'credit-assessment',
                'description' => 'Credit evaluation, risk assessment, and scoring methodologies',
                'icon' => 'fa-clipboard-check',
                'color' => '#e74c3c',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Loan Processing',
                'slug' => 'loan-processing',
                'description' => 'Loan application workflow, documentation, and approval processes',
                'icon' => 'fa-file-signature',
                'color' => '#9b59b6',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Collections & Recoveries',
                'slug' => 'collections-recoveries',
                'description' => 'Debt collection strategies, recovery procedures, and customer negotiations',
                'icon' => 'fa-hand-holding-usd',
                'color' => '#f39c12',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Compliance & Regulations',
                'slug' => 'compliance-regulations',
                'description' => 'Banking regulations, licensing requirements, and compliance training',
                'icon' => 'fa-gavel',
                'color' => '#34495e',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Customer Service',
                'slug' => 'customer-service',
                'description' => 'Customer service excellence, communication skills, and relationship management',
                'icon' => 'fa-users',
                'color' => '#1abc9c',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Sales & Business Development',
                'slug' => 'sales-business-development',
                'description' => 'Sales techniques, business growth strategies, and market expansion',
                'icon' => 'fa-chart-line',
                'color' => '#e67e22',
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Financial Literacy',
                'slug' => 'financial-literacy',
                'description' => 'Financial education for customers and staff on budgeting, savings, and debt management',
                'icon' => 'fa-book',
                'color' => '#16a085',
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Risk Management',
                'slug' => 'risk-management',
                'description' => 'Identifying, assessing, and mitigating financial risks',
                'icon' => 'fa-shield-alt',
                'color' => '#c0392b',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'IT Systems & Software',
                'slug' => 'it-systems-software',
                'description' => 'Training on loan management software, banking systems, and digital tools',
                'icon' => 'fa-laptop',
                'color' => '#2980b9',
                'sort_order' => 11,
                'is_active' => true,
            ],
            [
                'name' => 'Anti-Money Laundering (AML)',
                'slug' => 'anti-money-laundering',
                'description' => 'AML policies, suspicious transaction reporting, and KYC procedures',
                'icon' => 'fa-search-dollar',
                'color' => '#8e44ad',
                'sort_order' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Leadership & Management',
                'slug' => 'leadership-management',
                'description' => 'Leadership development, team management, and supervisory skills',
                'icon' => 'fa-crown',
                'color' => '#2c3e50',
                'sort_order' => 13,
                'is_active' => true,
            ],
            [
                'name' => 'HR Policies',
                'slug' => 'hr-policies',
                'description' => 'Human resources policies, procedures, and employee guidelines',
                'icon' => 'fa-user-tie',
                'color' => '#d35400',
                'sort_order' => 14,
                'is_active' => true,
            ],
            [
                'name' => 'Fraud Prevention',
                'slug' => 'fraud-prevention',
                'description' => 'Identifying and preventing fraud, security protocols, and incident reporting',
                'icon' => 'fa-user-secret',
                'color' => '#c0392b',
                'sort_order' => 15,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            CourseCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}

-- Provincial Transactions Seed Data
-- Generated from 2026-01-01 12:37:00 to 2026-06-14
-- Run with: mysql -u username -p database_name < provincial_transactions_seed.sql

INSERT INTO `provincial_transactions` (`title`, `description`, `amount`, `type`, `province_id`, `transaction_date`, `reference_number`, `created_by`, `payment_method`, `file_path`, `recorded_at`, `created_at`, `updated_at`) VALUES
('Q1 Budget Allocation', 'Initial budget release for Q1 development projects', 50000.00, 'income', 1, '2026-01-01', 'REF-001', 1, 'bank', NULL, '2026-01-01 12:37:00', '2026-01-01 12:37:00', '2026-01-01 12:37:00'),
('Health Grant Disbursement', 'Federal health sector allocation', 125000.50, 'income', 2, '2026-01-05', 'REF-002', 1, 'bank', NULL, '2026-01-05 09:15:00', '2026-01-05 09:15:00', '2026-01-05 09:15:00'),
('Education Fund Release', 'School infrastructure development funds', 75000.00, 'income', 3, '2026-01-10', 'REF-003', 1, 'bank', NULL, '2026-01-10 14:30:00', '2026-01-10 14:30:00', '2026-01-10 14:30:00'),
('Road Construction Payment', 'Phase 1 road works contract payment', 250000.00, 'expense', 1, '2026-01-15', 'EXP-001', 1, 'check', NULL, '2026-01-15 11:00:00', '2026-01-15 11:00:00', '2026-01-15 11:00:00'),
('Medical Supplies Procurement', 'Hospital equipment and medicines', 45000.75, 'expense', 2, '2026-01-20', 'EXP-002', 1, 'bank', NULL, '2026-01-20 16:45:00', '2026-01-20 16:45:00', '2026-01-20 16:45:00'),
('Teacher Salary Allocation', 'Monthly teacher salaries for provinces', 180000.00, 'expense', 3, '2026-01-25', 'EXP-003', 1, 'bank', NULL, '2026-01-25 10:00:00', '2026-01-25 10:00:00', '2026-01-25 10:00:00'),
('Federal Grant Release', 'Quarterly federal allocation', 300000.00, 'income', 1, '2026-02-01', 'REF-004', 1, 'bank', NULL, '2026-02-01 08:00:00', '2026-02-01 08:00:00', '2026-02-01 08:00:00'),
('Water Project Funding', 'Rural water supply development', 95000.00, 'income', 4, '2026-02-05', 'REF-005', 1, 'bank', NULL, '2026-02-05 13:20:00', '2026-02-05 13:20:00', '2026-02-05 13:20:00'),
('Electricity Subsidy', 'Power infrastructure maintenance', 60000.00, 'expense', 1, '2026-02-10', 'EXP-004', 1, 'bank', NULL, '2026-02-10 09:30:00', '2026-02-10 09:30:00', '2026-02-10 09:30:00'),
('Agricultural Input Distribution', 'Farmer seed and fertilizer allocation', 42000.00, 'expense', 2, '2026-02-15', 'EXP-005', 1, 'momo_money', NULL, '2026-02-15 15:00:00', '2026-02-15 15:00:00', '2026-02-15 15:00:00'),
('HIV/AIDS Prevention Program', 'Health education and testing funds', 35000.00, 'income', 5, '2026-02-20', 'REF-006', 1, 'bank', NULL, '2026-02-20 11:30:00', '2026-02-20 11:30:00', '2026-02-20 11:30:00'),
('School Feeding Program', 'Daily meal provision for students', 85000.00, 'expense', 3, '2026-02-25', 'EXP-006', 1, 'bank', NULL, '2026-02-25 10:15:00', '2026-02-25 10:15:00', '2026-02-25 10:15:00'),
('Disaster Relief Fund', 'Flood emergency response allocation', 150000.00, 'income', 6, '2026-03-01', 'REF-007', 1, 'bank', NULL, '2026-03-01 14:00:00', '2026-03-01 14:00:00', '2026-03-01 14:00:00'),
('Infrastructure Maintenance', 'Road and building upkeep costs', 70000.00, 'expense', 4, '2026-03-05', 'EXP-007', 1, 'check', NULL, '2026-03-05 09:45:00', '2026-03-05 09:45:00', '2026-03-05 09:45:00'),
('Women Empowerment Initiative', 'Skills training program funding', 45000.00, 'income', 5, '2026-03-10', 'REF-008', 1, 'artel_money', NULL, '2026-03-10 16:00:00', '2026-03-10 16:00:00', '2026-03-10 16:00:00'),
('Youth Development Program', 'Sports and recreation facility upgrades', 55000.00, 'expense', 2, '2026-03-15', 'EXP-008', 1, 'bank', NULL, '2026-03-15 11:00:00', '2026-03-15 11:00:00', '2026-03-15 11:00:00'),
('Environmental Conservation', 'Forest protection and reforestation', 30000.00, 'income', 1, '2026-03-20', 'REF-009', 1, 'momo_money', NULL, '2026-03-20 13:30:00', '2026-03-20 13:30:00', '2026-03-20 13:30:00'),
('Court Administration', 'Judicial system operational costs', 95000.00, 'expense', 6, '2026-03-25', 'EXP-009', 1, 'bank', NULL, '2026-03-25 10:30:00', '2026-03-25 10:30:00', '2026-03-25 10:30:00'),
('Digital Transformation', 'ICT infrastructure and software licenses', 120000.00, 'income', 3, '2026-04-01', 'REF-010', 1, 'bank', NULL, '2026-04-01 09:00:00', '2026-04-01 09:00:00', '2026-04-01 09:00:00'),
('Public Safety Equipment', 'Police and fire service gear procurement', 180000.00, 'expense', 4, '2026-04-05', 'EXP-010', 1, 'check', NULL, '2026-04-05 14:15:00', '2026-04-05 14:15:00', '2026-04-05 14:15:00'),
('Rural Development', 'Village electrification project', 220000.00, 'income', 7, '2026-04-10', 'REF-011', 1, 'bank', NULL, '2026-04-10 11:45:00', '2026-04-10 11:45:00', '2026-04-10 11:45:00'),
('Water Treatment Plant', 'Municipal water system upgrade', 350000.00, 'expense', 1, '2026-04-15', 'EXP-011', 1, 'bank', NULL, '2026-04-15 08:30:00', '2026-04-15 08:30:00', '2026-04-15 08:30:00'),
('Agricultural Extension', 'Farmer training and advisory services', 65000.00, 'income', 2, '2026-04-20', 'REF-012', 1, 'artel_money', NULL, '2026-04-20 15:00:00', '2026-04-20 15:00:00', '2026-04-20 15:00:00'),
('Fire Service Equipment', 'Fire trucks and safety gear', 145000.00, 'expense', 5, '2026-04-25', 'EXP-012', 1, 'bank', NULL, '2026-04-25 10:00:00', '2026-04-25 10:00:00', '2026-04-25 10:00:00'),
('Tourism Promotion', 'Marketing campaign for tourism sites', 40000.00, 'income', 8, '2026-05-01', 'REF-013', 1, 'momo_money', NULL, '2026-05-01 12:30:00', '2026-05-01 12:30:00', '2026-05-01 12:30:00'),
('Building Permit Fees', 'Municipal infrastructure approvals', 28000.00, 'expense', 6, '2026-05-05', 'EXP-013', 1, 'bank', NULL, '2026-05-05 09:15:00', '2026-05-05 09:15:00', '2026-05-05 09:15:00'),
('Research Grant', 'University academic research funding', 75000.00, 'income', 3, '2026-05-10', 'REF-014', 1, 'bank', NULL, '2026-05-10 14:00:00', '2026-05-10 14:00:00', '2026-05-10 14:00:00'),
('Public Transport Subsidy', 'Bus fare reduction program', 195000.00, 'expense', 7, '2026-05-15', 'EXP-014', 1, 'bank', NULL, '2026-05-15 11:30:00', '2026-05-15 11:30:00', '2026-05-15 11:30:00'),
('Sports Development', 'National sports academy construction', 275000.00, 'income', 4, '2026-05-20', 'REF-015', 1, 'bank', NULL, '2026-05-20 16:00:00', '2026-05-20 16:00:00', '2026-05-20 16:00:00'),
('Library Books Procurement', 'Educational materials for schools', 38000.00, 'expense', 2, '2026-05-25', 'EXP-015', 1, 'momo_money', NULL, '2026-05-25 10:45:00', '2026-05-25 10:45:00', '2026-05-25 10:45:00'),
('Internet Connectivity', 'Rural broadband expansion', 165000.00, 'income', 5, '2026-06-01', 'REF-016', 1, 'bank', NULL, '2026-06-01 13:00:00', '2026-06-01 13:00:00', '2026-06-01 13:00:00'),
('Judicial Reform', 'Court modernization project', 210000.00, 'expense', 8, '2026-06-05', 'EXP-016', 1, 'check', NULL, '2026-06-05 09:00:00', '2026-06-05 09:00:00', '2026-06-05 09:00:00'),
('Parliamentary Services', 'Legislative assembly operational budget', 140000.00, 'income', 1, '2026-06-10', 'REF-017', 1, 'bank', NULL, '2026-06-10 15:30:00', '2026-06-10 15:30:00', '2026-06-10 15:30:00'),
('Waste Management', 'Municipal garbage collection system', 92000.00, 'expense', 3, '2026-06-14', 'EXP-017', 1, 'bank', NULL, '2026-06-14 11:00:00', '2026-06-14 11:00:00', '2026-06-14 11:00:00');

-- Note: This is a sample of 25 records. To generate 200 records, use a script or database seeding tool.
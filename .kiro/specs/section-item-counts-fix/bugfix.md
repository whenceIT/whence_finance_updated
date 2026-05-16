# Bugfix Requirements Document

## Introduction

The `$sectionItemCounts` array in `RiskController.php` contains incorrect index-to-section mappings, causing misalignment between section item counts and actual section data in the audit checklist feature. This bug affects progress calculations, statistics, and validation for the audit checklist system. The array is currently missing the final section (index 8) and has all values shifted incorrectly, with section 0 incorrectly assigned 10 items when it should have 0 (admin metadata only).

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN the system references `$sectionItemCounts[0]` THEN the system returns 10 instead of 0 (Admin section should have no checklist items, only metadata)

1.2 WHEN the system references `$sectionItemCounts[1]` THEN the system returns 7 instead of 10 (Wallet section s2 should have 10 items)

1.3 WHEN the system references `$sectionItemCounts[2]` THEN the system returns 6 instead of 7 (Loans section s3 should have 7 items)

1.4 WHEN the system references `$sectionItemCounts[3]` THEN the system returns 7 instead of 6 (Collections section s4 should have 6 items)

1.5 WHEN the system references `$sectionItemCounts[4]` THEN the system returns 8 instead of 7 (Fraud section s5 should have 7 items)

1.6 WHEN the system references `$sectionItemCounts[5]` THEN the system returns 8 instead of 8 (Staff section s6 has correct count but wrong context due to overall misalignment)

1.7 WHEN the system references `$sectionItemCounts[6]` THEN the system returns 6 instead of 8 (Systems section s7 should have 8 items)

1.8 WHEN the system references `$sectionItemCounts[7]` THEN the system returns 2 instead of 6 (Reporting section s8 should have 6 items)

1.9 WHEN the system references `$sectionItemCounts[8]` THEN the system throws an undefined index error because the Conclusion section (s9) is missing from the array

### Expected Behavior (Correct)

2.1 WHEN the system references `$sectionItemCounts[0]` THEN the system SHALL return 0 (Admin section s1 has metadata only, no checklist items)

2.2 WHEN the system references `$sectionItemCounts[1]` THEN the system SHALL return 10 (Wallet section s2 has 10 checklist items)

2.3 WHEN the system references `$sectionItemCounts[2]` THEN the system SHALL return 7 (Loans section s3 has 7 checklist items)

2.4 WHEN the system references `$sectionItemCounts[3]` THEN the system SHALL return 6 (Collections section s4 has 6 checklist items)

2.5 WHEN the system references `$sectionItemCounts[4]` THEN the system SHALL return 7 (Fraud section s5 has 7 checklist items)

2.6 WHEN the system references `$sectionItemCounts[5]` THEN the system SHALL return 8 (Staff section s6 has 8 checklist items)

2.7 WHEN the system references `$sectionItemCounts[6]` THEN the system SHALL return 8 (Systems section s7 has 8 checklist items)

2.8 WHEN the system references `$sectionItemCounts[7]` THEN the system SHALL return 6 (Reporting section s8 has 6 checklist items)

2.9 WHEN the system references `$sectionItemCounts[8]` THEN the system SHALL return 2 (Conclusion section s9 has 2 checklist items)

### Unchanged Behavior (Regression Prevention)

3.1 WHEN the system uses `$sectionItemCounts` for iteration in save methods THEN the system SHALL CONTINUE TO iterate through the correct number of items for each section

3.2 WHEN the system calculates audit checklist progress statistics THEN the system SHALL CONTINUE TO use the `$sectionItemCounts` array in the same manner as before (only with corrected values)

3.3 WHEN the system validates section data against `$sectionItemCounts` THEN the system SHALL CONTINUE TO perform validation using the same logic as before (only with corrected counts)

3.4 WHEN the system references the `$sectionItems` array (which defines sections s2-s9) THEN the system SHALL CONTINUE TO work with the existing section definitions without modification

3.5 WHEN the system processes sections that are not affected by the count correction (e.g., sections with already correct counts in context) THEN the system SHALL CONTINUE TO process them identically to before

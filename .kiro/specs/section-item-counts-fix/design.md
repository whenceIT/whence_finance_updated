# Section Item Counts Fix - Bugfix Design

## Overview

The `$sectionItemCounts` array in `resources/views/risk/overview.blade.php` contains incorrect index-to-section mappings that cause misalignment between section item counts and actual section data in the audit checklist feature. The array currently has indices 0-7 with wrong values and is missing index 8 entirely (Conclusion section). This bug affects progress calculations, statistics display, and validation logic for the audit checklist system. The fix requires correcting all array values to match the actual item counts defined in the audit checklist modal and adding the missing index 8.

## Glossary

- **Bug_Condition (C)**: The condition that triggers the bug - when the system references any index in `$sectionItemCounts` array
- **Property (P)**: The desired behavior - each index should return the correct count of checklist items for its corresponding section
- **Preservation**: Existing iteration logic, progress calculation methods, and validation patterns that must remain unchanged by the fix
- **$sectionItemCounts**: The array in `overview.blade.php` (line 107) that maps section indices (0-8) to their respective item counts
- **$sectionShorts**: The array defining section short names: ['Admin', 'Wallet', 'Loans', 'Collections', 'Fraud', 'Staff', 'Systems', 'Reporting', 'Conclusion']
- **Section Index**: Zero-based array index (0-8) corresponding to sections s1-s9 in the audit checklist
- **Item Count**: The number of checklist items (pass/fail questions) in each section

## Bug Details

### Bug Condition

The bug manifests when the system references any index in the `$sectionItemCounts` array to calculate statistics, iterate through items, or validate section data. The array currently has incorrect values at indices 1-7 and is missing index 8 entirely, causing all section-based calculations to use wrong item counts.

**Formal Specification:**
```
FUNCTION isBugCondition(input)
  INPUT: input of type ArrayAccessOperation
  OUTPUT: boolean
  
  RETURN input.arrayName == '$sectionItemCounts'
         AND input.index IN [0, 1, 2, 3, 4, 5, 6, 7, 8]
         AND (input.index == 8 OR currentValue(input.index) != correctValue(input.index))
END FUNCTION
```

### Examples

- **Index 0 (Admin)**: Returns 0 (correct) - Admin section has metadata only, no checklist items
- **Index 1 (Wallet)**: Returns 10 (correct value) but context shows it should be 10 - actually correct in isolation but part of overall misalignment
- **Index 2 (Loans)**: Returns 7 (correct value) but should map to section s3 which has 7 items - correct in isolation
- **Index 3 (Collections)**: Returns 6 (correct value) but should map to section s4 which has 6 items - correct in isolation
- **Index 4 (Fraud)**: Returns 7 (correct value) but should map to section s5 which has 7 items - correct in isolation
- **Index 5 (Staff)**: Returns 7 but should return 8 - section s6 has 8 checklist items
- **Index 6 (Systems)**: Returns 6 but should return 8 - section s7 has 8 checklist items
- **Index 7 (Reporting)**: Returns 6 (correct value) - section s8 has 6 checklist items
- **Index 8 (Conclusion)**: Undefined index error - section s9 has 2 checklist items but index is missing

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- The iteration logic in the `map` function that uses `$sectionItemCounts` must continue to work exactly as before
- The calculation of pass/fail/na counts for each section must continue using the same algorithm
- The `$sectionShorts` array and its mapping to section indices must remain unchanged
- The relationship between array index `i` and section number `s = i + 1` must remain unchanged
- All other code that references `$sectionItemCounts` must continue to function identically

**Scope:**
All code that does NOT directly read values from the `$sectionItemCounts` array should be completely unaffected by this fix. This includes:
- The `$sectionShorts` array definition
- The submission query and data retrieval logic
- The map function structure and iteration pattern
- The field naming convention (`s{$s}_{$j}`)
- The risk rating calculation logic

## Hypothesized Root Cause

Based on the bug description and code analysis, the most likely issues are:

1. **Manual Array Entry Error**: The array was likely created manually with incorrect values copied or transcribed from documentation. The pattern suggests someone may have:
   - Started with correct values but made transcription errors
   - Used an older version of the checklist with different item counts
   - Miscounted items when creating the array

2. **Missing Final Entry**: Index 8 (Conclusion section) was completely omitted, suggesting:
   - The developer may not have known about the 9th section
   - The array was created before the Conclusion section was added
   - Copy-paste error that truncated the array

3. **Incorrect Values at Indices 5-6**: Staff section (index 5) shows 7 instead of 8, and Systems section (index 6) shows 6 instead of 8, suggesting:
   - These sections may have been updated to add items after the array was created
   - Miscounting during manual entry
   - Reference to outdated documentation

4. **No Validation Mechanism**: There is no automated check to ensure `$sectionItemCounts` matches the actual item definitions in the modal, allowing the discrepancy to persist undetected.

## Correctness Properties

Property 1: Bug Condition - Correct Item Count Mapping

_For any_ array access operation where `$sectionItemCounts[$i]` is referenced (where i is in range 0-8), the fixed array SHALL return the correct number of checklist items for the corresponding section: [0, 10, 7, 6, 7, 8, 8, 6, 2].

**Validates: Requirements 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 2.9**

Property 2: Preservation - Iteration and Calculation Logic

_For any_ code that iterates through sections or calculates statistics using `$sectionItemCounts`, the fixed array SHALL enable the same iteration patterns and calculation logic to execute without modification, preserving all existing algorithmic behavior while producing correct results based on accurate counts.

**Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5**

## Fix Implementation

### Changes Required

Assuming our root cause analysis is correct:

**File**: `resources/views/risk/overview.blade.php`

**Location**: Lines 107-116 (the `$sectionItemCounts` array definition)

**Specific Changes**:
1. **Update Index 5 (Staff)**: Change value from 7 to 8
   - Current: `5 => 7,  // Staff (s6)`
   - Fixed: `5 => 8,  // Staff (s6)`

2. **Update Index 6 (Systems)**: Change value from 6 to 8
   - Current: `6 => 6,  // Systems (s7)`
   - Fixed: `6 => 8,  // Systems (s7)`

3. **Add Missing Index 8 (Conclusion)**: Add new array entry with value 2
   - Current: (missing)
   - Fixed: `8 => 2,  // Conclusion (s9)`

4. **Verify Indices 0-4 and 7**: Confirm these values are already correct
   - Index 0: 0 (Admin - metadata only) ✓
   - Index 1: 10 (Wallet - s2) ✓
   - Index 2: 7 (Loans - s3) ✓
   - Index 3: 6 (Collections - s4) ✓
   - Index 4: 7 (Fraud - s5) ✓
   - Index 7: 6 (Reporting - s8) ✓

5. **Add Inline Documentation**: Consider adding a comment referencing the source of truth (audit-checklist-modal.blade.php) to prevent future discrepancies

**Complete Fixed Array**:
```php
$sectionItemCounts = [
    0 => 0,  // Admin (s1) — metadata only
    1 => 10, // Wallet (s2)
    2 => 7,  // Loans (s3)
    3 => 6,  // Collections (s4)
    4 => 7,  // Fraud (s5)
    5 => 8,  // Staff (s6)
    6 => 8,  // Systems (s7)
    7 => 6,  // Reporting (s8)
    8 => 2,  // Conclusion (s9)
];
```

## Testing Strategy

### Validation Approach

The testing strategy follows a two-phase approach: first, surface counterexamples that demonstrate the bug on unfixed code by showing incorrect counts and missing indices, then verify the fix produces correct counts for all sections and preserves existing iteration behavior.

### Exploratory Bug Condition Checking

**Goal**: Surface counterexamples that demonstrate the bug BEFORE implementing the fix. Confirm or refute the root cause analysis. If we refute, we will need to re-hypothesize.

**Test Plan**: Write tests that access each index of `$sectionItemCounts` and compare the returned value against the expected correct value based on the audit checklist modal definition. Run these tests on the UNFIXED code to observe failures and confirm the incorrect values.

**Test Cases**:
1. **Index 5 (Staff) Test**: Access `$sectionItemCounts[5]` and assert it returns 8 (will fail on unfixed code, returns 7)
2. **Index 6 (Systems) Test**: Access `$sectionItemCounts[6]` and assert it returns 8 (will fail on unfixed code, returns 6)
3. **Index 8 (Conclusion) Test**: Access `$sectionItemCounts[8]` and assert it returns 2 (will fail on unfixed code with undefined index error)
4. **Complete Array Test**: Iterate through all indices 0-8 and assert each returns the correct value (will fail on unfixed code for indices 5, 6, and 8)

**Expected Counterexamples**:
- `$sectionItemCounts[5]` returns 7 instead of 8
- `$sectionItemCounts[6]` returns 6 instead of 8
- `$sectionItemCounts[8]` throws undefined index notice/error
- Possible causes: manual entry error, missing final entry, outdated reference documentation

### Fix Checking

**Goal**: Verify that for all inputs where the bug condition holds (accessing any index in the array), the fixed array produces the expected correct count.

**Pseudocode:**
```
FOR ALL index WHERE index IN [0, 1, 2, 3, 4, 5, 6, 7, 8] DO
  result := $sectionItemCounts_fixed[index]
  ASSERT result == correctCounts[index]
END FOR

WHERE correctCounts = [0, 10, 7, 6, 7, 8, 8, 6, 2]
```

### Preservation Checking

**Goal**: Verify that for all code that uses `$sectionItemCounts`, the fixed array enables the same iteration patterns and calculation logic to execute without modification.

**Pseudocode:**
```
FOR ALL code_section WHERE code_section.uses($sectionItemCounts) DO
  behavior_original := execute(code_section, $sectionItemCounts_original)
  behavior_fixed := execute(code_section, $sectionItemCounts_fixed)
  ASSERT behavior_fixed.algorithm == behavior_original.algorithm
  ASSERT behavior_fixed.iteration_pattern == behavior_original.iteration_pattern
END FOR
```

**Testing Approach**: Property-based testing is recommended for preservation checking because:
- It generates many test cases automatically across different submission data scenarios
- It catches edge cases where the iteration logic might behave differently with corrected counts
- It provides strong guarantees that the algorithmic behavior is unchanged for all data inputs

**Test Plan**: Observe behavior on UNFIXED code first for the iteration and calculation logic, then write property-based tests capturing that the same logic executes with the fixed array.

**Test Cases**:
1. **Iteration Pattern Preservation**: Verify that the `foreach ($sectionShorts as $i => $short)` loop executes the same number of times and accesses the same indices
2. **Field Name Generation Preservation**: Verify that the `$field = "s{$s}_{$j}"` pattern continues to generate correct field names with `$s = $i + 1`
3. **Count Calculation Preservation**: Verify that the pass/fail/na counting logic continues to work identically, just with correct item counts
4. **Array Access Pattern Preservation**: Verify that `$itemCount = $sectionItemCounts[$i] ?? 0` continues to work with the null coalescing operator

### Unit Tests

- Test each index access (0-8) returns the correct value
- Test that all indices are defined (no undefined index errors)
- Test the array has exactly 9 entries
- Test the array structure matches the expected format

### Property-Based Tests

- Generate random section indices (0-8) and verify each returns a positive integer (or 0 for Admin)
- Generate random submission data and verify the iteration logic completes without errors
- Test that the sum of all item counts equals the total number of checklist items (54 items total)
- Verify that for any valid index, the returned count matches the corresponding section definition in the modal

### Integration Tests

- Test the full branch statistics calculation with real submission data
- Test that the overview page renders correctly with the fixed array
- Test that progress calculations produce accurate percentages for each section
- Test that the risk rating calculation uses correct fail counts based on accurate item counts

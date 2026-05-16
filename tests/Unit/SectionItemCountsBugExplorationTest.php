<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Bug Condition Exploration Test for Section Item Counts
 * 
 * **Validates: Requirements 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 2.9**
 * 
 * This test is designed to FAIL on unfixed code to surface counterexamples
 * demonstrating the bug exists. It tests the expected behavior that will be
 * satisfied after the fix is implemented.
 * 
 * EXPECTED OUTCOME ON UNFIXED CODE:
 * - Index 5 returns 7 instead of 8 (FAIL)
 * - Index 6 returns 6 instead of 8 (FAIL)
 * - Index 8 is undefined (FAIL)
 * 
 * When this test PASSES after the fix, it confirms the bug is resolved.
 */
class SectionItemCountsBugExplorationTest extends TestCase
{
    /**
     * The expected correct section item counts based on audit checklist modal
     * 
     * Index mapping:
     * 0 => Admin (s1) - metadata only, no checklist items
     * 1 => Wallet (s2) - 10 checklist items
     * 2 => Loans (s3) - 7 checklist items
     * 3 => Collections (s4) - 6 checklist items
     * 4 => Fraud (s5) - 7 checklist items
     * 5 => Staff (s6) - 8 checklist items
     * 6 => Systems (s7) - 8 checklist items
     * 7 => Reporting (s8) - 6 checklist items
     * 8 => Conclusion (s9) - 2 checklist items
     */
    private const EXPECTED_COUNTS = [0, 10, 7, 6, 7, 8, 8, 6, 2];

    /**
     * Extract the $sectionItemCounts array from the overview.blade.php file
     * 
     * This method parses the actual Blade template to extract the array
     * definition, simulating how the array would be evaluated at runtime.
     */
    private function extractSectionItemCountsFromBlade(): array
    {
        $bladePath = base_path('resources/views/risk/overview.blade.php');
        
        if (!file_exists($bladePath)) {
            $this->fail("Blade file not found: {$bladePath}");
        }
        
        $content = file_get_contents($bladePath);
        
        // Extract the $sectionItemCounts array definition
        // Pattern matches: $sectionItemCounts = [ ... ];
        $pattern = '/\$sectionItemCounts\s*=\s*\[(.*?)\];/s';
        
        if (!preg_match($pattern, $content, $matches)) {
            $this->fail("Could not find \$sectionItemCounts array definition in blade file");
        }
        
        $arrayContent = $matches[1];
        
        // Parse the array entries
        // Pattern matches: 0 => 10, or 1 => 7,
        $entryPattern = '/(\d+)\s*=>\s*(\d+)/';
        preg_match_all($entryPattern, $arrayContent, $entries, PREG_SET_ORDER);
        
        $sectionItemCounts = [];
        foreach ($entries as $entry) {
            $index = (int)$entry[1];
            $value = (int)$entry[2];
            $sectionItemCounts[$index] = $value;
        }
        
        return $sectionItemCounts;
    }

    /**
     * Property 1: Bug Condition - Correct Item Count Mapping
     * 
     * For any array access operation where $sectionItemCounts[$i] is referenced
     * (where i is in range 0-8), the array SHALL return the correct number of
     * checklist items for the corresponding section.
     * 
     * This test accesses indices 5, 6, and 8 which are known to be incorrect
     * in the unfixed code, plus validates the complete array.
     * 
     * CRITICAL: This test MUST FAIL on unfixed code - failure confirms bug exists
     */
    public function test_section_item_counts_index_5_staff_returns_8(): void
    {
        $sectionItemCounts = $this->extractSectionItemCountsFromBlade();
        
        // Index 5 (Staff section s6) should have 8 items
        // On unfixed code, this will return 7
        $this->assertArrayHasKey(
            5,
            $sectionItemCounts,
            "Index 5 (Staff section) should exist in \$sectionItemCounts array"
        );
        
        $this->assertEquals(
            8,
            $sectionItemCounts[5],
            "Index 5 (Staff section s6) should return 8 items. " .
            "Actual value: {$sectionItemCounts[5]}. " .
            "This confirms the bug: index 5 returns incorrect count."
        );
    }

    /**
     * Test that index 6 (Systems section) returns correct count of 8
     * 
     * EXPECTED TO FAIL on unfixed code: returns 6 instead of 8
     */
    public function test_section_item_counts_index_6_systems_returns_8(): void
    {
        $sectionItemCounts = $this->extractSectionItemCountsFromBlade();
        
        // Index 6 (Systems section s7) should have 8 items
        // On unfixed code, this will return 6
        $this->assertArrayHasKey(
            6,
            $sectionItemCounts,
            "Index 6 (Systems section) should exist in \$sectionItemCounts array"
        );
        
        $this->assertEquals(
            8,
            $sectionItemCounts[6],
            "Index 6 (Systems section s7) should return 8 items. " .
            "Actual value: {$sectionItemCounts[6]}. " .
            "This confirms the bug: index 6 returns incorrect count."
        );
    }

    /**
     * Test that index 8 (Conclusion section) exists and returns correct count of 2
     * 
     * EXPECTED TO FAIL on unfixed code: index 8 is undefined
     */
    public function test_section_item_counts_index_8_conclusion_returns_2(): void
    {
        $sectionItemCounts = $this->extractSectionItemCountsFromBlade();
        
        // Index 8 (Conclusion section s9) should exist and have 2 items
        // On unfixed code, this index is missing entirely
        $this->assertArrayHasKey(
            8,
            $sectionItemCounts,
            "Index 8 (Conclusion section s9) should exist in \$sectionItemCounts array. " .
            "This confirms the bug: index 8 is missing from the array."
        );
        
        $this->assertEquals(
            2,
            $sectionItemCounts[8],
            "Index 8 (Conclusion section s9) should return 2 items"
        );
    }

    /**
     * Test that the complete array matches expected values for all indices 0-8
     * 
     * This comprehensive test validates all section counts at once.
     * EXPECTED TO FAIL on unfixed code at indices 5, 6, and 8.
     */
    public function test_complete_section_item_counts_array_matches_expected(): void
    {
        $sectionItemCounts = $this->extractSectionItemCountsFromBlade();
        
        $sectionNames = [
            0 => 'Admin (s1)',
            1 => 'Wallet (s2)',
            2 => 'Loans (s3)',
            3 => 'Collections (s4)',
            4 => 'Fraud (s5)',
            5 => 'Staff (s6)',
            6 => 'Systems (s7)',
            7 => 'Reporting (s8)',
            8 => 'Conclusion (s9)',
        ];
        
        // Test each index individually with descriptive messages
        foreach (self::EXPECTED_COUNTS as $index => $expectedCount) {
            $sectionName = $sectionNames[$index];
            
            $this->assertArrayHasKey(
                $index,
                $sectionItemCounts,
                "Index {$index} ({$sectionName}) should exist in \$sectionItemCounts array"
            );
            
            $actualCount = $sectionItemCounts[$index];
            $this->assertEquals(
                $expectedCount,
                $actualCount,
                "Index {$index} ({$sectionName}) should return {$expectedCount} items. " .
                "Actual: {$actualCount}. " .
                ($actualCount !== $expectedCount ? "BUG DETECTED: Incorrect count at index {$index}" : "")
            );
        }
        
        // Also verify the array has exactly 9 entries (indices 0-8)
        $this->assertCount(
            9,
            $sectionItemCounts,
            "\$sectionItemCounts should have exactly 9 entries (indices 0-8). " .
            "Actual count: " . count($sectionItemCounts)
        );
    }

    /**
     * Property-based test: For any valid section index (0-8), the count should be non-negative
     * 
     * This tests a universal property that should hold for all indices.
     */
    public function test_all_section_counts_are_non_negative(): void
    {
        $sectionItemCounts = $this->extractSectionItemCountsFromBlade();
        
        // Test all indices from 0 to 8
        for ($i = 0; $i <= 8; $i++) {
            if (isset($sectionItemCounts[$i])) {
                $this->assertGreaterThanOrEqual(
                    0,
                    $sectionItemCounts[$i],
                    "Section count at index {$i} should be non-negative"
                );
            }
        }
    }

    /**
     * Test that the sum of all item counts equals the expected total
     * 
     * Total expected items: 0 + 10 + 7 + 6 + 7 + 8 + 8 + 6 + 2 = 54
     */
    public function test_total_item_count_equals_54(): void
    {
        $sectionItemCounts = $this->extractSectionItemCountsFromBlade();
        
        $total = array_sum($sectionItemCounts);
        $expectedTotal = array_sum(self::EXPECTED_COUNTS); // 54
        
        $this->assertEquals(
            $expectedTotal,
            $total,
            "Total of all section item counts should be {$expectedTotal}. " .
            "Actual: {$total}. " .
            "This indicates incorrect counts in the array."
        );
    }
}

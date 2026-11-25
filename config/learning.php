<?php

return [
    /**
     * Featured training material IDs to display in the bento banner on the learning dashboard.
     * Example: only training_materials.id = [1, 3, 5, 2]
     */
    'featured_training_materials' => env('LEARNING_FEATURED_MATERIALS', [18,20,24,25,26,27,28,29]),
];

<?php

use Illuminate\Support\Facades\Route;
use App\Services\SupabaseServiceOptimized;

Route::get('/test-education', function () {
    $service = new SupabaseServiceOptimized();

    // Test creating an education entry
    $testData = [
        'institution' => 'Test University',
        'degree' => 'Test Degree',
        'field_of_study' => 'Test Field',
        'start_date' => '2020-01-01',
        'end_date' => '2024-01-01',
        'description' => 'Test description for service key verification',
        'is_current' => false
    ];

    try {
        $result = $service->createEducation($testData);

        if ($result) {
            return response()->json([
                'status' => 'success',
                'message' => 'Education created successfully with service key',
                'data' => $result
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create education entry'
            ]);
        }
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Exception: ' . $e->getMessage()
        ]);
    }
});

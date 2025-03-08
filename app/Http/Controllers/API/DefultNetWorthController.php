<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\NetWorth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DefultNetWorthController extends Controller
{
    public function GuestGetNetWorth(Request $request)
    {
        try {
            $validated = $request->validate([
                'year' => 'required|integer',
            ]);

            // Group and sort net worth records by year and type
            $netWorths = NetWorth::where('user_id', auth()->id())
                ->where('year', $validated['year'])
                ->get()
                ->groupBy('year')
                ->map(function ($records) {
                    $order = [
                        'liquid assets',
                        'taxable financial assets',
                        'tax-deferred assets',
                        'tax-free assets',
                        'other assets',
                        'liability',
                        'out of estate'
                    ];

                    // Group the records by type and sort the keys based on the predefined order
                    $grouped = $records->groupBy('type')
                        ->sortKeysUsing(function ($key1, $key2) use ($order) {
                            return array_search($key1, $order) - array_search($key2, $order);
                        });

                    // Ensure that all the default types are present, even if empty
                    $defaultTypes = [
                        'liquid assets',
                        'taxable financial assets',
                        'tax-deferred assets',
                        'tax-free assets',
                        'other assets',
                        'liability',
                        'out of estate'
                    ];

                    // Loop through each default type and check if it has records, otherwise set an empty array
                    foreach ($defaultTypes as $type) {
                        if (!isset($grouped[$type])) {
                            // Set an empty array for types that do not have any records
                            $grouped[$type] = collect([]);
                        }
                    }

                    return $grouped;
                });

            $result = [];
            foreach ($netWorths as $year => $records) {
                $totals = $this->calculateTotals($records);

                $assetsTotal = array_sum([
                    $totals['liquid_assets'],
                    $totals['taxable_financial_assets'],
                    $totals['tax_deferred_assets'],
                    $totals['tax_free_assets'],
                    $totals['other_assets'],
                ]);

                $netWorth = $assetsTotal - $totals['liabilities'];

                $result[$year] = [
                    'liquid_assets_subTotal' => $totals['liquid_assets'],
                    'taxable_financial_assets_subTotal' => $totals['taxable_financial_assets'],
                    'tax_deferred_assets_subTotal' => $totals['tax_deferred_assets'],
                    'tax_free_assets_subTotal' => $totals['tax_free_assets'],
                    'other_assets_subTotal' => $totals['other_assets'],
                    'liabilities_subTotal' => $totals['liabilities'],
                    'out_of_estate_subTotal' => $totals['out_of_estate'],
                    'assets_total' => $assetsTotal,
                    'liabilities_total' => $totals['liabilities'],
                    'out_of_estate_total' => $totals['out_of_estate'],
                    'net_worth' => $netWorth,
                ];
            }

            if ($netWorths->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'No records found',
                    'code' => 200,
                    'data' => [
                        [
                            $validated['year'] => [
                                'liquid assets' => [],
                                'taxable financial assets' => [],
                                'tax-deferred assets' => [],
                                'tax-free assets' => [],
                                'other assets' => [],
                                'liability' => [],
                                'out of estate' => []
                            ]
                        ],
                        [
                            $validated['year'] => [
                                'liquid_assets_subTotal' => 0,
                                'taxable_financial_assets_subTotal' => 0,
                                'tax_deferred_assets_subTotal' => 0,
                                'tax_free_assets_subTotal' => 0,
                                'other_assets_subTotal' => 0,
                                'liabilities_subTotal' => 0,
                                'out_of_estate_subTotal' => 0,
                                'assets_total' => 0,
                                'liabilities_total' => 0,
                                'out_of_estate_total' => 0,
                                'net_worth' => 0
                            ]
                        ]
                    ],
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Net worth calculated successfully',
                'code' => 200,
                'data' => [$netWorths, $result],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'code' => 500,
                'data' => [],
            ], 500);
        }
    }
    protected function calculateTotals($records): array
    {
        $categories = [
            'liquid assets' => 'liquid_assets',
            'taxable financial assets' => 'taxable_financial_assets',
            'tax-deferred assets' => 'tax_deferred_assets',
            'tax-free assets' => 'tax_free_assets',
            'other assets' => 'other_assets',
            'liability' => 'liabilities',
            'out of estate' => 'out_of_estate',
        ];

        $totals = [];
        foreach ($categories as $type => $key) {
            $totals[$key] = $this->calculateSubtotal($records, $type);
        }

        return $totals;
    }

    protected function calculateSubtotal($records, $type)
    {
        return $records->get($type, collect())->sum(function ($record) {
            return collect([
                'total'
            ])->sum(fn($month) => $record->$month);
        });
    }
}

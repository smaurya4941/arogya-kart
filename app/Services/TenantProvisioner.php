<?php

namespace App\Services;

use App\Models\ExpenseCategory;
use App\Models\Pharmacy;
use App\Models\Unit;

/**
 * Seeds a freshly-registered pharmacy with the baseline reference data every
 * store needs (measurement units, common expense categories) so the app is
 * usable the moment the owner logs in — no empty dropdowns on the first sale or
 * purchase. Runs inside the registration transaction.
 */
class TenantProvisioner
{
    /** @var array<int,array{name:string,short_name:string}> */
    private const DEFAULT_UNITS = [
        ['name' => 'Piece',   'short_name' => 'pc'],
        ['name' => 'Strip',   'short_name' => 'strip'],
        ['name' => 'Bottle',  'short_name' => 'btl'],
        ['name' => 'Box',     'short_name' => 'box'],
        ['name' => 'Tube',    'short_name' => 'tube'],
        ['name' => 'Vial',    'short_name' => 'vial'],
        ['name' => 'Sachet',  'short_name' => 'sachet'],
    ];

    /** @var array<int,string> */
    private const DEFAULT_EXPENSE_CATEGORIES = [
        'Rent', 'Electricity', 'Salaries', 'Internet', 'Maintenance', 'Marketing', 'Miscellaneous',
    ];

    public function provision(Pharmacy $pharmacy): void
    {
        foreach (self::DEFAULT_UNITS as $unit) {
            Unit::create([
                'pharmacy_id' => $pharmacy->id,
                'name'        => $unit['name'],
                'short_name'  => $unit['short_name'],
            ]);
        }

        foreach (self::DEFAULT_EXPENSE_CATEGORIES as $name) {
            ExpenseCategory::create([
                'pharmacy_id' => $pharmacy->id,
                'name'        => $name,
            ]);
        }
    }
}

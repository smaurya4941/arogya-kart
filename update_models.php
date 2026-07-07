<?php

$files = glob(__DIR__ . '/app/Models/*.php');
$targetModels = ['Sale', 'SaleItem', 'Expense', 'ExpenseCategory', 'Payment', 'Plan', 'Subscription', 'Invoice', 'Customer', 'PurchaseInvoice'];

foreach ($files as $file) {
    $basename = basename($file, '.php');
    if (in_array($basename, $targetModels)) {
        $content = file_get_contents($file);
        
        // Add trait import if not present
        if (strpos($content, 'use App\Traits\BelongsToPharmacy;') === false) {
            $content = str_replace('use Illuminate\Database\Eloquent\Model;', "use Illuminate\Database\Eloquent\Model;\nuse App\Traits\BelongsToPharmacy;", $content);
        }
        
        // Add trait use statement
        if (strpos($content, 'use HasFactory;') !== false && strpos($content, 'use BelongsToPharmacy;') === false) {
            $content = str_replace('use HasFactory;', "use HasFactory, BelongsToPharmacy;", $content);
        } elseif (strpos($content, '{') !== false && strpos($content, 'use BelongsToPharmacy;') === false) {
            // Find the first { after class definition
            $content = preg_replace('/class\s+'.$basename.'\s+extends\s+Model\s*\{/', "class {$basename} extends Model\n{\n    use BelongsToPharmacy;", $content);
        }
        
        file_put_contents($file, $content);
    }
}
echo "Models updated.\n";

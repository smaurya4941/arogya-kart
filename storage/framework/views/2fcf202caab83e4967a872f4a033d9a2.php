<?php $__env->startSection('title', 'New Purchase'); ?>

<?php
    $oldItems = old('items');
    $initialRows = is_array($oldItems) && count($oldItems)
        ? array_values($oldItems)
        : [[
            'product_id' => '', 'batch_number' => '', 'expiry_date' => '',
            'quantity' => 1, 'purchase_price' => '', 'mrp' => '',
            'selling_price' => '', 'gst_percentage' => 0,
        ]];
?>

<?php $__env->startSection('content'); ?>
<div class="page max-w-6xl"
     x-data="purchaseForm(<?php echo e(Illuminate\Support\Js::from($initialRows)); ?>)">
    <div class="page-header">
        <h1 class="page-title">New Purchase</h1>
    </div>

    <form method="POST" action="<?php echo e(route('admin.purchases.store')); ?>" class="space-y-4">
        <?php echo csrf_field(); ?>

        
        <div class="card card-pad grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <label class="form-label">Supplier <span class="text-error">*</span></label>
                <select name="supplier_id" class="form-select" required>
                    <option value="">Select supplier</option>
                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($supplier->id); ?>"
                            <?php if(old('supplier_id', $selectedSupplierId) == $supplier->id): echo 'selected'; endif; ?>>
                            <?php echo e($supplier->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($suppliers->isEmpty()): ?>
                    <p class="mt-1 text-xs text-error">
                        No active suppliers. <a href="<?php echo e(route('admin.suppliers.create')); ?>" class="underline">Add one first.</a>
                    </p>
                <?php endif; ?>
            </div>
            <div>
                <label class="form-label">Purchase Date <span class="text-error">*</span></label>
                <input type="date" name="purchase_date" value="<?php echo e(old('purchase_date', now()->toDateString())); ?>" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Supplier Invoice #</label>
                <input type="text" name="supplier_invoice_number" value="<?php echo e(old('supplier_invoice_number')); ?>" class="form-input">
            </div>
            <div>
                <label class="form-label">Payment Terms</label>
                <input type="text" name="payment_terms" value="<?php echo e(old('payment_terms')); ?>" placeholder="e.g. Net 30" class="form-input">
            </div>
            <div class="md:col-span-2">
                <label class="form-label">Notes</label>
                <input type="text" name="notes" value="<?php echo e(old('notes')); ?>" class="form-input">
            </div>
        </div>

        
        <div class="card card-pad">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="section-title">Items</h2>
                <button type="button" @click="addRow()" class="btn btn-primary btn-sm">
                    <span class="material-symbols-outlined text-[16px]">add</span> Add Item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="table-saas">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Batch #</th>
                            <th>Expiry</th>
                            <th class="w-20">Qty</th>
                            <th class="w-28">Buy Price</th>
                            <th class="w-28">MRP</th>
                            <th class="w-28">Sell Price</th>
                            <th class="w-20">GST %</th>
                            <th class="w-28 text-right">Line Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, i) in rows" :key="i">
                            <tr class="align-top">
                                <td>
                                    <select :name="`items[${i}][product_id]`" x-model="row.product_id" class="form-select h-8 w-44" required>
                                        <option value="">Select</option>
                                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($product->id); ?>"><?php echo e($product->name); ?> (<?php echo e($product->sku); ?>)</option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </td>
                                <td><input type="text" :name="`items[${i}][batch_number]`" x-model="row.batch_number" class="form-input h-8 w-28" required></td>
                                <td><input type="date" :name="`items[${i}][expiry_date]`" x-model="row.expiry_date" class="form-input h-8 w-36" required></td>
                                <td><input type="number" min="1" :name="`items[${i}][quantity]`" x-model.number="row.quantity" class="form-input h-8 w-20" required></td>
                                <td><input type="number" step="0.01" min="0" :name="`items[${i}][purchase_price]`" x-model.number="row.purchase_price" class="form-input h-8 w-28" required></td>
                                <td><input type="number" step="0.01" min="0" :name="`items[${i}][mrp]`" x-model.number="row.mrp" class="form-input h-8 w-28" required></td>
                                <td><input type="number" step="0.01" min="0" :name="`items[${i}][selling_price]`" x-model.number="row.selling_price" class="form-input h-8 w-28"></td>
                                <td><input type="number" step="0.01" min="0" max="100" :name="`items[${i}][gst_percentage]`" x-model.number="row.gst_percentage" class="form-input h-8 w-20"></td>
                                <td class="text-right font-medium" x-text="lineTotal(row).toFixed(2)"></td>
                                <td>
                                    <button type="button" @click="removeRow(i)" x-show="rows.length > 1" class="btn-icon hover:text-error">✕</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="bg-surface-container-low/60">
                            <td colspan="8" class="px-4 py-3 text-right font-semibold">Grand Total</td>
                            <td class="px-4 py-3 text-right font-bold">₹<span x-text="grandTotal().toFixed(2)"></span></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex gap-2">
            <button class="btn btn-primary">Record Purchase</button>
            <a href="<?php echo e(route('admin.purchases.index')); ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
    function purchaseForm(initialRows) {
        return {
            rows: initialRows,
            addRow() {
                this.rows.push({
                    product_id: '', batch_number: '', expiry_date: '',
                    quantity: 1, purchase_price: '', mrp: '',
                    selling_price: '', gst_percentage: 0,
                });
            },
            removeRow(i) {
                this.rows.splice(i, 1);
            },
            lineTotal(row) {
                const base = (Number(row.quantity) || 0) * (Number(row.purchase_price) || 0);
                const gst = base * (Number(row.gst_percentage) || 0) / 100;
                return base + gst;
            },
            grandTotal() {
                return this.rows.reduce((sum, row) => sum + this.lineTotal(row), 0);
            },
        };
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/purchases/create.blade.php ENDPATH**/ ?>
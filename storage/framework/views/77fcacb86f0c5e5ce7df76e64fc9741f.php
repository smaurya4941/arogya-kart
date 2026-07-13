<?php
    // $action = route name for the current report; $pdfRoute = optional PDF export route name.
    $action = $action ?? null;
    $pdfRoute = $pdfRoute ?? null;
    $from = request('from', optional($start ?? null)->toDateString());
    $to = request('to', optional($end ?? null)->toDateString());
?>

<form method="GET" action="<?php echo e(route($action)); ?>" class="card card-pad">
    <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
        <div>
            <label class="form-label">From</label>
            <input type="date" name="from" value="<?php echo e($from); ?>" class="form-input">
        </div>
        <div>
            <label class="form-label">To</label>
            <input type="date" name="to" value="<?php echo e($to); ?>" class="form-input">
        </div>
        <div class="flex items-end gap-2">
            <button class="btn btn-primary btn-sm">Apply</button>
            <a href="<?php echo e(route($action)); ?>" class="btn btn-outline btn-sm">Reset</a>
        </div>
        <?php if($pdfRoute): ?>
            <div class="flex items-end justify-end">
                <a href="<?php echo e(route($pdfRoute, ['from' => $from, 'to' => $to])); ?>" class="btn btn-outline">
                    <span class="material-symbols-outlined text-[18px]">download</span> Download PDF
                </a>
            </div>
        <?php endif; ?>
    </div>
</form>
<?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/reports/_filters.blade.php ENDPATH**/ ?>
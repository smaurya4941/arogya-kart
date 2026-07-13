<?php
    $messages = collect([
        [
            'key' => 'success',
            'classes' => 'border-tertiary/30 bg-tertiary-container/15 text-tertiary',
            'title' => 'Success',
        ],
        [
            'key' => 'error',
            'classes' => 'border-error/30 bg-error-container/40 text-on-error-container',
            'title' => 'Error',
        ],
        [
            'key' => 'status',
            'classes' => 'border-secondary/30 bg-secondary/10 text-secondary',
            'title' => 'Notice',
        ],
    ])->filter(fn ($message) => session()->has($message['key']));
?>

<?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div
        x-data="{ visible: true }"
        x-show="visible"
        x-transition.opacity.duration.300ms
        class="mb-4 flex items-start justify-between gap-4 rounded-xl border px-4 py-3 <?php echo e($message['classes']); ?>"
        role="alert"
    >
        <div>
            <p class="text-sm font-semibold"><?php echo e($message['title']); ?></p>
            <p class="mt-0.5 text-sm"><?php echo e(session($message['key'])); ?></p>
        </div>

        <button
            type="button"
            class="rounded-lg p-1 transition hover:bg-black/5"
            @click="visible = false"
            aria-label="Dismiss notification"
        >
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php if($errors->any()): ?>
    <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900" role="alert">
        <p class="text-sm font-semibold">Please review the highlighted fields.</p>
        <ul class="mt-2 space-y-1 text-sm">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>
<?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/components/flash-message.blade.php ENDPATH**/ ?>
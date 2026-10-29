<?php $__env->startSection('title', 'Rooms & Suites — LuxStay'); ?>

<?php $__env->startSection('content'); ?>
<div class="relative bg-ink-900 text-white">
    <div class="absolute inset-0 opacity-30 bg-[url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1600')] bg-cover bg-center"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <h1 class="text-4xl md:text-5xl font-serif">Find Your Perfect Stay</h1>
        <p class="mt-3 text-slate-300 max-w-xl mx-auto">Browse our rooms and suites, check live availability, and book in minutes.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-10">
    <form method="GET" class="bg-white rounded-xl shadow-xl p-5 grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">CHECK-IN</label>
            <input type="date" name="check_in" value="<?php echo e(request('check_in')); ?>" class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">CHECK-OUT</label>
            <input type="date" name="check_out" value="<?php echo e(request('check_out')); ?>" class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">ROOM TYPE</label>
            <select name="type" class="w-full rounded-lg border-slate-300 text-sm">
                <option value="">Any type</option>
                <?php $__currentLoopData = ['single','standard','deluxe','suite']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type); ?>" <?php if(request('type') === $type): echo 'selected'; endif; ?>><?php echo e(ucfirst($type)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">MAX PRICE / NIGHT</label>
            <input type="number" name="max_price" value="<?php echo e(request('max_price')); ?>" placeholder="Any" class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div class="flex items-end">
            <button class="w-full bg-ink-900 hover:bg-ink-800 text-white font-semibold rounded-lg py-2.5 text-sm transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Search
            </button>
        </div>
    </form>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php $__empty_1 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $cover = $room->coverImage(); ?>
            <a href="<?php echo e(route('rooms.show', $room)); ?>" class="group bg-white rounded-2xl shadow-md hover:shadow-xl transition overflow-hidden border border-slate-100">
                <div class="h-52 overflow-hidden">
                    <img src="<?php echo e($cover?->url() ?? 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800'); ?>"
                         alt="<?php echo e($room->name); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase tracking-wide font-semibold text-gold-600"><?php echo e($room->type); ?></span>
                        <span class="text-xs text-slate-400"><i class="fa-solid fa-user-group"></i> Up to <?php echo e($room->capacity); ?></span>
                    </div>
                    <h3 class="text-lg font-serif mt-1"><?php echo e($room->name); ?></h3>
                    <p class="text-sm text-slate-500 mt-1 line-clamp-2"><?php echo e($room->description); ?></p>
                    <div class="flex items-center gap-2 mt-3 text-slate-400 text-sm">
                        <?php $__currentLoopData = $room->amenities->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <i class="<?php echo e($a->icon); ?>" title="<?php echo e($a->name); ?>"></i>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($room->amenities->count() > 3): ?>
                            <span class="text-xs">+<?php echo e($room->amenities->count() - 3); ?> more</span>
                        <?php endif; ?>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <div><span class="text-xl font-bold">$<?php echo e(number_format($room->price_per_night, 0)); ?></span><span class="text-slate-400 text-sm"> / night</span></div>
                        <span class="text-sm font-semibold text-gold-600 group-hover:underline">View room &rarr;</span>
                    </div>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="col-span-3 text-center text-slate-500 py-20">No rooms match your search. Try different dates or filters.</p>
        <?php endif; ?>
    </div>

    <div class="mt-10"><?php echo e($rooms->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\hotel-management-system-main\resources\views/rooms/index.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', $room->name.' — LuxStay'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <a href="<?php echo e(route('rooms.index')); ?>" class="text-sm text-slate-500 hover:text-gold-600"><i class="fa-solid fa-arrow-left mr-1"></i> Back to rooms</a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mt-4">
        <div class="lg:col-span-2">
            <div class="rounded-2xl overflow-hidden shadow-lg h-96 bg-slate-200" x-data="{ active: 0 }">
                <?php $__currentLoopData = $room->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <img x-show="active === <?php echo e($i); ?>" src="<?php echo e($img->url()); ?>" class="w-full h-full object-cover">
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($room->images->isEmpty()): ?>
                    <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?w=1200" class="w-full h-full object-cover">
                <?php endif; ?>
            </div>

            <div class="mt-8">
                <span class="text-xs uppercase tracking-wide font-semibold text-gold-600"><?php echo e($room->type); ?> &middot; Room <?php echo e($room->room_number); ?></span>
                <h1 class="text-3xl font-serif mt-1"><?php echo e($room->name); ?></h1>
                <p class="text-slate-600 mt-4 leading-relaxed"><?php echo e($room->description); ?></p>

                <h3 class="font-semibold mt-8 mb-3">Amenities</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <?php $__currentLoopData = $room->amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center gap-2 text-sm text-slate-600 bg-slate-50 rounded-lg px-3 py-2">
                            <i class="<?php echo e($a->icon); ?> text-gold-600"></i> <?php echo e($a->name); ?>

                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white border border-slate-200 rounded-2xl shadow-lg p-6 sticky top-24">
                <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-bold">$<?php echo e(number_format($room->price_per_night, 2)); ?></span>
                    <span class="text-slate-400 text-sm">/ night</span>
                </div>
                <p class="text-sm text-slate-500 mt-1"><i class="fa-solid fa-user-group"></i> Sleeps up to <?php echo e($room->capacity); ?> guests</p>

                <div class="mt-5 flex items-center gap-2 text-sm">
                    <span class="w-2.5 h-2.5 rounded-full <?php echo e($room->status === 'available' ? 'bg-emerald-500' : 'bg-amber-500'); ?>"></span>
                    <?php echo e(ucfirst($room->status)); ?>

                </div>

                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('bookings.create', $room)); ?>"
                       class="mt-6 block text-center w-full bg-gold-500 hover:bg-gold-600 text-ink-900 font-semibold py-3 rounded-lg transition">
                        Book This Room
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>"
                       class="mt-6 block text-center w-full bg-ink-900 hover:bg-ink-800 text-white font-semibold py-3 rounded-lg transition">
                        Sign in to Book
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\hotel-management-system-main\resources\views/rooms/show.blade.php ENDPATH**/ ?>
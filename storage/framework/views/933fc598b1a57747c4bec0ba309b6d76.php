
<?php
    $bannerUser = auth()->user();
    $showSwitch = \App\Support\Demo\ReportMode::switchVisibleFor($bannerUser) && isset($reportParty);
    $ownDataReady = $showSwitch && ($isDemoReport ?? false)
        ? app(\App\Services\Demo\ReportPartyResolver::class)->hasOwnData($bannerUser)
        : false;
?>

<?php if($showSwitch): ?>
    <?php if($isDemoReport ?? false): ?>
        <div class="mb-4 rounded-md border border-amber-400 bg-amber-50 px-4 py-3 text-amber-900 dark:border-amber-600 dark:bg-amber-950 dark:text-amber-100"
             role="status" aria-live="polite">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-semibold">
                        <span class="mr-2 inline-block rounded bg-amber-500 px-2 py-0.5 text-xs font-bold uppercase tracking-wide text-white">Sample data</span>
                        You're viewing <?php echo e(\App\Support\Demo\DemoParty::name()); ?>, a fictional company.
                    </p>
                    <p class="mt-1 text-sm">
                        <?php if($ownDataReady): ?>
                            Your books are connected. Switch to My business to see your own figures.
                        <?php else: ?>
                            These figures are not yours. Your own reports appear here once your books are connected.
                        <?php endif; ?>
                    </p>
                </div>
                <form method="POST" action="<?php echo e(route('report-mode.update')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="report_mode" value="<?php echo e(\App\Support\Demo\ReportMode::OWN); ?>">
                    <button type="submit"
                            class="rounded-md bg-amber-600 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400">
                        Show my business
                    </button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="mb-3 flex justify-end">
            <form method="POST" action="<?php echo e(route('report-mode.update')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="report_mode" value="<?php echo e(\App\Support\Demo\ReportMode::DEMO); ?>">
                <button type="submit"
                        class="text-sm text-gray-600 underline hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                    View sample reports
                </button>
            </form>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views/shared/_report_mode_banner.blade.php ENDPATH**/ ?>
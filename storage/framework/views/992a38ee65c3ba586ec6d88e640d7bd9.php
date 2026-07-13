<!DOCTYPE html>
<html lang="en">

<head>
    <?php echo $__env->make('includes.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>

<body class="antialiased font-sans selection:bg-balantro-primary selection:text-white overflow-hidden">
<!-- Intro Overlay -->
    <div id="intro-overlay">
      <div id="intro-white-bg"></div>
      <div class="intro-container" id="intro-container">
        <img
          src="images/Balantro%20logo%20final%20deliverables_Only%20Black.png"
          alt="Balantro"
          class="intro-logo-img"
        />
        <div class="intro-text-wrapper" id="intro-text-wrapper">
          <span class="intro-text"
            >Balantro<span style="color: #22d3ee">.</span></span
          >
        </div>
      </div>
    </div>
    <script>
      // Intro sequence logic
      if (!document.documentElement.classList.contains("skip-intro")) {
        window.scrollTo(0, 0);

        document.addEventListener("DOMContentLoaded", () => {
          const whiteBg = document.getElementById("intro-white-bg");
          const textWrapper = document.getElementById("intro-text-wrapper");
          const container = document.getElementById("intro-container");
          const overlay = document.getElementById("intro-overlay");

          // 1) Create 1px line
          setTimeout(() => {
            whiteBg.classList.add("expand-line");
          }, 100);

          // 2) Expand white bg to full height
          setTimeout(() => {
            whiteBg.classList.add("expand-full");
          }, 600);

          // 3) Show black logo once white background covers screen
          setTimeout(() => {
            container.classList.add("show");
          }, 1400);

          // 4) Pause, then expand text from behind logo slowly
          setTimeout(() => {
            textWrapper.classList.add("expand");
          }, 2800);

          // 5) Smooth transition into home hero
          setTimeout(() => {
            container.classList.add("zoom-out");
            overlay.classList.add("hide");

            if (typeof window.initBalantroAnimations === "function") {
              window.initBalantroAnimations();
            }

            // Unlock scroll and save state
            setTimeout(() => {
              document.body.classList.remove("overflow-hidden");
              sessionStorage.setItem("balantroIntroPlayed", "true");
            }, 1200);
          }, 6500);
        });
      } else {
        // make sure scroll is allowed if skip-intro is active
        document.body.classList.remove("overflow-hidden");
        document.documentElement.classList.remove("intro-running");
      }
    </script>
    <?php echo $__env->make('includes.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->yieldContent('content'); ?>

    <?php echo $__env->make('includes.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->yieldContent('scripts'); ?>

    <?php echo $__env->make('includes.footer-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</body>

</html>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views/layouts/front.blade.php ENDPATH**/ ?>
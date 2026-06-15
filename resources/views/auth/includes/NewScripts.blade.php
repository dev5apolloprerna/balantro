<script>
    // Theme management functions
    function getStoredTheme() {
        return localStorage.getItem('theme') || localStorage.getItem('color-theme');
    }

    function setStoredTheme(theme) {
        localStorage.setItem('theme', theme);
        localStorage.setItem('color-theme', theme);
    }

    function getPreferredTheme() {
        const storedTheme = getStoredTheme();
        if (storedTheme) {
            return storedTheme;
        }

         return 'dark'; //return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function setTheme(theme) {
        if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.toggle('dark', theme === 'dark');
        }

        // Store in localStorage
        setStoredTheme(theme);

        // Also store in session to maintain across requests
        updateServerTheme(theme);
    }

    function updateServerTheme(theme) {
        // Send theme preference to server to maintain consistency
        fetch('{{ route('theme.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    theme: theme
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Theme updated on server:', data);
            })
            .catch(error => {
                console.error('Error updating theme on server:', error);
            });
    }

    // Initialize theme on page load
    document.addEventListener('DOMContentLoaded', function() {
        const preferredTheme = getPreferredTheme();
        setTheme(preferredTheme);

        // Set up theme toggle button
        const themeToggleBtn = document.getElementById('theme-toggle');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', function() {
                const currentTheme = getStoredTheme();
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                setTheme(newTheme);
            });
        }

        // Password toggle functionality
        document.querySelectorAll('.toggle-password').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const target = document.querySelector(this.getAttribute('data-toggle'));
                const icon = this.querySelector('iconify-icon');

                if (target.type === 'password') {
                    target.type = 'text';
                    icon.setAttribute('icon', 'ri:eye-off-line');
                } else {
                    target.type = 'password';
                    icon.setAttribute('icon', 'ri:eye-line');
                }
            });
        });
    });

    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
        const storedTheme = getStoredTheme();
        if (storedTheme !== 'light' && storedTheme !== 'dark') {
            setTheme(e.matches ? 'dark' : 'light');
        }
    });
</script>

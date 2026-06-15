class MagicButtonEffect {
    constructor(button) {
        this.button = button;
        
        // Only initialize if not already initialized
        if (this.button.classList.contains('magic-initialized')) return;
        this.button.classList.add('magic-initialized');
        
        // Save original HTML content to keep SVG or inner spans if any
        const htmlContent = this.button.innerHTML;
        this.button.innerHTML = '';
        
        // Add required classes to button
        this.button.classList.add('relative', 'overflow-hidden');
        // Ensure z-index is elevated on hover without breaking layout
        
        // Setup Canvas
        this.canvas = document.createElement('canvas');
        this.canvas.className = 'absolute inset-0 z-0 pointer-events-none opacity-0 transition-opacity duration-500 rounded-full';
        this.ctx = this.canvas.getContext('2d');
        
        // Setup Content wrapper
        this.content = document.createElement('div');
        this.content.className = 'relative z-10 pointer-events-none flex items-center justify-center w-full h-full';
        this.content.innerHTML = htmlContent;
        
        this.button.appendChild(this.canvas);
        this.button.appendChild(this.content);
        
        // Particles array
        this.particles = [];
        this.mouse = { x: 0, y: 0 };
        this.isHovering = false;
        
        // Resize canvas
        this.resize();
        
        // Events
        this.button.addEventListener('mouseenter', this.onMouseEnter.bind(this));
        this.button.addEventListener('mouseleave', this.onMouseLeave.bind(this));
        this.button.addEventListener('mousemove', this.onMouseMove.bind(this));
        window.addEventListener('resize', this.resize.bind(this));
        
        // Start animation loop
        this.animate();
    }
    
    resize() {
        const rect = this.button.getBoundingClientRect();
        this.canvas.width = rect.width;
        this.canvas.height = rect.height;
    }
    
    onMouseEnter(e) {
        this.isHovering = true;
        this.canvas.classList.remove('opacity-0');
        this.canvas.classList.add('opacity-100');
        this.updateMousePos(e);
        this.resize();
    }
    
    onMouseLeave() {
        this.isHovering = false;
        this.canvas.classList.remove('opacity-100');
        this.canvas.classList.add('opacity-0');
    }
    
    onMouseMove(e) {
        this.updateMousePos(e);
    }
    
    updateMousePos(e) {
        const rect = this.button.getBoundingClientRect();
        this.mouse.x = e.clientX - rect.left;
        this.mouse.y = e.clientY - rect.top;
        
        // Add emit particles
        if (Math.random() > 0.3) {
            for(let i=0; i<2; i++) {
                this.particles.push({
                    x: this.mouse.x,
                    y: this.mouse.y,
                    vx: (Math.random() - 0.5) * 2,
                    vy: (Math.random() - 0.5) * 2 - 0.5,
                    size: Math.random() * 2 + 1,
                    life: 1,
                    color: Math.random() > 0.5 ? '#ffffff' : '#e0f2fe' // white and light blue
                });
            }
        }
    }
    
    animate() {
        requestAnimationFrame(this.animate.bind(this));
        
        // Only draw if hovering or if there are particles (to fade out)
        if (!this.isHovering && this.particles.length === 0) return;
        
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        // Draw soft glow at mouse position
        if (this.isHovering) {
            const gradient = this.ctx.createRadialGradient(this.mouse.x, this.mouse.y, 0, this.mouse.x, this.mouse.y, 50);
            gradient.addColorStop(0, 'rgba(255, 255, 255, 0.4)');
            gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
            this.ctx.fillStyle = gradient;
            this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
        }
        
        // Draw particles
        for (let i = 0; i < this.particles.length; i++) {
            const p = this.particles[i];
            
            p.x += p.vx;
            p.y += p.vy;
            p.life -= 0.03; // fade speed
            p.size *= 0.96; // shrink speed
            
            if (p.life <= 0) {
                this.particles.splice(i, 1);
                i--;
                continue;
            }
            
            this.ctx.beginPath();
            this.ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
            this.ctx.fillStyle = p.color;
            this.ctx.globalAlpha = p.life;
            this.ctx.fill();
        }
        this.ctx.globalAlpha = 1;
    }
}

// Initialize on load
document.addEventListener('DOMContentLoaded', () => {
    // Find all Get Started buttons statically
    const initButtons = () => {
        const links = document.querySelectorAll('a');
        links.forEach(a => {
            if (a.textContent.trim() === 'Get Started' && a.classList.contains('bg-gradient-to-r')) {
                new MagicButtonEffect(a);
            }
        });
    };
    
    initButtons();
    
    // Also use mutation observer in case buttons are added dynamically
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.addedNodes.length) {
                initButtons();
            }
        });
    });
    
    observer.observe(document.body, { childList: true, subtree: true });
});

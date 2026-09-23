document.addEventListener('DOMContentLoaded', () => {
    const submitBtn = document.getElementById('submit-signature-btn');
    const signatureArea = document.getElementById('client-signature-area');
    const signatureBox = document.querySelector('.client-box');
    const downloadPdfBtn = document.getElementById('download-pdf-btn');
    const clearBtn = document.getElementById('clear-signature-btn');
    const dateText = document.getElementById('client-date-text');
    const canvas = document.getElementById('signature-canvas');
    const placeholder = document.getElementById('canvas-placeholder');

    let ctx = null;
    let isDrawing = false;
    let hasDrawn = false;

    if (canvas) {
        ctx = canvas.getContext('2d');
        
        // Resize canvas to match the DOM element size exactly
        const initCanvas = () => {
            const rect = signatureArea.getBoundingClientRect();
            // Fallback sizes if rect width is 0 (e.g. element not visible yet)
            canvas.width = rect.width || 300; 
            canvas.height = rect.height || 60;
            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#1E2432';
        };
        initCanvas();

        const startDrawing = (e) => {
            isDrawing = true;
            ctx.beginPath();
            
            // On first interaction, hide placeholder and style as signed
            if (!hasDrawn) {
                placeholder.style.display = 'none';
                clearBtn.style.display = 'inline-block';
                signatureBox.classList.remove('action-required');
                signatureBox.style.border = '1px solid #E5E7EE';
                signatureBox.style.backgroundColor = '#FFFFFF';
                dateText.innerText = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                submitBtn.disabled = false;
                hasDrawn = true;
            }
            draw(e);
        };

        const stopDrawing = () => {
            isDrawing = false;
            ctx.beginPath();
        };

        const getCoordinates = (e) => {
            const rect = canvas.getBoundingClientRect();
            let x, y;
            if (e.touches && e.touches.length > 0) {
                x = e.touches[0].clientX - rect.left;
                y = e.touches[0].clientY - rect.top;
            } else {
                x = e.clientX - rect.left;
                y = e.clientY - rect.top;
            }
            return { x, y };
        };

        const draw = (e) => {
            if (!isDrawing) return;
            e.preventDefault(); // prevent scrolling while drawing on touch
            
            const { x, y } = getCoordinates(e);
            
            ctx.lineTo(x, y);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(x, y);
        };

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        // Touch support for mobile devices
        canvas.addEventListener('touchstart', startDrawing, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', stopDrawing);
        canvas.addEventListener('touchcancel', stopDrawing);
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasDrawn = false;
            placeholder.style.display = 'inline-block';
            clearBtn.style.display = 'none';
            signatureBox.classList.add('action-required');
            signatureBox.style.border = '';
            signatureBox.style.backgroundColor = '';
            dateText.innerText = 'Pending';
            submitBtn.disabled = true;
        });
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', () => {
            alert('Signature submitted successfully! (This is a static shell demo)');
        });
    }

    if (downloadPdfBtn) {
        downloadPdfBtn.addEventListener('click', () => {
            alert('PDF Download initiated. (This is a frontend shell demo, real download requires backend integration).');
        });
    }

    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            
            menuItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');

            const title = item.querySelector('.title').innerText;
            if (title.includes('NDA')) {
                alert('Non-Disclosure Agreement (NDA) document selected. (This is a static shell demo, actual document would load here).');
            }
        });
    });
});

    // Update Invoice Download Buttons
    const downloadInvoiceBtns = document.querySelectorAll('.download-invoice-btn');
    downloadInvoiceBtns.forEach(btn => {
        // Remove old alert listener
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
        
        newBtn.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Visual feedback (blink effect)
            newBtn.style.transform = 'scale(0.95)';
            newBtn.style.opacity = '0.7';
            setTimeout(() => {
                newBtn.style.transform = 'scale(1)';
                newBtn.style.opacity = '1';
                
                // Actual file download trigger
                const link = document.createElement('a');
                link.href = 'dummy-invoice.pdf';
                link.download = 'LogixPulse_Invoice.pdf';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 150);
        });
    });

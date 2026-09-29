/**
 * LogixPulse Client Portal
 * JavaScript Controller for Agreements, Signatures, and Dashboard Interactivity
 * Squad FE-B | Task: FEB-W8D1-1 — Submit the Signature
 */

document.addEventListener('DOMContentLoaded', () => {
    // ----------------------------------------------------
    // 1. Toast Notification Helper
    // ----------------------------------------------------
    const showToast = (message, type = 'success') => {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        const iconSvg = type === 'success'
            ? `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`
            : `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`;

        toast.innerHTML = `
            <span class="toast-icon">${iconSvg}</span>
            <span class="toast-message">${message}</span>
        `;

        container.appendChild(toast);

        // Auto remove after 4 seconds
        setTimeout(() => {
            toast.classList.add('toast-fadeout');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 4000);
    };

    // ----------------------------------------------------
    // 2. Master Agreement & Signature Management
    // ----------------------------------------------------
    const STORAGE_KEY = 'logixpulse_agreement_MSA_2026_0892';

    const canvas = document.getElementById('signature-canvas');
    const signatureArea = document.getElementById('client-signature-area');
    const signatureBox = document.getElementById('client-signature-box');
    const placeholder = document.getElementById('canvas-placeholder');
    const clearBtn = document.getElementById('clear-signature-btn');
    const submitBtn = document.getElementById('submit-signature-btn');
    const downloadPdfBtn = document.getElementById('download-pdf-btn');
    const dateText = document.getElementById('client-date-text');
    const lockedBadge = document.getElementById('signature-locked-badge');
    const resetDemoBtn = document.getElementById('reset-signature-demo-btn');

    // Header & Sidebar Elements
    const headerStatusBadge = document.getElementById('agreement-status-badge');
    const headerStatusDot = document.getElementById('agreement-status-dot');
    const headerStatusLabel = document.getElementById('agreement-status-label');
    const msaSidebarSubtitle = document.getElementById('msa-sidebar-subtitle');
    const msaSidebarIndicator = document.getElementById('msa-sidebar-indicator');

    if (canvas && signatureArea) {
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let hasDrawn = false;

        // Resize and calibrate canvas dimensions with device pixel ratio support
        const resizeCanvas = () => {
            const rect = signatureArea.getBoundingClientRect();
            const width = rect.width || 320;
            const height = rect.height || 60;
            
            // Set canvas display size
            canvas.style.width = '100%';
            canvas.style.height = '100%';
            canvas.width = width;
            canvas.height = height;

            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.strokeStyle = '#1E2432';
        };

        resizeCanvas();
        window.addEventListener('resize', () => {
            // Re-render saved signature if resized
            const savedData = loadSavedAgreement();
            resizeCanvas();
            if (savedData && savedData.signatureDataUrl) {
                renderSavedSignature(savedData.signatureDataUrl);
            }
        });

        // Load saved state from LocalStorage
        const loadSavedAgreement = () => {
            try {
                const saved = localStorage.getItem(STORAGE_KEY);
                return saved ? JSON.parse(saved) : null;
            } catch (err) {
                console.error('Error loading agreement state:', err);
                return null;
            }
        };

        // Render saved signature onto canvas
        const renderSavedSignature = (dataUrl) => {
            const img = new Image();
            img.onload = () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            };
            img.src = dataUrl;
        };

        // Apply Locked & Signed State to UI
        const applySignedState = (data) => {
            hasDrawn = true;

            // Canvas & Box locked styling
            if (placeholder) placeholder.style.display = 'none';
            if (clearBtn) clearBtn.style.display = 'none';
            if (lockedBadge) lockedBadge.style.display = 'inline-flex';
            
            canvas.style.pointerEvents = 'none';
            canvas.style.cursor = 'default';

            if (signatureBox) {
                signatureBox.classList.remove('action-required');
                signatureBox.classList.add('signed-locked');
            }

            if (dateText) {
                dateText.innerText = `Signed on ${data.signedDate}`;
            }

            // Header badge update to Emerald Green "Signed"
            if (headerStatusBadge) {
                headerStatusBadge.className = 'status-badge status-signed';
            }
            if (headerStatusDot) {
                headerStatusDot.className = 'status-dot signed';
            }
            if (headerStatusLabel) {
                headerStatusLabel.innerText = `Signed on ${data.signedDate}`;
            }

            // Sidebar indicator update
            if (msaSidebarSubtitle) {
                msaSidebarSubtitle.innerText = `Signed ${data.signedDate}`;
            }
            if (msaSidebarIndicator) {
                msaSidebarIndicator.className = 'status-indicator signed';
            }

            // Submit Button locked state
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('btn-locked');
                submitBtn.innerHTML = `
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px; vertical-align: -2px;">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Document Locked & Signed
                `;
            }

            // Show reset for demo testing button
            if (resetDemoBtn) {
                resetDemoBtn.style.display = 'inline-block';
            }
        };

        // Reset UI to Pending Signature State
        const resetPendingState = () => {
            hasDrawn = false;
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            if (placeholder) placeholder.style.display = 'inline-block';
            if (clearBtn) clearBtn.style.display = 'none';
            if (lockedBadge) lockedBadge.style.display = 'none';

            canvas.style.pointerEvents = 'auto';
            canvas.style.cursor = 'crosshair';

            if (signatureBox) {
                signatureBox.classList.remove('signed-locked');
                signatureBox.classList.add('action-required');
            }

            if (dateText) {
                dateText.innerText = 'Pending';
            }

            // Reset header status badge
            if (headerStatusBadge) {
                headerStatusBadge.className = 'status-badge status-pending';
            }
            if (headerStatusDot) {
                headerStatusDot.className = 'status-dot';
            }
            if (headerStatusLabel) {
                headerStatusLabel.innerText = 'Pending Signature';
            }

            // Reset sidebar
            if (msaSidebarSubtitle) {
                msaSidebarSubtitle.innerText = 'Action Required';
            }
            if (msaSidebarIndicator) {
                msaSidebarIndicator.className = 'status-indicator pending';
            }

            // Reset submit button
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.remove('btn-locked');
                submitBtn.innerHTML = 'Submit Signature';
            }

            if (resetDemoBtn) {
                resetDemoBtn.style.display = 'none';
            }
        };

        // Initialize agreement status on page load
        const savedAgreement = loadSavedAgreement();
        if (savedAgreement && savedAgreement.isSigned) {
            renderSavedSignature(savedAgreement.signatureDataUrl);
            applySignedState(savedAgreement);
        } else {
            resetPendingState();
        }

        // Coordinate calculation helper
        const getCoordinates = (e) => {
            const rect = canvas.getBoundingClientRect();
            let clientX, clientY;
            if (e.touches && e.touches.length > 0) {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            } else {
                clientX = e.clientX;
                clientY = e.clientY;
            }
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        };

        // Drawing Event Handlers
        const startDrawing = (e) => {
            const saved = loadSavedAgreement();
            if (saved && saved.isSigned) return; // Prevent drawing if locked

            isDrawing = true;
            ctx.beginPath();
            const { x, y } = getCoordinates(e);
            ctx.moveTo(x, y);

            if (!hasDrawn) {
                hasDrawn = true;
                if (placeholder) placeholder.style.display = 'none';
                if (clearBtn) clearBtn.style.display = 'inline-block';
                if (signatureBox) {
                    signatureBox.classList.remove('action-required');
                }
                const todayFormatted = new Date().toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                });
                if (dateText) {
                    dateText.innerText = `${todayFormatted} (Draft)`;
                }
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            }
        };

        const draw = (e) => {
            if (!isDrawing) return;
            if (e.cancelable) e.preventDefault(); // Prevent touch scroll while drawing

            const { x, y } = getCoordinates(e);
            ctx.lineTo(x, y);
            ctx.stroke();
        };

        const stopDrawing = () => {
            if (!isDrawing) return;
            isDrawing = false;
            ctx.beginPath();
        };

        // Desktop Mouse Listeners
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseleave', stopDrawing);

        // Mobile / Tablet Touch Listeners
        canvas.addEventListener('touchstart', startDrawing, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', stopDrawing);
        canvas.addEventListener('touchcancel', stopDrawing);

        // Clear Signature Button
        if (clearBtn) {
            clearBtn.addEventListener('click', (e) => {
                e.preventDefault();
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hasDrawn = false;
                if (placeholder) placeholder.style.display = 'inline-block';
                if (clearBtn) clearBtn.style.display = 'none';
                if (signatureBox) {
                    signatureBox.classList.add('action-required');
                }
                if (dateText) dateText.innerText = 'Pending';
                if (submitBtn) submitBtn.disabled = true;
            });
        }

        // Submit Signature Button - Task FEB-W8D1-1 Core Logic
        if (submitBtn) {
            submitBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (!hasDrawn) return;

                const today = new Date();
                const formattedDate = today.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                });

                const signatureData = {
                    isSigned: true,
                    documentId: 'MSA-2026-0892',
                    documentTitle: 'Master Service Agreement',
                    signerName: 'John Doe',
                    signerCompany: 'Acme Cloud Infrastructure',
                    signedDate: formattedDate,
                    signedTimestamp: today.toISOString(),
                    signatureDataUrl: canvas.toDataURL('image/png')
                };

                // Persist signature and lock status
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(signatureData));
                } catch (err) {
                    console.error('Failed to save to localStorage:', err);
                }

                // Apply signed and locked UI
                applySignedState(signatureData);

                // Notify User
                showToast('Signature successfully submitted! Master Service Agreement is now locked and legally binding.', 'success');
            });
        }

        // Reset for Demo Testing Button
        if (resetDemoBtn) {
            resetDemoBtn.addEventListener('click', (e) => {
                e.preventDefault();
                localStorage.removeItem(STORAGE_KEY);
                resetPendingState();
                showToast('Agreement reset to pending state for demo testing.', 'info');
            });
        }

        // Download PDF Button
        if (downloadPdfBtn) {
            downloadPdfBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const saved = loadSavedAgreement();
                
                // Visual feedback (press animation)
                downloadPdfBtn.style.transform = 'scale(0.96)';
                downloadPdfBtn.style.opacity = '0.8';

                setTimeout(() => {
                    downloadPdfBtn.style.transform = 'scale(1)';
                    downloadPdfBtn.style.opacity = '1';

                    const link = document.createElement('a');
                    link.href = 'dummy-invoice.pdf';
                    if (saved && saved.isSigned) {
                        link.download = `LogixPulse_Signed_MSA_2026_0892.pdf`;
                        showToast('Downloading legally signed Master Service Agreement (PDF)...', 'success');
                    } else {
                        link.download = `LogixPulse_Draft_MSA_2026_0892.pdf`;
                        showToast('Downloading draft Master Service Agreement (PDF)...', 'info');
                    }
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }, 180);
            });
        }
    }

    // ----------------------------------------------------
    // 3. Sidebar Agreements Switcher
    // ----------------------------------------------------
    const menuItems = document.querySelectorAll('.sidebar-menu .menu-item');
    menuItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            menuItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');

            const title = item.querySelector('.title')?.innerText || '';
            if (title.includes('NDA')) {
                showToast('Non-Disclosure Agreement (NDA) is already signed and archived.', 'info');
            }
        });
    });

    // ----------------------------------------------------
    // 4. Dashboard Integration (Cross-Page Consistency)
    // ----------------------------------------------------
    const checkDashboardAgreements = () => {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (!saved) return;

        try {
            const data = JSON.parse(saved);
            if (data.isSigned) {
                // Find timeline on dashboard if present
                const timeline = document.querySelector('.timeline');
                if (timeline) {
                    const existingSignedItem = document.getElementById('timeline-signed-msa');
                    if (!existingSignedItem) {
                        const newTimelineItem = document.createElement('div');
                        newTimelineItem.id = 'timeline-signed-msa';
                        newTimelineItem.className = 'timeline-item';
                        newTimelineItem.innerHTML = `
                            <div class="timeline-dot" style="background: #10B981; border-color: #DEF7EC;"></div>
                            <div class="timeline-content">
                                <h4 style="color: #065F46;">Master Service Agreement Signed & Locked</h4>
                                <span class="timeline-date">${data.signedDate} (Ref: ${data.documentId})</span>
                            </div>
                        `;
                        timeline.insertBefore(newTimelineItem, timeline.firstChild);
                    }
                }
            }
        } catch (err) {
            console.error('Error updating dashboard timeline:', err);
        }
    };

    checkDashboardAgreements();

    // ----------------------------------------------------
    // 5. Invoices Download Functionality (With Blink Effect)
    // ----------------------------------------------------
    const downloadInvoiceBtns = document.querySelectorAll('.download-invoice-btn');
    downloadInvoiceBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();

            // Visual feedback (blink effect)
            btn.style.transform = 'scale(0.95)';
            btn.style.opacity = '0.7';

            setTimeout(() => {
                btn.style.transform = 'scale(1)';
                btn.style.opacity = '1';

                const link = document.createElement('a');
                link.href = 'dummy-invoice.pdf';
                link.download = 'LogixPulse_Invoice.pdf';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                showToast('Invoice download started.', 'info');
            }, 150);
        });
    });
});

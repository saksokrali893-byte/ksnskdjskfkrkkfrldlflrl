// Api Service - Professional Card Checking Interface
class ApiServiceChecker {
    constructor() {
        this.isRunning = false;
        this.currentIndex = 0;
        this.cards = [];
        this.stats = {
            total: 0,
            approved: 0,
            declined: 0
        };
        this.delay = 1000;
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateStats();
        this.showWelcomeMessage();
    }

    bindEvents() {
        // Control buttons
        document.getElementById('startCheck')?.addEventListener('click', () => this.startChecking());
        document.getElementById('stopCheck')?.addEventListener('click', () => this.stopChecking());
        document.getElementById('clearResults')?.addEventListener('click', () => this.clearResults());

        // Input validation
        document.getElementById('cardList')?.addEventListener('input', (e) => this.validateCardList(e.target.value));
        document.getElementById('delay')?.addEventListener('change', (e) => this.updateDelay(e.target.value));

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => this.handleKeyboardShortcuts(e));
    }

    showWelcomeMessage() {
        const resultsContainer = document.getElementById('results');
        if (resultsContainer && resultsContainer.innerHTML.includes('No results yet')) {
            resultsContainer.innerHTML = `
                <div class="text-center text-muted py-5 fade-in">
                    <i class="fas fa-shield-alt fa-3x mb-3 text-gradient"></i>
                    <h5 class="mb-3">Welcome to Api Service</h5>
                    <p class="mb-4">Professional card validation services at your fingertips</p>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="feature-item">
                                <i class="fas fa-bolt text-warning mb-2"></i>
                                <p class="small mb-0">Fast Processing</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="feature-item">
                                <i class="fas fa-shield-check text-success mb-2"></i>
                                <p class="small mb-0">Secure Validation</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="feature-item">
                                <i class="fas fa-chart-line text-info mb-2"></i>
                                <p class="small mb-0">Real-time Results</p>
                            </div>
                        </div>
                    </div>
                    <p class="small text-muted">Enter your cards above and click "Start Checking" to begin</p>
                </div>
            `;
        }
    }

    validateCardList(value) {
        const lines = value.split('\n').filter(line => line.trim());
        const validCards = [];
        const invalidCards = [];

        lines.forEach(line => {
            const parts = line.trim().split('|');
            if (parts.length === 4 && this.isValidCard(parts[0]) && this.isValidExpiry(parts[1], parts[2]) && this.isValidCVV(parts[3])) {
                validCards.push(line.trim());
            } else if (line.trim()) {
                invalidCards.push(line.trim());
            }
        });

        this.cards = validCards;
        this.updateCardCount();

        // Show validation feedback
        this.showValidationFeedback(validCards.length, invalidCards.length);
    }

    isValidCard(number) {
        const cleaned = number.replace(/\D/g, '');
        return cleaned.length >= 13 && cleaned.length <= 19 && /^\d+$/.test(cleaned);
    }

    isValidExpiry(month, year) {
        const m = parseInt(month);
        const y = parseInt(year);
        return m >= 1 && m <= 12 && y >= 0 && y <= 99;
    }

    isValidCVV(cvv) {
        return /^\d{3,4}$/.test(cvv);
    }

    showValidationFeedback(valid, invalid) {
        const cardListElement = document.getElementById('cardList');
        if (!cardListElement) return;

        // Remove existing feedback
        const existingFeedback = cardListElement.parentNode.querySelector('.validation-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }

        if (invalid > 0 || valid > 0) {
            const feedback = document.createElement('div');
            feedback.className = 'validation-feedback mt-2';
            feedback.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-success">
                        <i class="fas fa-check-circle me-1"></i>
                        ${valid} valid cards
                    </small>
                    ${invalid > 0 ? `
                        <small class="text-danger">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            ${invalid} invalid cards
                        </small>
                    ` : ''}
                </div>
            `;
            cardListElement.parentNode.appendChild(feedback);
        }
    }

    updateCardCount() {
        this.stats.total = this.cards.length;
        this.updateStats();
    }

    updateDelay(value) {
        this.delay = Math.max(1, parseInt(value) || 1) * 1000;
    }

    async startChecking() {
        if (this.isRunning) return;

        const cardList = document.getElementById('cardList')?.value.trim();
        if (!cardList) {
            this.showAlert('Please enter some cards to check', 'warning');
            return;
        }

        this.validateCardList(cardList);
        
        if (this.cards.length === 0) {
            this.showAlert('No valid cards found. Please check the format: 4111111111111111|12|25|123', 'danger');
            return;
        }

        this.isRunning = true;
        this.currentIndex = 0;
        this.stats = { total: this.cards.length, approved: 0, declined: 0 };
        
        this.updateControlButtons();
        this.clearResults();
        this.showProgressBar();
        
        this.addResult('info', `🚀 Starting check for ${this.cards.length} cards using ${this.getCheckerName()}...`);

        for (let i = 0; i < this.cards.length && this.isRunning; i++) {
            this.currentIndex = i;
            this.updateProgress();
            
            try {
                await this.checkCard(this.cards[i]);
            } catch (error) {
                this.addResult('declined', `❌ Error | ${this.cards[i]} - Connection failed | Api Service`);
                console.error('Check error:', error);
            }

            if (this.isRunning && i < this.cards.length - 1) {
                await this.sleep(this.delay);
            }
        }

        if (this.isRunning) {
            this.addResult('info', `✅ Check completed! Total: ${this.stats.total}, Approved: ${this.stats.approved}, Declined: ${this.stats.declined}`);
        }

        this.stopChecking();
    }

    async checkCard(card) {
        const checkerType = document.getElementById('checkerType')?.value || 'auth2';
        
        try {
            const response = await fetch(`${checkerType}.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `card=${encodeURIComponent(card)}&lista=${encodeURIComponent(card)}`
            });

            let result;
            const contentType = response.headers.get('content-type');
            
            if (contentType && contentType.includes('application/json')) {
                result = await response.json();
                this.handleJsonResponse(result, card);
            } else {
                result = await response.text();
                this.handleTextResponse(result, card);
            }
        } catch (error) {
            this.addResult('declined', `❌ Declined | ${card} - Network error | Api Service`);
            console.error('Network error:', error);
        }
    }

    handleJsonResponse(result, card) {
        if (result.status === 'Live' || result.status === 'success' || result.status === 'Live CCN' || result.status === 'Live CVV') {
            this.stats.approved++;
            this.addResult('approved', `✅ ${result.status} | ${card} - ${result.message} | Api Service`);
        } else {
            this.stats.declined++;
            this.addResult('declined', `❌ ${result.status} | ${card} - ${result.message} | Api Service`);
        }
        this.updateStats();
    }

    handleTextResponse(result, card) {
        // Enhanced text response parsing
        const resultText = result.trim();
        
        if (this.isApprovedResult(resultText)) {
            this.stats.approved++;
            this.addResult('approved', resultText);
        } else if (this.isWarningResult(resultText)) {
            // Count warnings as approved for statistics
            this.stats.approved++;
            this.addResult('warning', resultText);
        } else {
            this.stats.declined++;
            this.addResult('declined', resultText);
        }
        this.updateStats();
    }

    isApprovedResult(text) {
        const approvedKeywords = [
            '✅', 'CHARGED', 'Approved', 'successful', 'succeeded', 
            'LIVE', 'CVV', 'CCN', 'Payment method successfully added'
        ];
        return approvedKeywords.some(keyword => text.includes(keyword));
    }

    isWarningResult(text) {
        const warningKeywords = [
            '⚠️', '3D Secure', 'requires_action', 'INVALID_BILLING_ADDRESS',
            'INVALID_SECURITY_CODE', 'Account Restricted'
        ];
        return warningKeywords.some(keyword => text.includes(keyword));
    }

    stopChecking() {
        this.isRunning = false;
        this.updateControlButtons();
        this.hideProgressBar();
        document.getElementById('progressText').textContent = 'Ready to start';
    }

    clearResults() {
        const resultsContainer = document.getElementById('results');
        if (resultsContainer) {
            resultsContainer.innerHTML = '';
            this.showWelcomeMessage();
        }
        this.stats = { total: 0, approved: 0, declined: 0 };
        this.updateStats();
    }

    addResult(type, text) {
        const resultsContainer = document.getElementById('results');
        if (!resultsContainer) return;

        // Clear welcome message if it exists
        if (resultsContainer.innerHTML.includes('Welcome to Api Service')) {
            resultsContainer.innerHTML = '';
        }

        const resultElement = document.createElement('div');
        resultElement.className = `result-item result-${type} slide-in`;
        
        // Add appropriate icon and styling based on result type
        let icon = '';
        switch (type) {
            case 'approved':
                icon = '<i class="fas fa-check-circle text-success me-2"></i>';
                break;
            case 'declined':
                icon = '<i class="fas fa-times-circle text-danger me-2"></i>';
                break;
            case 'warning':
                icon = '<i class="fas fa-exclamation-triangle text-warning me-2"></i>';
                break;
            case 'info':
                icon = '<i class="fas fa-info-circle text-info me-2"></i>';
                break;
        }
        
        resultElement.innerHTML = `${icon}${text}`;
        
        // Add to top of results (newest first)
        resultsContainer.insertBefore(resultElement, resultsContainer.firstChild);
        
        // Auto-scroll to show new result
        resultsContainer.scrollTop = 0;

        // Limit results to prevent memory issues
        const maxResults = 1000;
        while (resultsContainer.children.length > maxResults) {
            resultsContainer.removeChild(resultsContainer.lastChild);
        }
    }

    updateStats() {
        const totalElement = document.getElementById('totalCards');
        const approvedElement = document.getElementById('approvedCards');
        const declinedElement = document.getElementById('declinedCards');

        if (totalElement) totalElement.textContent = this.stats.total;
        if (approvedElement) approvedElement.textContent = this.stats.approved;
        if (declinedElement) declinedElement.textContent = this.stats.declined;
    }

    updateControlButtons() {
        const startBtn = document.getElementById('startCheck');
        const stopBtn = document.getElementById('stopCheck');

        if (startBtn && stopBtn) {
            startBtn.disabled = this.isRunning;
            stopBtn.disabled = !this.isRunning;
            
            if (this.isRunning) {
                startBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Checking...';
            } else {
                startBtn.innerHTML = '<i class="fas fa-play me-2"></i>Start Checking';
            }
        }
    }

    showProgressBar() {
        const progressBar = document.getElementById('progressBar');
        if (progressBar) {
            progressBar.style.display = 'block';
        }
    }

    hideProgressBar() {
        const progressBar = document.getElementById('progressBar');
        if (progressBar) {
            progressBar.style.display = 'none';
        }
    }

    updateProgress() {
        const progress = ((this.currentIndex + 1) / this.cards.length) * 100;
        const progressBar = document.querySelector('.progress-bar');
        const progressText = document.getElementById('progressText');

        if (progressBar) {
            progressBar.style.width = `${progress}%`;
        }

        if (progressText) {
            progressText.textContent = `Checking ${this.currentIndex + 1} of ${this.cards.length} cards...`;
        }
    }

    getCheckerName() {
        const checkerType = document.getElementById('checkerType')?.value || 'auth2';
        const checkerNames = {
            'auth2': 'Auth2 Checker',
            'paypal': 'PayPal Checker',
            'puan': 'Puan Checker',
            'stripe40': 'Stripe40 Checker',
            'tumkart2': 'Tumkart2 Checker',
            'vbv': 'VBV Checker'
        };
        return checkerNames[checkerType] || 'Unknown Checker';
    }

    showAlert(message, type = 'info') {
        // Create alert element
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        alert.innerHTML = `
            <i class="fas fa-${this.getAlertIcon(type)} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(alert);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    getAlertIcon(type) {
        const icons = {
            'success': 'check-circle',
            'danger': 'exclamation-triangle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    handleKeyboardShortcuts(e) {
        // Ctrl/Cmd + Enter to start checking
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            if (!this.isRunning) {
                this.startChecking();
            }
        }
        
        // Escape to stop checking
        if (e.key === 'Escape' && this.isRunning) {
            e.preventDefault();
            this.stopChecking();
        }

        // Ctrl/Cmd + K to clear results
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            this.clearResults();
        }
    }

    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

// Enhanced DOM utilities
class DOMUtils {
    static addTooltips() {
        // Add tooltips to various elements
        const tooltipElements = [
            { selector: '#startCheck', title: 'Start checking cards (Ctrl+Enter)' },
            { selector: '#stopCheck', title: 'Stop current check (Escape)' },
            { selector: '#clearResults', title: 'Clear all results (Ctrl+K)' },
            { selector: '#checkerType', title: 'Select the checker type to use' },
            { selector: '#delay', title: 'Delay between each card check (seconds)' }
        ];

        tooltipElements.forEach(({ selector, title }) => {
            const element = document.querySelector(selector);
            if (element) {
                element.setAttribute('title', title);
                element.setAttribute('data-bs-toggle', 'tooltip');
            }
        });

        // Initialize Bootstrap tooltips if available
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }

    static addAnimations() {
        // Add entrance animations to cards
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('fade-in');
        });
    }

    static addSmoothScrolling() {
        // Smooth scrolling for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
}

// Export to save/load functionality
class DataExporter {
    static exportResults() {
        const resultsContainer = document.getElementById('results');
        if (!resultsContainer) return;

        const results = Array.from(resultsContainer.children).map(item => item.textContent);
        const data = {
            timestamp: new Date().toISOString(),
            checker: document.getElementById('checkerType')?.value || 'auth2',
            results: results,
            stats: checker.stats
        };

        this.downloadJSON(data, `api-service-results-${Date.now()}.json`);
    }

    static downloadJSON(data, filename) {
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    static exportCSV() {
        const resultsContainer = document.getElementById('results');
        if (!resultsContainer) return;

        const results = Array.from(resultsContainer.children).map(item => {
            const text = item.textContent;
            const status = item.classList.contains('result-approved') ? 'Approved' : 
                          item.classList.contains('result-warning') ? 'Warning' : 'Declined';
            return { status, result: text };
        });

        const csvContent = 'Status,Result\n' + 
            results.map(r => `"${r.status}","${r.result.replace(/"/g, '""')}"`).join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `api-service-results-${Date.now()}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
}

// Initialize the application
let checker;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize main checker
    checker = new ApiServiceChecker();
    
    // Initialize DOM utilities
    DOMUtils.addTooltips();
    DOMUtils.addAnimations();
    DOMUtils.addSmoothScrolling();

    // Add export functionality if needed
    const exportBtn = document.getElementById('exportResults');
    if (exportBtn) {
        exportBtn.addEventListener('click', () => DataExporter.exportResults());
    }

    // Performance optimization: Virtual scrolling for large result sets
    const resultsContainer = document.getElementById('results');
    if (resultsContainer) {
        let isScrolling = false;
        resultsContainer.addEventListener('scroll', () => {
            if (!isScrolling) {
                requestAnimationFrame(() => {
                    // Handle scroll performance optimizations here if needed
                    isScrolling = false;
                });
            }
            isScrolling = true;
        });
    }

    // Auto-save settings
    const saveSettings = () => {
        const settings = {
            checkerType: document.getElementById('checkerType')?.value,
            delay: document.getElementById('delay')?.value
        };
        localStorage.setItem('apiServiceSettings', JSON.stringify(settings));
    };

    const loadSettings = () => {
        const settings = localStorage.getItem('apiServiceSettings');
        if (settings) {
            try {
                const parsed = JSON.parse(settings);
                if (parsed.checkerType) {
                    const checkerSelect = document.getElementById('checkerType');
                    if (checkerSelect) checkerSelect.value = parsed.checkerType;
                }
                if (parsed.delay) {
                    const delayInput = document.getElementById('delay');
                    if (delayInput) delayInput.value = parsed.delay;
                }
            } catch (e) {
                console.warn('Could not load settings:', e);
            }
        }
    };

    // Load settings on startup
    loadSettings();

    // Save settings on change
    document.getElementById('checkerType')?.addEventListener('change', saveSettings);
    document.getElementById('delay')?.addEventListener('change', saveSettings);

    console.log('🚀 Api Service initialized successfully');
});

// Add global styles for better UX
const style = document.createElement('style');
style.textContent = `
    .feature-item {
        padding: 1rem;
        text-align: center;
        border-radius: 8px;
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }
    
    .feature-item:hover {
        transform: translateY(-2px);
        background: var(--bg-card);
        border-color: var(--border-accent);
    }
    
    .feature-item i {
        font-size: 1.5rem;
        display: block;
    }
    
    .validation-feedback {
        font-size: 0.875rem;
        padding: 0.5rem;
        background: var(--bg-tertiary);
        border-radius: 6px;
        border: 1px solid var(--border-color);
    }
`;
document.head.appendChild(style);

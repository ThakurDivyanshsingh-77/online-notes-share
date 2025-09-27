// Main JavaScript for Online Notes Sharing

// Confirm delete action
function confirmDelete(noteId) {
    return confirm('Are you sure you want to delete this note? This action cannot be undone.');
}

// Toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);

    // Show toast
    setTimeout(() => toast.classList.add('show'), 100);

    // Hide and remove after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Dark mode toggle
function toggleDarkMode() {
    const body = document.body;
    const themeToggle = document.getElementById('theme-toggle');
    const icon = themeToggle.querySelector('i');

    if (body.classList.contains('dark')) {
        body.classList.remove('dark');
        icon.className = 'fas fa-moon';
        localStorage.setItem('theme', 'light');
    } else {
        body.classList.add('dark');
        icon.className = 'fas fa-sun';
        localStorage.setItem('theme', 'dark');
    }
}

// Load theme from localStorage
function loadTheme() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    const body = document.body;
    const themeToggle = document.getElementById('theme-toggle');
    const icon = themeToggle ? themeToggle.querySelector('i') : null;

    if (savedTheme === 'dark') {
        body.classList.add('dark');
        if (icon) icon.className = 'fas fa-sun';
    } else {
        if (icon) icon.className = 'fas fa-moon';
    }
}

// Star rating handler (client-side for now; AJAX for save)
function initStarRatings() {
    const stars = document.querySelectorAll('.star-rating');
    stars.forEach(ratingContainer => {
        const noteId = ratingContainer.dataset.noteId;
        const starsElements = ratingContainer.querySelectorAll('i');
        const currentRating = parseFloat(ratingContainer.dataset.rating) || 0;

        // Display current rating
        starsElements.forEach((star, index) => {
            if (index < currentRating) {
                star.classList.add('filled');
            }
        });

        // Click to rate
        starsElements.forEach((star, index) => {
            star.addEventListener('click', () => {
                const newRating = index + 1;
                starsElements.forEach((s, i) => {
                    if (i < newRating) {
                        s.classList.add('filled');
                    } else {
                        s.classList.remove('filled');
                    }
                });

                // TODO: Send AJAX to save rating
                fetch(`rate_note.php?id=${noteId}&rating=${newRating}`, { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Rating saved!', 'success');
                            // Update average display if needed
                        } else {
                            showToast('Failed to save rating.', 'error');
                        }
                    })
                    .catch(() => showToast('Error saving rating.', 'error'));
            });
        });
    });
}

// Live search (client-side filtering)
function initLiveSearch() {
    const searchInput = document.querySelector('input[name="search"]');
    if (!searchInput) return;

    const noteCards = document.querySelectorAll('.note-card');
    const debounce = (func, delay) => {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    };

    const filterNotes = debounce((query) => {
        const lowerQuery = query.toLowerCase();
        noteCards.forEach(card => {
            const title = card.querySelector('h3')?.textContent.toLowerCase() || '';
            const subject = card.querySelector('p b')?.nextSibling?.textContent.toLowerCase() || '';
            const desc = card.textContent.toLowerCase();

            if (title.includes(lowerQuery) || subject.includes(lowerQuery) || desc.includes(lowerQuery)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }, 300);

    searchInput.addEventListener('input', (e) => filterNotes(e.target.value));
}

// Document ready
document.addEventListener('DOMContentLoaded', function() {
    loadTheme();

    // Theme toggle
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleDarkMode);
    }

    // File validation for upload
    const fileInput = document.getElementById('note');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                if (file.type !== 'application/pdf') {
                    showToast('Please select a PDF file.', 'error');
                    this.value = '';
                } else if (file.size > 10 * 1024 * 1024) {
                    showToast('File size must be less than 10MB.', 'error');
                    this.value = '';
                } else {
                    showToast('Valid file selected.', 'success');
                }
            }
        });
    }

    // Delete confirmation (update to use toast if needed)
    const deleteLinks = document.querySelectorAll('a[href*="delete"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirmDelete()) {
                e.preventDefault();
            }
        });
    });

    // Initialize features
    initLiveSearch();
    initStarRatings();

    // Form submission loading
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.textContent;
                submitBtn.innerHTML = '<span class="loading"></span> ' + originalText;
                submitBtn.disabled = true;
            }
        });
    });

    // Smooth scrolling for pagination (optional)
    const paginationLinks = document.querySelectorAll('.pagination a');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Add loading if needed
            showToast('Loading...', 'info');
        });
    });
});

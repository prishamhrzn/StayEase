// ============================================================
// StayEase — Main JavaScript
// ============================================================

document.addEventListener('DOMContentLoaded', () => {

    // ── Navbar scroll effect ──────────────────────────────
    const navbar = document.getElementById('navbar');
    if (navbar) {
        const updateNav = () => navbar.classList.toggle('scrolled', window.scrollY > 40);
        window.addEventListener('scroll', updateNav, { passive: true });
        updateNav();
    }

    // ── Hamburger menu ────────────────────────────────────
    const hamburger = document.getElementById('hamburger');
    const navLinks  = document.getElementById('navLinks');
    if (hamburger && navLinks) {
        hamburger.addEventListener('click', () => {
            navLinks.classList.toggle('open');
            hamburger.classList.toggle('active');
        });
        document.addEventListener('click', e => {
            if (!navbar.contains(e.target)) navLinks.classList.remove('open');
        });
    }

    // ── Auto-dismiss flash messages ───────────────────────
    document.querySelectorAll('.alert').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity .4s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, 4000);
    });

    // ── Modal helpers ─────────────────────────────────────
    window.openModal = id => {
        const m = document.getElementById(id);
        if (m) { m.classList.add('open'); document.body.style.overflow = 'hidden'; }
    };
    window.closeModal = id => {
        const m = document.getElementById(id);
        if (m) { m.classList.remove('open'); document.body.style.overflow = ''; }
    };
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', e => {
            if (e.target === overlay) closeModal(overlay.id);
        });
    });
    document.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', () => closeModal(btn.dataset.modalClose));
    });

    // ── Form Validation ───────────────────────────────────
    const validators = {
        required: v => v.trim() !== '',
        email:    v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v),
        minLen:   (v, n) => v.length >= n,
        match:    (v, id) => v === document.getElementById(id)?.value,
        phone:    v => /^[\d\+\-\s\(\)]{7,15}$/.test(v),
        price:    v => !isNaN(v) && parseFloat(v) > 0,
        rating:   v => !isNaN(v) && parseFloat(v) >= 0 && parseFloat(v) <= 5,
    };

    function validateField(input) {
        const rules    = input.dataset.validate?.split('|') || [];
        const errEl    = input.parentElement.querySelector('.form-error')
                      || input.closest('.form-group')?.querySelector('.form-error');
        let   valid    = true;
        let   errorMsg = '';

        for (const rule of rules) {
            const [name, arg] = rule.split(':');
            if (!validators[name]) continue;
            const ok = validators[name](input.value, arg);
            if (!ok) {
                valid    = false;
                errorMsg = input.dataset[`error${name.charAt(0).toUpperCase() + name.slice(1)}`]
                         || getDefaultError(name, arg, input);
                break;
            }
        }

        input.classList.toggle('error', !valid);
        if (errEl) {
            errEl.textContent = errorMsg;
            errEl.classList.toggle('visible', !valid);
        }
        return valid;
    }

    function getDefaultError(rule, arg, input) {
        const msgs = {
            required: `${input.dataset.label || 'This field'} is required`,
            email:    'Please enter a valid email address',
            minLen:   `Minimum ${arg} characters required`,
            match:    'Passwords do not match',
            phone:    'Enter a valid phone number',
            price:    'Enter a valid price',
            rating:   'Rating must be between 0 and 5',
        };
        return msgs[rule] || 'Invalid value';
    }

    // Attach live validation
    document.querySelectorAll('[data-validate]').forEach(input => {
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => {
            if (input.classList.contains('error')) validateField(input);
        });
    });

    // Full-form validation on submit
    document.querySelectorAll('form[data-validate-form]').forEach(form => {
        form.addEventListener('submit', e => {
            const fields = form.querySelectorAll('[data-validate]');
            let allValid = true;
            fields.forEach(f => { if (!validateField(f)) allValid = false; });
            if (!allValid) {
                e.preventDefault();
                form.querySelector('.error')?.focus();
            } else {
                const btn = form.querySelector('[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner"></span> Processing…';
                }
            }
        });
    });

    // ── Search form (homepage) ────────────────────────────
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', e => {
            e.preventDefault();
            const loc = document.getElementById('searchLocation')?.value.trim();
            const ci  = document.getElementById('searchCheckIn')?.value;
            const co  = document.getElementById('searchCheckOut')?.value;
            const g   = document.getElementById('searchGuests')?.value;
            const p   = new URLSearchParams();
            if (loc) p.set('location', loc);
            if (ci)  p.set('check_in', ci);
            if (co)  p.set('check_out', co);
            if (g)   p.set('guests', g);
            window.location.href = `hotels.php?${p.toString()}`;
        });
        // Set min date to today
        const today = new Date().toISOString().split('T')[0];
        const checkIn  = document.getElementById('searchCheckIn');
        const checkOut = document.getElementById('searchCheckOut');
        if (checkIn)  { checkIn.min  = today; checkIn.value = today; }
        if (checkOut) { checkOut.min = today; }
        if (checkIn && checkOut) {
            checkIn.addEventListener('change', () => {
                checkOut.min   = checkIn.value;
                if (checkOut.value && checkOut.value <= checkIn.value) checkOut.value = '';
            });
        }
    }

    // ── Hotel detail — booking form date constraints ───────
    const bookCheckIn  = document.getElementById('check_in');
    const bookCheckOut = document.getElementById('check_out');
    const totalDisplay = document.getElementById('totalPriceDisplay');
    const pricePerNight = parseFloat(document.getElementById('pricePerNight')?.value || 0);

    if (bookCheckIn && bookCheckOut) {
        const today = new Date().toISOString().split('T')[0];
        bookCheckIn.min  = today;
        bookCheckOut.min = today;

        function updateTotal() {
            if (!bookCheckIn.value || !bookCheckOut.value || !totalDisplay) return;
            const d1 = new Date(bookCheckIn.value);
            const d2 = new Date(bookCheckOut.value);
            const nights = Math.round((d2 - d1) / 86400000);
            if (nights > 0) {
                totalDisplay.textContent = `NPR ${(nights * pricePerNight).toLocaleString()} (${nights} night${nights>1?'s':''})`;
            } else {
                totalDisplay.textContent = '';
            }
        }

        bookCheckIn.addEventListener('change', () => {
            bookCheckOut.min = bookCheckIn.value;
            if (bookCheckOut.value && bookCheckOut.value <= bookCheckIn.value) bookCheckOut.value = '';
            updateTotal();
        });
        bookCheckOut.addEventListener('change', updateTotal);
    }

    // ── Admin — confirm delete ────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', e => {
            if (!confirm(el.dataset.confirm || 'Are you sure?')) e.preventDefault();
        });
    });

    // ── Admin — image preview ─────────────────────────────
    const imageInput   = document.getElementById('hotelImage');
    const imagePreview = document.getElementById('imagePreview');
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', () => {
            const file = imageInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // ── Filters page live filter ──────────────────────────
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.querySelectorAll('select, input').forEach(el => {
            el.addEventListener('change', () => filterForm.submit());
        });
    }

    // ── Animate numbers (stats) ───────────────────────────
    function animateNumber(el, target, duration = 1500) {
        const start = performance.now();
        const update = ts => {
            const prog = Math.min((ts - start) / duration, 1);
            el.textContent = Math.floor(prog * target).toLocaleString();
            if (prog < 1) requestAnimationFrame(update);
            else el.textContent = target.toLocaleString() + (el.dataset.suffix || '');
        };
        requestAnimationFrame(update);
    }

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                animateNumber(el, parseInt(el.dataset.target, 10));
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('[data-target]').forEach(el => observer.observe(el));

    // ── Fade-in on scroll ─────────────────────────────────
    const fadeObserver = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.style.opacity = '1';
                e.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.hotel-card, .testimonial-card, .stat-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity .5s ease, transform .5s ease';
        fadeObserver.observe(el);
    });
});

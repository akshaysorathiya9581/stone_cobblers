@extends('layouts.admin')

@section('title', 'Add Customer')

@push('css')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="search-bar">
                <i>🔍</i>
                <input type="text" placeholder="Search customers, email, phone...">
            </div>

            <div class="header-actions">
                <a href="#export" class="header-btn secondary" role="button">
                    <i>📤</i> Export
                </a>

                <a href="{{ route('admin.customers.create') }}" class="header-btn primary" role="button">
                    <i>➕</i> New Customer
                </a>

                <a href="/account" class="user-avatar" aria-label="Open profile">BM</a>
            </div>
        </div>

        <!-- Content -->
        <div class="content bg-content">
            <div class="form-container">
                <meta name="csrf-token" content="{{ csrf_token() }}">

                <!-- Global Progress -->
                <div class="progress-section">
                    <div class="step-indicator"></div>
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                    <div class="progress-percentage"></div>
                </div>

                <!-- ✅ All steps + buttons live INSIDE one <form> -->
                <form id="customerForm" novalidate>

                    <!-- Step 1 -->
                    <div class="form-container-view active">
                        <div class="form-header">
                            <div class="form-icon icon-person"></div>
                            <h1 class="form-title">Let's start with your details</h1>
                            <p class="form-subtitle">We'll use this information to contact you about your project</p>
                        </div>
                        <div class="form-fields">
                            <div class="field-row">
                                <div class="form-field">
                                    <label class="form-label required" for="first_name">First Name</label>
                                    <input type="text" class="form-input" id="first_name" name="first_name"
                                        placeholder="Enter your first name" required>
                                    <small class="error-text" data-for="first_name"></small>
                                </div>
                                <div class="form-field">
                                    <label class="form-label required" for="last_name">Last Name</label>
                                    <input type="text" class="form-input" id="last_name" name="last_name"
                                        placeholder="Enter your last name" required>
                                    <small class="error-text" data-for="last_name"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="form-container-view">
                        <div class="form-header">
                            <div class="form-icon icon-envelope"></div>
                            <h1 class="form-title">How can we reach you?</h1>
                            <p class="form-subtitle">Your contact information helps us provide updates on your project</p>
                        </div>
                        <div class="form-fields">
                            <div class="field-row">
                                <div class="form-field">
                                    <label class="form-label required" for="email">Email Address</label>
                                    <input type="email" class="form-input" id="email" name="email"
                                        placeholder="your.email@example.com" required>
                                    <small class="error-text" data-for="email"></small>
                                </div>
                                <div class="form-field">
                                    <label class="form-label required" for="phone">Phone Number</label>
                                    <input type="tel" class="form-input" id="phone" name="phone"
                                        placeholder="(555) 123-4567" required>
                                    <small class="error-text" data-for="phone"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="form-container-view">
                        <div class="form-header">
                            <div class="form-icon icon-home"></div>
                            <h1 class="form-title">Where are you located?</h1>
                            <p class="form-subtitle">Your address helps us plan project logistics and delivery</p>
                        </div>
                        <div class="form-fields">
                            <div class="field-row">
                                <div class="form-field">
                                    <label class="form-label required" for="address">Street Address</label>
                                    <input type="text" class="form-input" id="address" name="address"
                                        placeholder="123 Main Street" required>
                                    <small class="error-text" data-for="address"></small>
                                </div>
                                <div class="form-field">
                                    <label class="form-label required" for="city">City</label>
                                    <input type="text" class="form-input" id="city" name="city"
                                        placeholder="Your City" required>
                                    <small class="error-text" data-for="city"></small>
                                </div>
                                <div class="form-field">
                                    <label class="form-label required" for="state">State</label>
                                    <input type="text" class="form-input" id="state" name="state"
                                        placeholder="Your State" required>
                                    <small class="error-text" data-for="state"></small>
                                </div>
                                <div class="form-field">
                                    <label class="form-label required" for="zipCode">ZIP Code</label>
                                    <input type="text" class="form-input" id="zipCode" name="zipCode"
                                        placeholder="12345" required>
                                    <small class="error-text" data-for="zipCode"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="form-container-view">
                        <div class="form-header">
                            <div class="form-icon icon-notes"></div>
                            <h1 class="form-title">Additional Information</h1>
                            <p class="form-subtitle">Any other details that might help us serve you better</p>
                        </div>
                        <div class="form-fields">
                            <div class="field-row">
                                <div class="form-field single">
                                    <label class="form-label" for="additionalNotes">Additional Notes</label>
                                    <textarea class="form-input" id="additionalNotes" name="additionalNotes" rows="4"
                                        placeholder="Any special requirements, preferences, or questions..."></textarea>
                                    <small class="error-text" data-for="additionalNotes"></small>
                                </div>
                                <div class="form-field">
                                    <label class="form-label" for="referralSource">How did you hear about us?</label>
                                    <select class="form-input" id="referralSource" name="referralSource">
                                        <option value="">Select How did you hear about us?</option>
                                        <option value="Google Search">Google Search</option>
                                        <option value="Social Media">Social Media</option>
                                        <option value="Referral">Referral</option>
                                        <option value="Advertisement">Advertisement</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <small class="error-text" data-for="referralSource"></small>
                                </div>
                            </div>
                            <div class="field-row">
                                <div class="form-field">
                                    <label class="form-label" for="customer_status">Status</label>
                                    <select name="customer_status" id="customer_status" class="form-input">
                                        <option value="">Select Status</option>
                                        @foreach (get_customer_status_list() as $status)
                                            <option value="{{ $status['id'] }}"
                                                @if (old('customer_status') == $status['id']) selected @endif>
                                                {{ $status['text'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5 (Review) -->
                    <div class="form-container-view">
                        <div class="form-header">
                            <div class="form-icon icon-check"></div>
                            <h1 class="form-title">Review &amp; Submit</h1>
                            <p class="form-subtitle">Please review your information before submitting</p>
                        </div>
                        <div class="form-fields">
                            <div class="form-field">
                                <h3 style="margin-bottom:20px;color:#333;">Please review your information:</h3>
                                <div id="review-content"
                                    style="background:#f8f9fa;padding:20px;border-radius:8px;margin-bottom:20px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 6 (Success) -->
                    <div class="form-container-view" id="success-step">
                        <div style="text-align:center;padding:40px;">
                            <div class="form-icon icon-check"
                                style="background-color:rgb(22,163,74);color:#fff;border-color:rgb(22,163,74);">✓</div>
                            <h1 class="form-title" style="color:rgb(22,163,74);margin:20px 0;">Customer Added
                                Successfully!</h1>
                            <p class="form-subtitle" style="margin-bottom:30px;">Thank you for choosing The Stone
                                Cobblers. We'll be in touch soon!</p>
                            <a href="{{ route('admin.customers.index') }}" class="nav-btn next"
                                style="text-decoration:none;display:inline-flex;"><i>🏠</i> Back to Customers</a>
                        </div>
                    </div>

                    <!-- Navigation (inside form) -->
                    <div class="form-navigation">
                        <button type="button" class="nav-btn previous">← Previous</button>
                        <button type="submit" class="nav-btn next">Next →</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Make sure jQuery is loaded in your layout before this script.
        (function($) {
            $(function() {

                const $form = $('#customerForm');
                const $steps = $('.form-container-view');
                const $prevBtn = $('.form-navigation .nav-btn.previous');
                const $nextBtn = $('.form-navigation .nav-btn.next');

                const $progressSection = $('.progress-section');
                const $progressFill = $('.progress-fill');
                const $stepIndicator = $('.step-indicator');
                const $progressPercent = $('.progress-percentage');
                const $navWrapper = $('.form-navigation');

                const csrf = $('meta[name="csrf-token"]').attr('content');

                let currentStep = 0;
                const totalSteps = $steps.length - 1; // exclude success step from progress

                // --- helpers ---
                function escapeHtml(t) {
                    return String(t).replace(/[&<>"']/g, s => ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        '\'': '&#39;'
                    } [s]));
                }

                function labelOf($input) {
                    return ($input.closest('.form-field').find('.form-label').text() || '').replace(/\s*\*$/,
                        '');
                }

                function setError($input, msg) {
                    $input.addClass('is-invalid');
                    const name = $input.attr('name');
                    const $small = $input.closest('.form-field').find(`.error-text[data-for="${name}"]`);
                    if ($small.length) $small.text(msg || 'This field is required.');
                }

                function clearError($input) {
                    $input.removeClass('is-invalid');
                    const name = $input.attr('name');
                    const $small = $input.closest('.form-field').find(`.error-text[data-for="${name}"]`);
                    if ($small.length) $small.text('');
                }

                function showStep(n) {
                    $steps.removeClass('active').eq(n).addClass('active');
                    updateProgress();
                    updateButtons();

                    if (n === totalSteps - 1) fillReview();

                    // hide progress + nav on success step
                    if (n === $steps.length - 1) {
                        $navWrapper.hide();
                        $progressSection.hide();
                    } else {
                        $navWrapper.show();
                        $progressSection.show();
                    }
                }

                function updateProgress() {
                    if (currentStep < totalSteps) {
                        const progress = ((currentStep + 1) / totalSteps) * 100;
                        $progressFill.css('width', progress + '%');
                        $stepIndicator.text(`Step ${currentStep + 1} of ${totalSteps}`);
                        $progressPercent.text(Math.round(progress) + '% Complete');
                    }
                }

                function updateButtons() {
                    $prevBtn.prop('disabled', currentStep === 0);
                    if (currentStep === totalSteps - 1) {
                        $nextBtn.text('Submit ✓');
                    } else {
                        $nextBtn.html('Next <i>→</i>');
                    }
                }

                function validateCurrentStep() {
                    let ok = true;
                    const $required = $steps.eq(currentStep).find('.form-input[required]');
                    $required.each(function() {
                        const $el = $(this);
                        const val = String($el.val() || '').trim();
                        if (!val) {
                            ok = false;
                            setError($el, (labelOf($el) || 'This field') + ' is required.');
                            return; // continue each
                        }
                        if ($el.attr('type') === 'email') {
                            // Use browser validity via a tmp input for portability
                            const tmp = document.createElement('input');
                            tmp.type = 'email';
                            tmp.value = val;
                            if (!tmp.checkValidity()) {
                                ok = false;
                                setError($el, 'Please enter a valid email address.');
                                return;
                            }
                        }
                        clearError($el);
                    });

                    // If current step has email, enforce the uniqueness flag (if checked already)
                    const $emailEl = $steps.eq(currentStep).find('#email');
                    if ($emailEl.length && emailChecked && emailUnique === false) {
                        ok = false;
                        setError($emailEl, emailMessage || 'This email is already registered.');
                    }
                    return ok;
                }

                function fillReview() {
                    const $review = $('#review-content');
                    if (!$review.length) return;

                    const $fields = $('.form-input').filter(function() {
                        return !!$(this).attr('name');
                    });
                    const html = $fields.map(function() {
                        const $f = $(this);
                        const label = labelOf($f) || $f.attr('name');
                        const val = $f.val();
                        return `<div style="margin-bottom:6px"><strong>${escapeHtml(label)}:</strong> ${val ? escapeHtml(val) : '<em>-</em>'}</div>`;
                    }).get().join('');
                    $review.html(html || '<div>No data entered yet.</div>');
                }

                function collectPayload() {
                    const payload = {};
                    $('.form-input').each(function() {
                        const $el = $(this);
                        const name = $el.attr('name');
                        if (name) payload[name] = $el.val();
                    });
                    return payload;
                }

                // ---- Email uniqueness (on blur) ----
                const $email = $('#email');
                let emailUnique = true; // last known
                let emailChecked = false; // whether we've checked current value
                let emailMessage = '';
                const emailCache = {}; // cache by email string

                async function checkEmailUnique(email) {
                    const key = String(email || '').trim().toLowerCase();
                    if (!key) {
                        emailChecked = false;
                        emailUnique = false;
                        emailMessage = 'Email is required.';
                        return {
                            unique: false,
                            message: emailMessage
                        };
                    }

                    // Simple local format guard
                    const tmp = document.createElement('input');
                    tmp.type = 'email';
                    tmp.value = key;
                    if (!tmp.checkValidity()) {
                        emailChecked = true;
                        emailUnique = false;
                        emailMessage = 'Please enter a valid email address.';
                        return {
                            unique: false,
                            message: emailMessage
                        };
                    }

                    if (emailCache[key] !== undefined) {
                        const {
                            unique,
                            message
                        } = emailCache[key];
                        emailChecked = true;
                        emailUnique = unique;
                        emailMessage = message || '';
                        return emailCache[key];
                    }

                    try {
                        const res = await $.ajax({
                            url: `{{ route('admin.customers.check-email') }}`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            contentType: 'application/json',
                            data: JSON.stringify({
                                email: key
                            })
                        });
                        // res expected: { unique: bool, message: string }
                        emailCache[key] = {
                            unique: !!res.unique,
                            message: res.message || ''
                        };
                        emailChecked = true;
                        emailUnique = !!res.unique;
                        emailMessage = res.message || '';
                        return emailCache[key];
                    } catch (e) {
                        // If the check fails (network), don't hard-block. Treat as unknown/ok.
                        emailChecked = true;
                        emailUnique = true;
                        emailMessage = '';
                        return {
                            unique: true,
                            message: ''
                        };
                    }
                }

                // blur: check and show inline error
                if ($email.length) {
                    $email.on('blur', async function() {
                        const val = $(this).val();
                        const {
                            unique,
                            message
                        } = await checkEmailUnique(val);
                        if (!unique) setError($email, message ||
                            'This email is already registered.');
                        else clearError($email);
                    });

                    // input: debounce re-check
                    let emailDebounce;
                    $email.on('input', function() {
                        clearError($email);
                        emailChecked = false; // value changed
                        if (emailDebounce) clearTimeout(emailDebounce);
                        emailDebounce = setTimeout(async () => {
                            const {
                                unique,
                                message
                            } = await checkEmailUnique($email.val());
                            if (!unique) setError($email, message ||
                                'This email is already registered.');
                        }, 450);
                    });
                }

                // ---- AJAX Submit ----
                async function submitFormAjax() {
                    const payload = collectPayload();
                    $nextBtn.prop('disabled', true);

                    try {
                        const res = await $.ajax({
                            url: `{{ route('admin.customers.store') }}`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            contentType: 'application/json',
                            data: JSON.stringify(payload)
                        });

                        // success → render + show last step
                        renderSuccess(res); // expects { status:'ok', message:'...', id: N }
                        currentStep = $steps.length - 1;
                        showStep(currentStep);

                    } catch (xhr) {
                        if (xhr.status === 422) {
                            const json = xhr.responseJSON || {};
                            const errs = json.errors || {};

                            // Put first error under its field (keeps your inline UX)
                            $.each(errs, function(name, msgs) {
                                const $el = $form.find(`[name="${name}"]`);
                                if ($el.length) setError($el, msgs && msgs[0] ? msgs[0] :
                                    'Invalid value.');
                            });

                            // Jump to the first errored step
                            const firstField = Object.keys(errs)[0];
                            if (firstField) {
                                const idx = $steps.toArray().findIndex(sec => $(sec).find(
                                    `[name="${firstField}"]`).length);
                                if (idx >= 0) {
                                    currentStep = idx;
                                    showStep(currentStep);
                                }
                            }
                        } else {
                            alert('Something went wrong. Please try again.');
                        }
                    } finally {
                        $nextBtn.prop('disabled', false);
                    }
                }

                function renderSuccess(data) {
                    const idSuffix = data && data.id ? ` #${data.id}` : '';
                    $('#success-title').text(`Customer Added Successfully${idSuffix}`);
                    $('#success-msg').text((data && data.message) || 'Customer saved successfully!');
                }

                // ---- Nav Buttons ----
                $prevBtn.on('click', function(e) {
                    e.preventDefault();
                    if (currentStep > 0) {
                        currentStep--;
                        showStep(currentStep);
                    }
                });
                $nextBtn.on('click', async function(e) {
                    e.preventDefault();

                    // If on review step, submit
                    if (currentStep === totalSteps - 1) {
                        await submitFormAjax();
                        return;
                    }

                    // Normal step: validate
                    if (!validateCurrentStep()) return;

                    // If this step has email and we haven't checked uniqueness yet, force-check
                    const $emailEl = $steps.eq(currentStep).find('#email');
                    if ($emailEl.length) {
                        const val = $emailEl.val();
                        const {
                            unique,
                            message
                        } = await checkEmailUnique(val);
                        if (!unique) {
                            setError($emailEl, message || 'This email is already registered.');
                            return;
                        }
                    }

                    currentStep++;
                    showStep(currentStep);
                });

                // live clear on typing for all inputs
                $('.form-input').on('input', function() {
                    const $el = $(this);
                    if (String($el.val() || '').trim()) clearError($el);
                });

                // init
                showStep(currentStep);

            });
        })(jQuery);
    </script>
@endpush

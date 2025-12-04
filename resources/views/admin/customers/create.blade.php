@extends('layouts.admin')

@section('title', 'Add Customer')

@push('css')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <x-header
            :export-url="null"
            :create-url="route('admin.customers.create')"
            export-label="Export Customers"
            create-label="New Customer"
        />

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
                                    <select class="form-input custom-select" id="referralSource" name="referralSource" data-placeholder="Select How did you hear about us?">
                                        <option></option>
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
                                    <select name="customer_status" id="customer_status" class="form-input custom-select" data-placeholder="Select Status">
                                        <option></option>
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

                    <!-- Step 5 (Review) - role selector added here (last step before submit) -->
                    <div class="form-container-view">
                        <div class="form-header">
                            <div class="form-icon icon-check"></div>
                            <h1 class="form-title">Review &amp; Submit</h1>
                            <p class="form-subtitle">Please review your information before submitting</p>
                        </div>
                        <div class="form-fields">
                            <!-- Role selector (Admin / Customer) -->
                            <div class="form-field" style="margin-bottom:12px;">
                                <label class="form-label required">Role</label>
                                <div style="display:flex;gap:12px;align-items:center;margin-top:6px;">
                                    <label style="display:inline-flex;align-items:center;gap:6px;">
                                        <input type="radio" name="role" value="customer" class="form-input" checked> Customer
                                    </label>
                                    <label style="display:inline-flex;align-items:center;gap:6px;">
                                        <input type="radio" name="role" value="admin" class="form-input"> Admin
                                    </label>
                                </div>
                                <small class="error-text" data-for="role"></small>
                            </div>

                            <!-- Modules (shown for both roles but filtered by allowed list) -->
                            <div class="form-field" id="modules-wrapper" style="margin-bottom:14px;">
                                <label class="form-label">Modules</label>
                                <div id="modules-list" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:6px;">
                                    <!-- include all modules possible; JS will show/hide according to role -->
                                    <label data-mod="dashboard"><input data-mod="dashboard" type="checkbox" name="modules[]" value="dashboard" class="form-input"> Dashboard</label>
                                    <label data-mod="projects"><input data-mod="projects" type="checkbox" name="modules[]" value="projects" class="form-input"> Projects</label>
                                    <label data-mod="files"><input data-mod="files" type="checkbox" name="modules[]" value="files" class="form-input"> Files</label>
                                    <label data-mod="quotes"><input data-mod="quotes" type="checkbox" name="modules[]" value="quotes" class="form-input"> Quotes</label>
                                    <label data-mod="users"><input data-mod="users" type="checkbox" name="modules[]" value="users" class="form-input"> Users</label>
                                    <label data-mod="settings"><input data-mod="settings" type="checkbox" name="modules[]" value="settings" class="form-input"> Settings</label>
                                </div>
                                <small class="error-text" data-for="modules[]"></small>
                            </div>

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
                                style="background-color:rgb(22,163,74);color:#fff;border-color:rgb(22,163,74);"><i class="fas fa-check"></i></div>
                            <h1 id="success-title" class="form-title" style="color:rgb(22,163,74);margin:20px 0;">Customer Added Successfully!</h1>
                            <p id="success-msg" class="form-subtitle" style="margin-bottom:30px;">Thank you for choosing The Stone Cobblers. We'll be in touch soon!</p>
                            <a href="{{ route('admin.customers.index') }}" class="nav-btn next"
                                style="text-decoration:none;display:inline-flex;"><i class="fas fa-home"></i> Back to Customers</a>
                        </div>
                    </div>

                    <!-- Navigation (inside form) -->
                    <div class="form-navigation">
                        <button type="button" class="nav-btn previous">← Previous</button>
                        <button type="submit" class="nav-btn next">Next <i class="fas fa-arrow-right"></i></button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
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
                    }[s]));
                }

                function labelOf($input) {
                    return ($input.closest('.form-field').find('.form-label').text() || '').replace(/\s*\*$/, '');
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
                        $nextBtn.html('Submit <i class="fas fa-check"></i>');
                    } else {
                        $nextBtn.html('Next <i class="fas fa-arrow-right"></i>');
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
                            return;
                        }
                        if ($el.attr('type') === 'email') {
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

                // ---- Review rendering (skip role radios and modules[] in auto-list) ----
                function fillReview() {
                    const $review = $('#review-content');
                    if (!$review.length) return;

                    const $fields = $('.form-input').filter(function() {
                        return !!$(this).attr('name');
                    });

                    const html = $fields.map(function() {
                        const $f = $(this);
                        const name = $f.attr('name');

                        // skip modules[] and role here (we will render them explicitly)
                        if (name === 'modules[]' || name === 'role') return null;

                        if ($f.attr('type') === 'radio') {
                            if (!$f.is(':checked')) return null;
                        }
                        if ($f.attr('type') === 'checkbox' && !$f.is(':checked')) return null;

                        const label = labelOf($f) || name;
                        const val = $f.val();
                        return `<div style="margin-bottom:6px"><strong>${escapeHtml(label)}:</strong> ${val ? escapeHtml(val) : '<em>-</em>'}</div>`;
                    }).get().join('');

                    $review.html(html || '<div>No data entered yet.</div>');

                    // render role & modules once
                    const role = $('input[name="role"]:checked').val() || 'customer';
                    $review.append(`<div style="margin-top:8px"><strong>Role:</strong> ${escapeHtml(role)}</div>`);
                }

                // ---- Collect payload (always send modules array) ----
                function collectPayload() {
                    const payload = {};
                    $('.form-input').each(function() {
                        const $el = $(this);
                        const name = $el.attr('name');
                        if (!name) return;

                        if (name === 'modules[]') return; // modules handled separately
                        if (name === 'role') {
                            if (!$el.is(':checked')) return; // only the checked radio
                        }
                        if ($el.attr('type') === 'checkbox' && !$el.is(':checked')) return;

                        payload[name] = $el.val();
                    });

                    // collect modules explicitly
                    payload.modules = $('input[name="modules[]"]:checked').map(function(){ return $(this).val(); }).get();

                    return payload;
                }

                // ---- Email uniqueness (on blur) ----
                const $email = $('#email');
                let emailUnique = true;
                let emailChecked = false;
                let emailMessage = '';
                const emailCache = {};

                async function checkEmailUnique(email) {
                    const key = String(email || '').trim().toLowerCase();
                    if (!key) {
                        emailChecked = false;
                        emailUnique = false;
                        emailMessage = 'Email is required.';
                        return { unique: false, message: emailMessage };
                    }

                    const tmp = document.createElement('input');
                    tmp.type = 'email';
                    tmp.value = key;
                    if (!tmp.checkValidity()) {
                        emailChecked = true;
                        emailUnique = false;
                        emailMessage = 'Please enter a valid email address.';
                        return { unique: false, message: emailMessage };
                    }

                    if (emailCache[key] !== undefined) {
                        const { unique, message } = emailCache[key];
                        emailChecked = true;
                        emailUnique = unique;
                        emailMessage = message || '';
                        return emailCache[key];
                    }

                    try {
                        const res = await $.ajax({
                            url: `{{ route('admin.customers.check-email') }}`,
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                            contentType: 'application/json',
                            data: JSON.stringify({ email: key })
                        });
                        emailCache[key] = { unique: !!res.unique, message: res.message || '' };
                        emailChecked = true;
                        emailUnique = !!res.unique;
                        emailMessage = res.message || '';
                        return emailCache[key];
                    } catch (e) {
                        emailChecked = true;
                        emailUnique = true;
                        emailMessage = '';
                        return { unique: true, message: '' };
                    }
                }

                if ($email.length) {
                    $email.on('blur', async function() {
                        const val = $(this).val();
                        const { unique, message } = await checkEmailUnique(val);
                        if (!unique) setError($email, message || 'This email is already registered.');
                        else clearError($email);
                    });

                    let emailDebounce;
                    $email.on('input', function() {
                        clearError($email);
                        emailChecked = false;
                        if (emailDebounce) clearTimeout(emailDebounce);
                        emailDebounce = setTimeout(async () => {
                            const { unique, message } = await checkEmailUnique($email.val());
                            if (!unique) setError($email, message || 'This email is already registered.');
                        }, 450);
                    });
                }

                // --- Role <> Modules behavior: both roles see modules; admin auto-checks all and locks them ---
                const $roleRadios = $('input[name="role"].form-input');
                const $modulesWrapper = $('#modules-wrapper');
                const $modulesList = $('#modules-list');

                // derive list of modules from DOM
                const DOM_MODULES = $modulesList.find('input[name="modules[]"]').map(function(){ return $(this).val(); }).get();

                function updateRoleUI() {
                    const role = $('input[name="role"]:checked').val() || 'customer';

                    // keep modules visible for both roles
                    $modulesWrapper.show();

                    if (role === 'admin') {
                        // admin: check all modules and disable inputs
                        $modulesList.find('input[name="modules[]"]').each(function() {
                            $(this).prop('checked', true).prop('disabled', true);
                        });
                    } else {
                        // customer: enable checkboxes and leave current checked state (user may change)
                        $modulesList.find('input[name="modules[]"]').each(function() {
                            $(this).prop('disabled', false);
                        });
                    }
                }

                $roleRadios.on('change', updateRoleUI);
                updateRoleUI(); // init

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

                        // success
                        renderSuccess(res);
                        currentStep = $steps.length - 1;
                        showStep(currentStep);

                    } catch (xhr) {
                        if (xhr.status === 422) {
                            const json = xhr.responseJSON || {};
                            const errs = json.errors || {};

                            $.each(errs, function(name, msgs) {
                                let selectorName = name;
                                if (name.indexOf('modules') === 0) selectorName = 'modules[]';
                                const $el = $form.find(`[name="${selectorName}"]`);
                                if ($el.length) setError($el.first(), msgs && msgs[0] ? msgs[0] : 'Invalid value.');
                            });

                            const firstField = Object.keys(errs)[0];
                            if (firstField) {
                                const targetName = firstField.indexOf('modules') === 0 ? 'modules[]' : firstField;
                                const idx = $steps.toArray().findIndex(sec => $(sec).find(`[name="${targetName}"]`).length);
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
                        const { unique, message } = await checkEmailUnique(val);
                        if (!unique) {
                            setError($emailEl, message || 'This email is already registered.');
                            return;
                        }
                    }

                    currentStep++;
                    showStep(currentStep);
                });

                // live clear on typing for all inputs
                $('.form-input').on('input change', function() {
                    const $el = $(this);
                    if (String($el.val() || '').trim()) clearError($el);
                });

                // init
                showStep(currentStep);

            });
        })(jQuery);
    </script>
@endpush


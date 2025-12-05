@extends('layouts.admin')

@section('title', isset($customer) ? 'Edit Customer' : 'Add Customer')

@push('css')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <x-header :export-url="null" :create-url="route('admin.customers.create')" create-label="New Customer" />

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

                <!-- Form: works both for create and edit -->
                <form id="customerForm" novalidate data-mode="{{ isset($customer) ? 'edit' : 'create' }}"
                    data-update-url="{{ isset($customer) ? route('admin.customers.update', $customer->id) : '' }}">

                    <!-- Hidden: record id for edit mode -->
                    @if (isset($customer))
                        <input type="hidden" name="id" id="customer_id" value="{{ $customer->id }}">
                    @endif

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
                                        placeholder="Enter your first name" required
                                        value="{{ old('first_name', $customer->first_name ?? '') }}">
                                    <small class="error-text" data-for="first_name"></small>
                                </div>
                                <div class="form-field">
                                    <label class="form-label required" for="last_name">Last Name</label>
                                    <input type="text" class="form-input" id="last_name" name="last_name"
                                        placeholder="Enter your last name" required
                                        value="{{ old('last_name', $customer->last_name ?? '') }}">
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
                                        placeholder="your.email@example.com" required
                                        value="{{ old('email', $customer->email ?? '') }}">
                                    <small class="error-text" data-for="email"></small>
                                </div>
                                <div class="form-field">
                                    <label class="form-label required" for="phone">Phone Number</label>
                                    <input type="tel" class="form-input" id="phone" name="phone"
                                        placeholder="(555) 123-4567" required
                                        value="{{ old('phone', $customer->phone ?? '') }}">
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
                                        placeholder="123 Main Street" required
                                        value="{{ old('address', $customer->address ?? '') }}">
                                    <small class="error-text" data-for="address"></small>
                                </div>
                                <div class="form-field">
                                    <label class="form-label required" for="city">City</label>
                                    <input type="text" class="form-input" id="city" name="city"
                                        placeholder="Your City" required value="{{ old('city', $customer->city ?? '') }}">
                                    <small class="error-text" data-for="city"></small>
                                </div>
                                <div class="form-field">
                                    <label class="form-label required" for="state">State</label>
                                    <input type="text" class="form-input" id="state" name="state"
                                        placeholder="Your State" required
                                        value="{{ old('state', $customer->state ?? '') }}">
                                    <small class="error-text" data-for="state"></small>
                                </div>
                                <div class="form-field">
                                    <label class="form-label required" for="zipCode">ZIP Code</label>
                                    <input type="text" class="form-input" id="zipCode" name="zipCode"
                                        placeholder="12345" required
                                        value="{{ old('zipCode', $customer->zipCode ?? '') }}">
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
                                        placeholder="Any special requirements, preferences, or questions...">{{ old('additionalNotes', $customer->additionalNotes ?? '') }}</textarea>
                                    <small class="error-text" data-for="additionalNotes"></small>
                                </div>
                                <div class="form-field">
                                    <label class="form-label" for="referralSource">How did you hear about us?</label>
                                    <select class="form-input custom-select" id="referralSource" name="referralSource">
                                        <option></option>
                                        <option value="Google Search"
                                            {{ old('referralSource', $customer->referralSource ?? '') == 'Google Search' ? 'selected' : '' }}>
                                            Google Search</option>
                                        <option value="Social Media"
                                            {{ old('referralSource', $customer->referralSource ?? '') == 'Social Media' ? 'selected' : '' }}>
                                            Social Media</option>
                                        <option value="Referral"
                                            {{ old('referralSource', $customer->referralSource ?? '') == 'Referral' ? 'selected' : '' }}>
                                            Referral</option>
                                        <option value="Advertisement"
                                            {{ old('referralSource', $customer->referralSource ?? '') == 'Advertisement' ? 'selected' : '' }}>
                                            Advertisement</option>
                                        <option value="Other"
                                            {{ old('referralSource', $customer->referralSource ?? '') == 'Other' ? 'selected' : '' }}>
                                            Other</option>
                                    </select>
                                    <small class="error-text" data-for="referralSource"></small>
                                </div>
                            </div>

                            <div class="field-row">
                                <div class="form-field">
                                    <label class="form-label" for="customer_status">Status</label>
                                    <select name="customer_status" id="customer_status" class="form-input custom-select">
                                        <option></option>
                                        @foreach (get_customer_status_list() as $status)
                                            <option value="{{ $status['id'] }}"
                                                {{ (string) old('customer_status', $customer->status ?? '') === (string) $status['id'] ? 'selected' : '' }}>
                                                {{ $status['text'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            @php
                                // --- Determine selected role ---
                                $selectedRole = old('role', $customer->role ?? 'customer');

                                // --- Decode modules safely (old() takes priority) ---
                                $oldModules = old('modules');

                                if (is_array($oldModules)) {
                                    $selectedModules = $oldModules;
                                } else {
                                    $rawModules = $customer->modules ?? null;

                                    if (is_string($rawModules)) {
                                        // Try to decode JSON string — fallback: treat as single string module or empty array
                                        $decoded = json_decode($rawModules, true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                            $selectedModules = $decoded;
                                        } else {
                                            $selectedModules = $rawModules ? [$rawModules] : [];
                                        }
                                    } elseif (is_array($rawModules)) {
                                        $selectedModules = $rawModules;
                                    } else {
                                        $selectedModules = [];
                                    }
                                }
                                // dd($selectedModules);
                                // --- Determine if modules should be disabled (for admin) ---
                                $modulesDisabled = ($selectedRole === 'admin');

                                // --- All available modules ---
                                $allModules = ['dashboard','projects','files','quotes','users','settings'];
                            @endphp

                            <!-- ✅ Role selector -->
                            <div class="form-field" style="margin-bottom:12px;">
                                <label class="form-label required">Role</label>
                                <div style="display:flex;gap:12px;align-items:center;margin-top:6px;">
                                    <label>
                                        <input type="radio" name="role" value="customer" class="form-input"
                                            {{ $selectedRole === 'customer' ? 'checked' : '' }}>
                                        Customer
                                    </label>
                                    <label>
                                        <input type="radio" name="role" value="admin" class="form-input"
                                            {{ $selectedRole === 'admin' ? 'checked' : '' }}>
                                        Admin
                                    </label>
                                </div>
                                <small class="error-text" data-for="role"></small>
                            </div>

                            <!-- ✅ Modules -->
                            <div class="form-field" id="modules-wrapper" style="margin-bottom:14px;">
                                <label class="form-label">Modules</label>
                                <div id="modules-list" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:6px;">
                                    @foreach($allModules as $mod)
                                        @php
                                            // checked if admin OR present in $selectedModules
                                            $isChecked = ($selectedRole === 'admin') || in_array($mod, $selectedModules, true);
                                            // disable checkbox for admin (JS will mirror this behavior)
                                            $isDisabled = ($selectedRole === 'admin');
                                        @endphp
                                        <label>
                                            <input type="checkbox"
                                                name="modules[]"
                                                value="{{ $mod }}"
                                                class="form-input"
                                                {{ $isChecked ? 'checked' : '' }}
                                                {{ $isDisabled ? 'disabled' : '' }}>
                                            {{ ucfirst($mod) }}
                                        </label>
                                    @endforeach
                                </div>
                                <small class="error-text" data-for="modules[]"></small>
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
                                style="background-color:rgb(22,163,74);color:#fff;border-color:rgb(22,163,74);"><i class="fas fa-check"></i></div>
                            <h1 class="form-title" id="success-title" style="color:rgb(22,163,74);margin:20px 0;">
                                Customer updated Successfully!</h1>
                            <p class="form-subtitle" id="success-msg" style="margin-bottom:30px;">Thank you for choosing
                                The Stone Cobblers. We'll be in touch soon!</p>
                            <a href="{{ route('admin.customers.index') }}" class="nav-btn next"
                                style="text-decoration:none;display:inline-flex;"><i class="fas fa-home"></i> Back to Customers</a>
                        </div>
                    </div>

                    <!-- Navigation -->
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
            const totalSteps = $steps.length - 1;

            const mode = $form.data('mode') || 'create';
            const updateUrl = $form.data('update-url') || '';

            // ===== Helpers =====
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

                const $emailEl = $steps.eq(currentStep).find('#email');
                if ($emailEl.length && emailChecked && emailUnique === false) {
                    ok = false;
                    setError($emailEl, emailMessage || 'This email is already registered.');
                }
                return ok;
            }

            // ===== Review Step Fill =====
            function fillReview() {
                const $review = $('#review-content');
                if (!$review.length) return;

                const $fields = $('.form-input').filter(function() {
                    return !!$(this).attr('name');
                });

                const html = $fields.map(function() {
                    const $f = $(this);
                    const name = $f.attr('name');
                    if (name === 'modules[]' || name === 'role') return null;

                    if ($f.attr('type') === 'radio' && !$f.is(':checked')) return null;
                    if ($f.attr('type') === 'checkbox' && !$f.is(':checked')) return null;

                    const label = labelOf($f) || name;
                    const val = $f.val();
                    return `<div style="margin-bottom:6px"><strong>${escapeHtml(label)}:</strong> ${val ? escapeHtml(val) : '<em>-</em>'}</div>`;
                }).get().join('');

                $review.html(html || '<div>No data entered yet.</div>');

                // Role & Modules summary
                const role = $('input[name="role"]:checked').val() || 'customer';
                $review.append(`<div><strong>Role:</strong> ${escapeHtml(role)}</div>`);

                const modules = $('input[name="modules[]"]:checked').map(function() {
                    return $(this).val();
                }).get();
                $review.append(
                    `<div><strong>Modules:</strong> ${modules.length ? escapeHtml(modules.join(', ')) : '<em>None</em>'}</div>`
                    );
            }

            // ===== Collect Payload =====
            function collectPayload() {
                const payload = {};
                $('.form-input').each(function() {
                    const $el = $(this);
                    const name = $el.attr('name');
                    if (!name) return;

                    if ($el.attr('type') === 'radio') {
                        if ($el.is(':checked')) payload[name] = $el.val();
                    } else if ($el.attr('type') === 'checkbox') {
                        payload['modules'] = $('input[name="modules[]"]:checked').map(function() {
                            return $(this).val();
                        }).get();
                    } else {
                        payload[name] = $el.val();
                    }
                });

                const id = $('#customer_id').val();
                if (id) payload.id = id;

                return payload;
            }

            // ===== Email Uniqueness =====
            const $email = $('#email');
            let emailUnique = true;
            let emailChecked = false;
            let emailMessage = '';
            const emailCache = {};

            async function checkEmailUnique(email) {
                const id = $('#customer_id').val() || '';
                const key = `${String(email || '').trim().toLowerCase()}|${id}`;
                if (!String(email || '').trim()) {
                    emailChecked = false;
                    emailUnique = false;
                    emailMessage = 'Email is required.';
                    return {
                        unique: false,
                        message: emailMessage
                    };
                }

                const tmp = document.createElement('input');
                tmp.type = 'email';
                tmp.value = email;
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
                            email: String(email || '').trim(),
                            id: id
                        })
                    });
                    emailCache[key] = {
                        unique: !!res.unique,
                        message: res.message || ''
                    };
                    emailChecked = true;
                    emailUnique = !!res.unique;
                    emailMessage = res.message || '';
                    return emailCache[key];
                } catch (e) {
                    emailChecked = true;
                    emailUnique = true;
                    emailMessage = '';
                    return {
                        unique: true,
                        message: ''
                    };
                }
            }

            if ($email.length) {
                $email.on('blur', async function() {
                    const val = $(this).val();
                    const {
                        unique,
                        message
                    } = await checkEmailUnique(val);
                    if (!unique) setError($email, message || 'This email is already registered.');
                    else clearError($email);
                });

                let emailDebounce;
                $email.on('input', function() {
                    clearError($email);
                    emailChecked = false;
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

            // ===== Role & Modules Logic =====
            const $roleRadios = $('input[name="role"]');
            const $modules = $('input[name="modules[]"]');

            function updateModulesForRole() {
                const role = $('input[name="role"]:checked').val();
                if (role === 'admin') {
                    $modules.prop('checked', true).prop('disabled', true);
                } else {
                    $modules.prop('disabled', false);
                }
            }

            $roleRadios.on('change', updateModulesForRole);
            updateModulesForRole();

            // ===== AJAX Submit =====
            async function submitFormAjax() {
                const payload = collectPayload();
                $nextBtn.prop('disabled', true);
                try {
                    let url = `{{ route('admin.customers.store') }}`,
                        method = 'POST';
                    if (mode === 'edit' && updateUrl) {
                        url = updateUrl;
                        method = 'PUT';
                    }

                    const res = await $.ajax({
                        url,
                        method,
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        contentType: 'application/json',
                        data: JSON.stringify(payload)
                    });

                    currentStep = $steps.length - 1;
                    showStep(currentStep);

                } catch (xhr) {
                    if (xhr.status === 422) {
                        const json = xhr.responseJSON || {};
                        const errs = json.errors || {};
                        $.each(errs, function(name, msgs) {
                            const $el = $form.find(`[name="${name}"]`);
                            if ($el.length) setError($el, msgs && msgs[0] ? msgs[0] : 'Invalid value.');
                        });
                    } else {
                        alert('Something went wrong. Please try again.');
                    }
                } finally {
                    $nextBtn.prop('disabled', false);
                }
            }

            // ===== Navigation Buttons =====
            $prevBtn.on('click', function(e) {
                e.preventDefault();
                if (currentStep > 0) {
                    currentStep--;
                    showStep(currentStep);
                }
            });

            $nextBtn.on('click', async function(e) {
                e.preventDefault();

                if (currentStep === totalSteps - 1) {
                    await submitFormAjax();
                    return;
                }

                if (!validateCurrentStep()) return;

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

            // ===== Live Error Clear =====
            $('.form-input').on('input change', function() {
                const $el = $(this);
                if (String($el.val() || '').trim()) clearError($el);
            });

            // ===== Init =====
            showStep(currentStep);

        });
    </script>
@endpush

@extends('layouts.admin')

@section('title', isset($project) ? 'Edit Project' : 'Add Project')

@push('css')
    {{-- You can move CSS to a separate file. Keep minimal inline for demo. --}}
    <style>
        /* minimal styles (you can keep your full CSS here) */

    </style>
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="search-bar">
                <i>🔍</i>
                <input type="text" placeholder="Search projects, customers, status...">
            </div>

            <div class="header-actions">
                <a href="#export" class="header-btn secondary" role="button"><i>📤</i> Export</a>
                <a href="{{ route('admin.projects.create') }}" class="header-btn primary" role="button"><i>➕</i> New Project</a>
                <div class="user-avatar">BM</div>
            </div>
        </div>

        <!-- Content -->
        <div class="content bg-content">
            <div class="form-container">
                <form id="projectForm"
                      method="POST"
                      action="{{ isset($project) ? route('admin.projects.update', $project) : route('admin.projects.store') }}">
                    @csrf
                    @if(isset($project))
                        @method('PUT')
                    @endif

                    <!-- Progress -->
                    <div class="progress-section">
                        <div class="step-indicator"></div>
                        <div class="progress-bar"><div class="progress-fill"></div></div>
                        <div class="progress-percentage"></div>
                    </div>

                    <!-- Step 1 -->
                    <div class="form-container-view active">
                        <div class="form-header">
                            <div class="form-icon icon-project"></div>
                            <h1 class="form-title">Let's start with your project details</h1>
                            <p class="form-subtitle">We'll use this information to set up your project</p>
                        </div>

                        <div class="form-fields">
                            <div class="field-row">
                                <div class="form-field">
                                    <label class="form-label required">Project Name</label>
                                    <input type="text" name="name" class="form-input" placeholder="Enter project name" required
                                           value="{{ old('name', $project->name ?? '') }}">
                                    <div class="error-msg" data-for="name"></div>
                                </div>
                                <div class="form-field">
                                    <label class="form-label">Project Subtitle</label>
                                    <input type="text" name="subtitle" class="form-input" placeholder="Enter project subtitle"
                                           value="{{ old('subtitle', $project->subtitle ?? '') }}">
                                    <div class="error-msg" data-for="subtitle"></div>
                                </div>
                            </div>
                            <div class="field-row single">
                                <div class="form-field">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-input" placeholder="Describe your project in detail..." rows="4">{{ old('description', $project->description ?? '') }}</textarea>
                                    <div class="error-msg" data-for="description"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="form-container-view">
                        <div class="form-header">
                            <div class="form-icon icon-customer"></div>
                            <h1 class="form-title">Which customer is this for?</h1>
                            <p class="form-subtitle">Select the customer this project belongs to</p>
                        </div>

                        <div class="form-fields">
                            <div class="field-row">
                                <div class="form-field">
                                    <label class="form-label required">Customer</label>
                                    <select class="form-input" name="customer_id" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $c)
                                            <option value="{{ $c->id }}" {{ (int)old('customer_id', $project->user_id ?? 0) === $c->id ? 'selected' : '' }}>
                                                {{ $c->first_name }} {{ $c->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="error-msg" data-for="customer_id"></div>
                                </div>

                                <div class="form-field">
                                    <label class="form-label">Customer Notes</label>
                                    <input type="text" name="customer_notes" class="form-input"
                                           placeholder="Any specific customer requirements or notes..."
                                           value="{{ old('customer_notes', $project->customer_notes ?? '') }}">
                                    <div class="error-msg" data-for="customer_notes"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="form-container-view">
                        <div class="form-header">
                            <div class="form-icon icon-project"></div>
                            <h1 class="form-title">What's the budget and timeline?</h1>
                            <p class="form-subtitle">Help us understand your project scope and timeline</p>
                        </div>

                        <div class="form-fields">
                            <div class="field-row">
                                <div class="form-field">
                                    <label class="form-label required">Budget Range</label>
                                    <select class="form-input" name="budget" required>
                                        <option value="">Select Budget Range</option>
                                        @foreach(get_budget_ranges() as $opt)
                                            <option value="{{ $opt['id'] }}" {{ old('budget', $project->budget ?? '') == $opt['id'] ? 'selected' : '' }}>
                                                {{ $opt['text'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="error-msg" data-for="budget"></div>
                                </div>

                                <div class="form-field">
                                    <label class="form-label required">Timeline</label>
                                    <select class="form-input" name="timeline" required>
                                        <option value="">Select Timeline</option>
                                        @foreach(get_timeline_options() as $opt)
                                            <option value="{{ $opt['id'] }}" {{ old('timeline', $project->timeline ?? '') == $opt['id'] ? 'selected' : '' }}>
                                                {{ $opt['text'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="error-msg" data-for="timeline"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="form-container-view">
                        <div class="form-header">
                            <div class="form-icon icon-timeline"></div>
                            <h1 class="form-title">Project status and progress</h1>
                            <p class="form-subtitle">Set the initial status and progress for your project</p>
                        </div>

                        <div class="form-fields">
                            <div class="field-row">
                                <div class="form-field">
                                    <label class="form-label required">Project Status</label>
                                    <select class="form-input" name="status" required>
                                        <option value="">Select Project Status</option>
                                        @foreach(get_project_status_list() as $opt)
                                            <option value="{{ $opt['id'] }}" {{ old('status', $project->status ?? '') == $opt['id'] ? 'selected' : '' }}>
                                                {{ $opt['text'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="error-msg" data-for="status"></div>
                                </div>

                                <div class="form-field">
                                    <label class="form-label required">Progress</label>
                                    <select class="form-input" name="progress" required>
                                        <option value="">Select Progress</option>
                                        @foreach(get_progress_list() as $opt)
                                            <option value="{{ $opt['id'] }}" {{ old('progress', $project->progress ?? '') == $opt['id'] ? 'selected' : '' }}>
                                                {{ $opt['text'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="error-msg" data-for="progress"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5 -->
                    <div class="form-container-view">
                        <div class="form-header">
                            <div class="form-icon icon-team"></div>
                            <h1 class="form-title">Who's on the team?</h1>
                            <p class="form-subtitle">Assign team members to this project</p>
                        </div>

                        <div class="form-fields">
                            <div class="form-field">
                                <h3 style="margin-bottom: 20px; color: #333;">Please review your project information:</h3>
                                <div id="review-content" style="background: #f8f9fa; padding:20px; border-radius:8px; margin-bottom:20px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Success -->
                    <div class="form-container-view">
                        <div style="text-align:center">
                            <div class="form-icon icon-project" style="background-color:rgb(22,163,74); color:white;">✓</div>
                            <h1 class="form-title" style="color:rgb(22,163,74); margin:20px 0;">
                                <span id="success-title">{{ isset($project) ? 'Project Updated Successfully!' : 'Project Created Successfully!' }}</span>
                            </h1>
                            <p class="form-subtitle" id="success-subtitle">Your project has been set up and is ready to go!</p>
                            <a href="{{ route('admin.projects.index') }}" class="nav-btn next" style="text-decoration:none; display:inline-flex;">
                                <i>📋</i> Back to Projects
                            </a>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="form-navigation">
                        <button type="button" class="nav-btn previous">← Previous</button>
                        <button type="button" class="nav-btn next">Next →</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script>
    $(function () {
        const $steps = $('.form-container-view');
        const $prevBtn = $('.form-navigation .nav-btn.previous');
        const $nextBtn = $('.form-navigation .nav-btn.next');
        const $progressFill = $('.progress-fill');
        const $stepIndicator = $('.step-indicator');
        const $progressPercentage = $('.progress-percentage');
        const $navWrapper = $('.form-navigation');
        const $form = $('#projectForm');
        const totalSteps = $steps.length - 1; // exclude success view
        let currentStep = 0;
        const route = $form.attr('action');
        const isEdit = $form.find('input[name="_method"]').length > 0;

        // required fields per step
        const stepRequired = {
            0: ['name'],
            1: ['customer_id'],
            2: ['budget','timeline'],
            3: ['status','progress'],
            4: []
        };

        function showStep(n) {
            $steps.removeClass('active').eq(n).addClass('active');
            updateProgress();
            updateButtons();
            if (n === totalSteps - 1) populateReview();

            if (n === $steps.length - 1) {
                $navWrapper.hide();
            } else {
                $navWrapper.show();
            }
        }

        function updateProgress() {
            if (currentStep < totalSteps) {
                const progress = ((currentStep + 1) / totalSteps) * 100;
                $progressFill.css('width', progress + '%');
                $stepIndicator.text(`Step ${currentStep + 1} of ${totalSteps}`);
                $progressPercentage.text(Math.round(progress) + '% Complete');
            }
        }

        function updateButtons() {
            $prevBtn.prop('disabled', currentStep === 0);
            if (currentStep === totalSteps - 1) {
                $nextBtn.text(isEdit ? 'Save Changes ✓' : 'Create Project ✓');
            } else {
                $nextBtn.html('Next <i>→</i>');
            }
        }

        function validateCurrentStep() {
            let ok = true;
            const required = stepRequired[currentStep] || [];
            $('.error-msg').text('').hide();
            required.forEach(name => {
                const $el = $('[name="' + name + '"]');
                const val = $.trim($el.val() || '');
                if (!val) {
                    ok = false;
                    showFieldError(name, 'This field is required.');
                }
            });
            return ok;
        }

        function showFieldError(name, message) {
            const $err = $('.error-msg[data-for="' + name + '"]');
            if ($err.length) {
                $err.text(message).show();
            } else {
                const $el = $('[name="' + name + '"]');
                $el.after('<div class="error-msg" data-for="' + name + '">' + message + '</div>');
            }
        }

        function populateReview() {
            const $review = $('#review-content');
            const fields = $form.find('.form-input, textarea, select').filter(function () { return $(this).attr('name'); });
            if (!fields.length) { $review.html('<div>No data entered yet.</div>'); return; }
            let html = '';
            fields.each(function () {
                const label = $(this).closest('.form-field').find('label').text().trim() || $(this).attr('name');
                const val = $(this).val();
                html += `<div style="margin-bottom:6px"><strong>${escapeHtml(label)}:</strong> ${val ? escapeHtml(val) : '<em>-</em>'}</div>`;
            });
            $review.html(html);
        }

        function escapeHtml(text) {
            return String(text).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
        }

        function submitForm() {
            const formData = $form.serialize();

            // clear server errors
            $('.error-msg').text('').hide();

            $.ajax({
                url: route,
                method: 'POST', // Laravel accepts POST + _method for PUT
                data: formData,
                headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
                beforeSend() { $nextBtn.prop('disabled', true).text(isEdit ? 'Saving...' : 'Saving...'); }
            })
            .done(function (res) {
                if (res && res.success) {
                    // set messages for edit/create
                    $('#success-title').text(isEdit ? 'Project Updated Successfully!' : 'Project Created Successfully!');
                    currentStep = $steps.length - 1;
                    showStep(currentStep);
                } else {
                    alert(res.message || 'Saved (unexpected response).');
                }
            })
            .fail(function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors || {};
                    $.each(errors, function (field, msgs) { showFieldError(field, msgs.join(' ')); });
                    const msg = xhr.responseJSON?.message; if (msg) alert(msg);
                } else {
                    alert('An error occurred while saving. Please try again.');
                }
            })
            .always(function () {
                $nextBtn.prop('disabled', false);
                updateButtons();
            });
        }

        // navigation handlers
        $prevBtn.on('click', function (e) { e.preventDefault(); if (currentStep > 0) { currentStep--; showStep(currentStep); }});
        $nextBtn.on('click', function (e) {
            e.preventDefault();
            if (currentStep === totalSteps - 1) {
                if (!validateCurrentStep()) return;
                submitForm();
                return;
            }
            if (!validateCurrentStep()) return;
            if (currentStep < $steps.length - 1) { currentStep++; showStep(currentStep); }
        });

        // auto-open first incomplete step when editing
        (function autoOpen() {
            @if(isset($project))
                for (let i = 0; i < Object.keys(stepRequired).length; i++) {
                    const req = stepRequired[i] || [];
                    let missing = false;
                    req.forEach(name => { if ($('[name="' + name + '"]').length && $.trim($('[name="' + name + '"]').val() || '') === '') missing = true; });
                    if (missing) { currentStep = i; break; }
                }
            @endif
        })();

        showStep(currentStep);
    });
    </script>
@endpush

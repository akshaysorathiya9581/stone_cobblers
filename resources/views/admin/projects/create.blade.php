@extends('layouts.admin')

@section('title', 'Add Project')

@push('css')
    
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <x-header
            :export-url="null"
            :create-url="route('admin.projects.create')"
            export-label="Export Project"
            create-label="New Project"
        />

        <!-- Content -->
        <div class="content bg-content">
            <div class="form-container">
                <form id="projectForm" method="POST" action="{{ route('admin.projects.store') }}">
                    @csrf

                    <!-- 🔹 Global Progress Section -->
                    <div class="progress-section">
                        <div class="step-indicator"></div>
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                        <div class="progress-percentage"></div>
                    </div>

                    <!-- Step 1 (MOVED) - Customer selection (2 inputs) -->
                    <div class="form-container-view active">
                        <div class="form-header">
                            <div class="form-icon icon-customer"></div>
                            <h1 class="form-title">Which customer is this for?</h1>
                            <p class="form-subtitle">Select the customer this project belongs to</p>
                        </div>

                        <div class="form-fields">
                            <div class="field-row">
                                <div class="form-field ">
                                    <label class="form-label required">Customer</label>
                                    <select class="form-input custom-select" name="customer_id" data-placeholder="Select Customer" required>
                                        <option></option>
                                        @foreach($customers as $c)
                                            <option value="{{ $c->id }}" @if(old('customer_id') == $c->id) selected @endif>{{ $c->first_name }} {{ $c->last_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="error-msg" data-for="customer_id"></div>
                                </div>

                                <div class="form-field ">
                                    <label class="form-label ">Customer Notes</label>
                                    <input type="text" name="customer_notes" class="form-input"
                                        placeholder="Any specific customer requirements or notes..." value="{{ old('customer_notes') }}">
                                    <div class="error-msg" data-for="customer_notes"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 (MOVED) - Project basic info (2 inputs) -->
                    <div class="form-container-view">
                        <div class="form-header">
                            <div class="form-icon icon-project"></div>
                            <h1 class="form-title">Let's start with your project details</h1>
                            <p class="form-subtitle">We'll use this information to set up your project</p>
                        </div>

                        <div class="form-fields">
                            <div class="field-row">
                                <div class="form-field">
                                    <label class="form-label required">Project Name</label>
                                    <input type="text" name="name" class="form-input" placeholder="Enter project name" required value="{{ old('name') }}">
                                    <div class="error-msg" data-for="name"></div>
                                </div>
                                <div class="form-field">
                                    <label class="form-label">Project Subtitle</label>
                                    <input type="text" name="subtitle" class="form-input" placeholder="Enter project subtitle" value="{{ old('subtitle') }}">
                                    <div class="error-msg" data-for="subtitle"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 - Description (kept separate for two-and-two style) -->
                    <div class="form-container-view">
                        <div class="form-header">
                            <div class="form-icon icon-project"></div>
                            <h1 class="form-title">Project Description</h1>
                            <p class="form-subtitle">Describe your project in detail</p>
                        </div>

                        <div class="form-fields">
                            <div class="field-row single">
                                <div class="form-field">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-input" placeholder="Describe your project in detail..." rows="4">{{ old('description') }}</textarea>
                                    <div class="error-msg" data-for="description"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="form-container-view">
                        <div class="form-header">
                            <div class="form-icon icon-budget"></div>
                            <h1 class="form-title">What's the budget and timeline?</h1>
                            <p class="form-subtitle">Help us understand your project scope and timeline</p>
                        </div>

                        <div class="form-fields">
                            <div class="field-row">
                                <div class="form-field ">
                                    <label class="form-label required">Budget Range</label>
                                    <select class="form-input custom-select" name="budget" data-placeholder="Select Budget Range" required>
                                        <option></option>
                                        @foreach(get_budget_ranges() as $opt)
                                            <option value="{{ $opt['id'] }}" @if(old('budget', $model->budget ?? '') == $opt['id']) selected @endif>
                                                {{ $opt['text'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="error-msg" data-for="budget"></div>
                                </div>

                                <div class="form-field ">
                                    <label class="form-label required">Timeline</label>
                                    <select class="form-input custom-select" name="timeline" data-placeholder="Select Timeline" required>
                                        <option></option>
                                        @foreach(get_timeline_options() as $opt)
                                            <option value="{{ $opt['id'] }}" @if(old('timeline', $model->timeline ?? '') == $opt['id']) selected @endif>
                                                {{ $opt['text'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="error-msg" data-for="timeline"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5 -->
                    <div class="form-container-view">
                        <div class="form-header">
                            <div class="form-icon icon-timeline"></div>
                            <h1 class="form-title">Project status and progress</h1>
                            <p class="form-subtitle">Set the initial status and progress for your project</p>
                        </div>

                        <div class="form-fields">
                            <div class="field-row">
                                <div class="form-field ">
                                    <label class="form-label required">Project Status</label>
                                    <select class="form-input custom-select" name="status" data-placeholder="Select Project Status" required>
                                        <option></option>
                                        @foreach(get_project_status_list() as $opt)
                                            <option value="{{ $opt['id'] }}" @if(old('status', $model->status ?? '') == $opt['id']) selected @endif>
                                                {{ $opt['text'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="error-msg" data-for="status"></div>
                                </div>

                                <div class="form-field ">
                                    <label class="form-label required">Progress</label>
                                    <select class="form-input custom-select" name="progress" data-placeholder="Select Progress" required>
                                        <option></option>
                                        @foreach(get_progress_list() as $opt)
                                            <option value="{{ $opt['id'] }}" @if(old('progress', $model->progress ?? '') == $opt['id']) selected @endif>
                                                {{ $opt['text'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="error-msg" data-for="progress"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 6 (Review + Team assignment simplified) -->
                    <div class="form-container-view">
                        <div class="form-header">
                            <div class="form-icon icon-team"></div>
                            <h1 class="form-title">Who's on the team?</h1>
                            <p class="form-subtitle">Assign team members to this project</p>
                        </div>

                        <div class="form-fields">
                            <div class="form-field">
                                <h3 style="margin-bottom: 20px; color: #333;">Please review your project information:</h3>
                                <div id="review-content"
                                    style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                                    <!-- Review content will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 7 (success) -->
                    <div class="form-container-view">
                        <div style="text-align: center;">
                            <div class="form-icon icon-project"
                                style="background-color: rgb(22, 163, 74); color: white; border-color: rgb(22, 163, 74);">
                                <i class="fas fa-check"></i>
                            </div>
                            <h1 class="form-title" style="color: rgb(22, 163, 74); margin: 20px 0;">Project Created
                                Successfully!</h1>
                            <p class="form-subtitle" id="success-subtitle" style="margin-bottom: 30px;">Your project has been set up and is ready to go!</p>
                            <a href="{{ route('admin.projects.index') }}" class="nav-btn next"
                                style="text-decoration: none; display: inline-flex;">
                                <i class="fas fa-folder-open"></i> Back to Projects
                            </a>
                        </div>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="nav-btn previous">← Previous</button>
                        <button type="button" class="nav-btn next">Next <i class="fas fa-arrow-right"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- jQuery (required) -->
    <!-- <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script> -->

    <script>
        $(function () {
            const $steps = $('.form-container-view');
            const $prevBtn = $('.form-navigation .nav-btn.previous');
            const $nextBtn = $('.form-navigation .nav-btn.next');
            const $progressSection = $('.progress-section');
            const $progressFill = $('.progress-fill');
            const $stepIndicator = $('.step-indicator');
            const $progressPercentage = $('.progress-percentage');
            const $navWrapper = $('.form-navigation');
            const $form = $('#projectForm');
            const totalSteps = $steps.length - 1; // exclude success step
            let currentStep = 0;
            const route = $form.attr('action');

            // map which fields are required per step (name attributes)
            // UPDATED: Step 0 => customer selection (customer_id)
            //          Step 1 => project basic (name)
            //          Step 2 => description (optional)
            //          Step 3 => budget/timeline
            //          Step 4 => status/progress
            //          Step 5 => review (no required)
            const stepRequired = {
                0: ['customer_id'], // Step 1 (customer)
                1: ['name'],       // Step 2 (project name)
                2: [],             // Step 3 (description) - optional
                3: ['budget', 'timeline'], // Step 4
                4: ['status', 'progress'], // Step 5
                5: [] // Step 6 (review)
            };

            function showStep(n) {
                $steps.removeClass('active').eq(n).addClass('active');
                updateProgress();
                updateButtons();

                if (n === totalSteps - 1) {
                    populateReview();
                }

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
                    $progressPercentage.text(Math.round(progress) + '% Complete');
                }
            }

            function updateButtons() {
                $prevBtn.prop('disabled', currentStep === 0);
                if (currentStep === totalSteps - 1) {
                    $nextBtn.html('Create Project <i class="fas fa-check"></i>');
                } else {
                    $nextBtn.html('Next <i class="fas fa-arrow-right"></i>');
                }
            }

            // simple client validation for required fields (per step)
            function validateCurrentStep() {
                let ok = true;
                const required = stepRequired[currentStep] || [];
                // clear previous errors
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
                    // fallback: append under field
                    const $el = $('[name="' + name + '"]');
                    $el.after('<div class="error-msg" data-for="' + name + '">' + message + '</div>');
                }
            }

            function populateReview() {
                const $review = $('#review-content');
                const fields = $form.find('.form-input, textarea, select').filter(function () { return $(this).attr('name'); });
                if (!fields.length) {
                    $review.html('<div>No data entered yet.</div>');
                    return;
                }
                let html = '';
                fields.each(function () {
                    const label = $(this).closest('.form-field').find('label').text().trim() || $(this).attr('name');
                    const val = $(this).val();
                    html += '<div style="margin-bottom:6px"><strong>' + escapeHtml(label) + ':</strong> ' + (val ? escapeHtml(val) : '<em>-</em>') + '</div>';
                });
                $review.html(html);
            }

            function escapeHtml(text) {
                return String(text)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            // AJAX submit
            function submitForm() {
                // gather data
                const formData = $form.serialize();

                // clear server errors
                $('.error-msg').text('').hide();

                $.ajax({
                    url: route,
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    beforeSend() {
                        $nextBtn.prop('disabled', true).text('Saving...');
                    }
                })
                .done(function (res) {
                    // expect JSON { success: true, project: {...}, message: '...' }
                    if (res && res.success) {
                        // show success step
                        currentStep = $steps.length - 1;
                        showStep(currentStep);
                    } else {
                        // if response not as expected, show a basic message
                        alert('Project saved, but response format unexpected.');
                    }
                })
                .fail(function (xhr) {
                    if (xhr.status === 422) {
                        // validation errors from server
                        const errors = xhr.responseJSON?.errors || {};
                        $.each(errors, function (field, msgs) {
                            showFieldError(field, msgs.join(' '));
                        });
                        // if server returns message
                        const msg = xhr.responseJSON?.message;
                        if (msg) {
                            // optionally show a top-level error
                            alert(msg);
                        }
                    } else {
                        alert('An error occurred while saving. Please try again.');
                    }
                })
                .always(function () {
                    $nextBtn.prop('disabled', false);
                    updateButtons();
                });
            }

            // nav handlers
            $prevBtn.on('click', function (e) {
                e.preventDefault();
                if (currentStep > 0) {
                    currentStep--;
                    showStep(currentStep);
                }
            });

            $nextBtn.on('click', function (e) {
                e.preventDefault();

                // if on last progress step -> submit
                if (currentStep === totalSteps - 1) {
                    // validate final step before sending
                    if (!validateCurrentStep()) return;
                    submitForm();
                    return;
                }

                // normal step forward validation
                if (!validateCurrentStep()) return;

                // move
                if (currentStep < $steps.length - 1) {
                    currentStep++;
                    showStep(currentStep);
                }
            });

            // show step 0
            showStep(currentStep);
        });
    </script>

@endpush

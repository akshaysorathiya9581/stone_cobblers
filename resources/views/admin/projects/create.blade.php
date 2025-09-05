@extends('layouts.admin')

@section('title', 'Add Project')

@push('css')
    <style>
        .form-container-view {
            display: none;
        }

        .form-container-view.active {
            display: block;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #f1f3f4;
            border-right: 1px solid #e0e0e0;
            padding: 20px 0;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background-color: rgb(22, 163, 74);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .nav-section {
            margin-bottom: 30px;
        }

        .nav-section h3 {
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 10px;
            padding: 0 20px;
            font-weight: 600;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .nav-item:hover {
            background-color: #e8f5e8;
        }

        .nav-item.active {
            background-color: #e8f5e8;
            color: rgb(22, 163, 74);
            font-weight: 500;
        }

        .nav-item i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        /* Icon styles */
        .icon-dashboard::before {
            content: "🏠";
        }

        .icon-customers::before {
            content: "👥";
        }

        .icon-projects::before {
            content: "📋";
        }

        .icon-quotes::before {
            content: "💰";
        }

        .icon-files::before {
            content: "📁";
        }

        .icon-reports::before {
            content: "📊";
        }

        .icon-settings::before {
            content: "⚙️";
        }

        .icon-starred::before {
            content: "⭐";
        }

        .icon-pinned::before {
            content: "📌";
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            background: white;
            border-bottom: 1px solid #e0e0e0;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background: #f1f3f4;
            border-radius: 8px;
            padding: 8px 16px;
            width: 400px;
        }

        .search-bar input {
            border: none;
            background: none;
            outline: none;
            width: 100%;
            font-size: 14px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .header-btn.primary {
            background-color: rgb(22, 163, 74);
            color: white;
        }

        .header-btn.secondary {
            background-color: #f1f3f4;
            color: #333;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #ff9500;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        /* Content Area */
        .content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            background-color: #f0f9f0;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .content-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .back-btn {
            padding: 10px 20px;
            border: 1px solid #e0e0e0;
            background: white;
            color: #333;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #f8f9fa;
        }

        /* Multi-step Form */
        .form-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 600px;
            position: relative;
        }

        /* Progress Bar */
        .progress-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .step-indicator {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .progress-bar {
            width: 200px;
            height: 4px;
            background-color: #e0e0e0;
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background-color: #000;
            transition: width 0.3s ease;
        }

        .progress-percentage {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        /* Form Header */
        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            border: 2px solid rgb(22, 163, 74);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: rgb(22, 163, 74);
        }

        .form-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 12px;
        }

        .form-subtitle {
            font-size: 16px;
            color: #666;
            line-height: 1.5;
        }

        /* Form Fields */
        .form-fields {
            margin-bottom: 40px;
        }

        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .field-row.single {
            grid-template-columns: 1fr;
        }

        .form-field {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-label.required::after {
            content: " *";
            color: #dc3545;
        }

        .form-input {
            padding: 12px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: rgb(22, 163, 74);
        }

        .form-input::placeholder {
            color: #999;
        }

        /* Navigation Buttons */
        .form-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-btn.previous {
            background-color: white;
            color: #666;
            border: 1px solid #e0e0e0;
        }

        .nav-btn.previous:hover {
            background-color: #f8f9fa;
        }

        .nav-btn.next {
            background-color: rgb(22, 163, 74);
            color: white;
        }

        .nav-btn.next:hover {
            background-color: rgb(21, 128, 61);
        }

        .nav-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Step-specific icons */
        .icon-project::before {
            content: "📋";
        }

        .icon-customer::before {
            content: "👤";
        }

        .icon-budget::before {
            content: "💰";
        }

        .icon-timeline::before {
            content: "📅";
        }

        .icon-team::before {
            content: "👥";
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .search-bar {
                width: 250px;
            }

            .form-container {
                padding: 30px 20px;
                margin: 20px;
            }

            .field-row {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .progress-bar {
                width: 150px;
            }
        }
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
                <button class="header-btn secondary">
                    <i>📤</i> Export
                </button>
                <a href="{{ route('admin.projects.create') }}" class="header-btn primary">
                    <i>➕</i> New Project
                </a>
                <div class="user-avatar">BM</div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
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
                                    <input type="text" name="name" class="form-input" placeholder="Enter project name" required>
                                    <div class="error-msg" data-for="name"></div>
                                </div>
                                <div class="form-field">
                                    <label class="form-label">Project Subtitle</label>
                                    <input type="text" name="subtitle" class="form-input" placeholder="Enter project subtitle">
                                    <div class="error-msg" data-for="subtitle"></div>
                                </div>
                            </div>
                            <div class="field-row single">
                                <div class="form-field">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-input" placeholder="Describe your project in detail..." rows="4"></textarea>
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
                                <div class="form-field ">
                                    <label class="form-label required">Customer</label>
                                    <select class="form-input" name="customer_id" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $c)
                                            <option value="{{ $c->id }}">{{ $c->first_name }} {{ $c->last_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="error-msg" data-for="customer_id"></div>
                                </div>

                                <div class="form-field ">
                                    <label class="form-label ">Customer Notes</label>
                                    <input type="text" name="customer_notes" class="form-input"
                                        placeholder="Any specific customer requirements or notes...">
                                    <div class="error-msg" data-for="customer_notes"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
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
                                    <select class="form-input" name="budget" required>
                                        <option value="">Select Budget Range</option>
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
                                    <select class="form-input" name="timeline" required>
                                        <option value="">Select Timeline</option>
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

                    <!-- Step 4 -->
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
                                    <select class="form-input" name="status" required>
                                        <option value="">Select Project Status</option>
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
                                    <select class="form-input" name="progress" required>
                                        <option value="">Select Progress</option>
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

                    <!-- Step 5 (Review + Team assignment simplified) -->
                    <div class="form-container-view">
                        <div class="form-header">
                            <div class="form-icon icon-team"></div>
                            <h1 class="form-title">Who's on the team?</h1>
                            <p class="form-subtitle">Assign team members to this project</p>
                        </div>

                        <div class="form-fields">
                            <div class="form-field">
                                <label class="form-label">Assign Team (comma separated)</label>
                                <input type="text" name="team" class="form-input" placeholder="Team A, Team B">
                                <div class="error-msg" data-for="team"></div>

                                <h3 style="margin-bottom: 20px; color: #333;">Please review your project information:</h3>
                                <div id="review-content"
                                    style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                                    <!-- Review content will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 6 (success) -->
                    <div class="form-container-view">
                        <div style="text-align: center;">
                            <div class="form-icon icon-project"
                                style="background-color: rgb(22, 163, 74); color: white; border-color: rgb(22, 163, 74);">
                                ✓
                            </div>
                            <h1 class="form-title" style="color: rgb(22, 163, 74); margin: 20px 0;">Project Created
                                Successfully!</h1>
                            <p class="form-subtitle" id="success-subtitle" style="margin-bottom: 30px;">Your project has been set up and is ready to go!</p>
                            <a href="{{ route('admin.projects.index') }}" class="nav-btn next"
                                style="text-decoration: none; display: inline-flex;">
                                <i>📋</i> Back to Projects
                            </a>
                        </div>
                    </div>

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
    <!-- jQuery (required) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

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
            const stepRequired = {
                0: ['name'], // Step 1
                1: ['customer_id'], // Step 2
                2: ['budget', 'timeline'], // Step 3
                3: ['status', 'progress'], // Step 4
                4: [] // Step 5 - optional but allow team
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
                    $nextBtn.text('Create Project ✓');
                } else {
                    $nextBtn.html('Next <i>→</i>');
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
                    $err.text(message).css('color', 'red').show();
                } else {
                    // fallback: append under field
                    const $el = $('[name="' + name + '"]');
                    $el.after('<div class="error-msg" data-for="' + name + '" style="color:red">' + message + '</div>');
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
                        // populate success message if returned
                        $('#success-subtitle').text(res.message || 'Your project has been set up and is ready to go!');
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


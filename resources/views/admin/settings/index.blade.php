@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="main-content">
    <!-- Header -->
    <div class="header">
        <button class="sidebar-toggle">
                <i class="fas fa-bars toggle-icon"></i>
            </button>
        <!-- <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search settings...">
        </div> -->

        <div class="header-actions">
            <a href="{{ route('admin.profile.edit') }}"
                class="user-avatar">{{ auth()->user() ? Str::upper(Str::substr(auth()->user()->name ?? 'U', 0, 2)) : 'U' }}</a>
        </div>
    </div>

    <div class="content settings-main">
        <div class="content-header">
            <div class="settings-header-content">
                <div>
                    <h1 class="content-title">Settings</h1>
                    <p class="settings-subtitle">Manage your application settings and preferences</p>
                </div>
            </div>
        </div>

        <div class="settings-container">
            <form id="settingsForm">
                @csrf

                <!-- Tabs -->
                <div class="tabs settings-tabs">
                    <button type="button" class="tab active" data-tab="general">
                        <i class="fas fa-sliders-h"></i> General
                    </button>
                    <button type="button" class="tab" data-tab="company">
                        <i class="fas fa-building"></i> Company
                    </button>
                    <button type="button" class="tab" data-tab="tax">
                        <i class="fas fa-percentage"></i> Tax & Pricing
                    </button>
                    <button type="button" class="tab" data-tab="quote">
                        <i class="fas fa-file-invoice-dollar"></i> Quote
                    </button>
                    <button type="button" class="tab" data-tab="email">
                        <i class="fas fa-envelope"></i> Email
                    </button>
                    <button type="button" class="tab" data-tab="pdf">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                </div>

                <!-- General Settings -->
                <div class="settings-tab-content active" data-content="general">
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <h3 class="settings-section-title">
                                <i class="fas fa-sliders-h"></i> General Settings
                            </h3>
                        </div>
                        <div class="settings-card-body">
                            <div class="settings-grid">
                        @foreach($settings['general'] ?? [] as $setting)
                        <div class="settings-field">
                            <label for="setting_{{ $setting->key }}">
                                {{ ucwords(str_replace('_', ' ', str_replace('app_', '', $setting->key))) }}
                            </label>
                            @if($setting->type === 'boolean')
                            <div class="checkbox-wrapper">
                                <input type="checkbox" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" 
                                    value="1" {{ $setting->value ? 'checked' : '' }}>
                                <span>Enable</span>
                            </div>
                            @else
                            <input type="text" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" 
                                value="{{ $setting->value }}" class="form-input">
                            @endif
                            @if($setting->description)
                            <div class="field-description">{{ $setting->description }}</div>
                            @endif
                        </div>
                        @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Company Settings -->
                <div class="settings-tab-content" data-content="company">
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <h3 class="settings-section-title">
                                <i class="fas fa-building"></i> Company Information
                            </h3>
                        </div>
                        <div class="settings-card-body">
                            <div class="settings-grid">
                        @foreach($settings['company'] ?? [] as $setting)
                        <div class="settings-field">
                            <label for="setting_{{ $setting->key }}">
                                {{ ucwords(str_replace('_', ' ', str_replace('company_', '', $setting->key))) }}
                            </label>
                            <input type="text" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" 
                                value="{{ $setting->value }}" class="form-input">
                            @if($setting->description)
                            <div class="field-description">{{ $setting->description }}</div>
                            @endif
                        </div>
                        @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tax & Pricing Settings -->
                <div class="settings-tab-content" data-content="tax">
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <h3 class="settings-section-title">
                                <i class="fas fa-percentage"></i> Tax & Pricing Settings
                            </h3>
                        </div>
                        <div class="settings-card-body">
                            <div class="settings-grid">
                        @foreach($settings['tax'] ?? [] as $setting)
                        <div class="settings-field">
                            <label for="setting_{{ $setting->key }}">
                                {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                            </label>
                            @if($setting->type === 'decimal')
                            <input type="number" step="0.0001" name="settings[{{ $setting->key }}]" 
                                id="setting_{{ $setting->key }}" value="{{ $setting->value }}" class="form-input">
                            @else
                            <input type="text" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" 
                                value="{{ $setting->value }}" class="form-input">
                            @endif
                            @if($setting->description)
                            <div class="field-description">{{ $setting->description }}</div>
                            @endif
                        </div>
                        @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quote Settings -->
                <div class="settings-tab-content" data-content="quote">
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <h3 class="settings-section-title">
                                <i class="fas fa-file-invoice-dollar"></i> Quote Settings
                            </h3>
                        </div>
                        <div class="settings-card-body">
                            <div class="settings-grid">
                        @foreach($settings['quote'] ?? [] as $setting)
                        <div class="settings-field">
                            <label for="setting_{{ $setting->key }}">
                                {{ ucwords(str_replace('_', ' ', str_replace('quote_', '', $setting->key))) }}
                            </label>
                            @if(in_array($setting->key, ['quote_terms', 'quote_footer']))
                            <textarea name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" 
                                class="form-input">{{ $setting->value }}</textarea>
                            @elseif($setting->type === 'integer')
                            <input type="number" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" 
                                value="{{ $setting->value }}" class="form-input">
                            @else
                            <input type="text" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" 
                                value="{{ $setting->value }}" class="form-input">
                            @endif
                            @if($setting->description)
                            <div class="field-description">{{ $setting->description }}</div>
                            @endif
                        </div>
                        @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Settings -->
                <div class="settings-tab-content" data-content="email">
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <h3 class="settings-section-title">
                                <i class="fas fa-envelope"></i> Email Settings
                            </h3>
                        </div>
                        <div class="settings-card-body">
                            <div class="settings-grid">
                        @foreach($settings['email'] ?? [] as $setting)
                        <div class="settings-field">
                            <label for="setting_{{ $setting->key }}">
                                {{ ucwords(str_replace('_', ' ', str_replace('email_', '', $setting->key))) }}
                            </label>
                            @if($setting->type === 'boolean')
                            <div class="checkbox-wrapper">
                                <input type="checkbox" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" 
                                    value="1" {{ $setting->value ? 'checked' : '' }}>
                                <span>Enable</span>
                            </div>
                            @else
                            <input type="text" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" 
                                value="{{ $setting->value }}" class="form-input">
                            @endif
                            @if($setting->description)
                            <div class="field-description">{{ $setting->description }}</div>
                            @endif
                        </div>
                        @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PDF Settings -->
                <div class="settings-tab-content" data-content="pdf">
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <h3 class="settings-section-title">
                                <i class="fas fa-file-pdf"></i> PDF Settings
                            </h3>
                        </div>
                        <div class="settings-card-body">
                            <div class="settings-grid">
                        @foreach($settings['pdf'] ?? [] as $setting)
                        <div class="settings-field">
                            <label for="setting_{{ $setting->key }}">
                                {{ ucwords(str_replace('_', ' ', str_replace('pdf_', '', $setting->key))) }}
                            </label>
                            @if($setting->key === 'pdf_page_size')
                            <select name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" class="form-input">
                                <option value="letter" {{ $setting->value === 'letter' ? 'selected' : '' }}>Letter</option>
                                <option value="a4" {{ $setting->value === 'a4' ? 'selected' : '' }}>A4</option>
                                <option value="legal" {{ $setting->value === 'legal' ? 'selected' : '' }}>Legal</option>
                            </select>
                            @elseif($setting->key === 'pdf_orientation')
                            <select name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" class="form-input">
                                <option value="portrait" {{ $setting->value === 'portrait' ? 'selected' : '' }}>Portrait</option>
                                <option value="landscape" {{ $setting->value === 'landscape' ? 'selected' : '' }}>Landscape</option>
                            </select>
                            @else
                            <input type="text" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" 
                                value="{{ $setting->value }}" class="form-input">
                            @endif
                            @if($setting->description)
                            <div class="field-description">{{ $setting->description }}</div>
                            @endif
                        </div>
                        @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="settings-form-actions">
                    <button type="submit" class="btn theme" id="saveBtn">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Tab switching
    $('.settings-tabs .tab').on('click', function() {
        const tab = $(this).data('tab');
        
        $('.settings-tabs .tab').removeClass('active');
        $(this).addClass('active');
        
        $('.settings-tab-content').removeClass('active');
        $(`.settings-tab-content[data-content="${tab}"]`).addClass('active');
    });

    // Form submission
    $('#settingsForm').on('submit', function(e) {
        e.preventDefault();
        
        const $btn = $('#saveBtn');
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');

        // Get form data
        const formData = new FormData(this);
        
        // Convert checkbox values
        $('input[type="checkbox"]').each(function() {
            const name = $(this).attr('name');
            if (name && !$(this).is(':checked')) {
                formData.set(name, '0');
            }
        });

        $.ajax({
            url: '{{ route("admin.settings.update") }}',
            method: 'POST',
            data: Object.fromEntries(formData),
            dataType: 'json',
            success: function(response) {
                if (response.ok) {
                    toastr.success(response.message || 'Settings updated successfully');
                } else {
                    toastr.error(response.message || 'Failed to update settings');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors || {};
                    let errorMsg = 'Validation failed:<br>';
                    Object.keys(errors).forEach(function(key) {
                        errorMsg += errors[key].join('<br>') + '<br>';
                    });
                    toastr.error(errorMsg);
                } else {
                    toastr.error('Failed to update settings');
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Search functionality
    $('.search-bar input').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        $('.settings-field').each(function() {
            const text = $(this).text().toLowerCase();
            if (text.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>
@endpush


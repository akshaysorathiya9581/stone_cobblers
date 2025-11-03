@extends('layouts.admin')

@section('title', 'Settings')

@push('css')
<style>
    .settings-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
        border-bottom: 2px solid #e5e7eb;
    }

    .settings-tab {
        padding: 12px 24px;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        color: #6b7280;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        margin-bottom: -2px;
    }

    .settings-tab:hover {
        color: #111827;
    }

    .settings-tab.active {
        color: #0ea5e9;
        border-bottom-color: #0ea5e9;
    }

    .settings-tab-content {
        display: none;
    }

    .settings-tab-content.active {
        display: block;
    }

    .settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 20px;
    }

    .settings-field {
        margin-bottom: 20px;
    }

    .settings-field label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #374151;
    }

    .settings-field .field-description {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }

    .settings-field input[type="text"],
    .settings-field input[type="email"],
    .settings-field input[type="number"],
    .settings-field select,
    .settings-field textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
    }

    .settings-field textarea {
        min-height: 100px;
        resize: vertical;
    }

    .settings-field input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .save-settings-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        padding: 14px 28px;
        background: #0ea5e9;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
        z-index: 100;
    }

    .save-settings-btn:hover {
        background: #0284c7;
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
    }

    .save-settings-btn:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
    }

    .settings-section-title {
        font-size: 18px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e5e7eb;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <!-- Header -->
    <div class="header">
        <div class="search-bar">
            <i>🔍</i>
            <input type="text" placeholder="Search settings...">
        </div>

        <div class="header-actions">
            <a href="{{ route('admin.profile.edit') }}"
                class="user-avatar">{{ auth()->user() ? Str::upper(Str::substr(auth()->user()->name ?? 'U', 0, 2)) : 'U' }}</a>
        </div>
    </div>

    <div class="content bg-content">
        <div class="quote-details">
            <div class="content-header">
                <h2 class="title">Settings</h2>
                <p style="color: #6b7280; margin-top: 5px;">Manage your application settings</p>
            </div>

            <form id="settingsForm">
                @csrf

                <!-- Tabs -->
                <div class="settings-tabs">
                    <button type="button" class="settings-tab active" data-tab="general">General</button>
                    <button type="button" class="settings-tab" data-tab="company">Company</button>
                    <button type="button" class="settings-tab" data-tab="tax">Tax & Pricing</button>
                    <button type="button" class="settings-tab" data-tab="quote">Quote</button>
                    <button type="button" class="settings-tab" data-tab="email">Email</button>
                    <button type="button" class="settings-tab" data-tab="pdf">PDF</button>
                </div>

                <!-- General Settings -->
                <div class="settings-tab-content active" data-content="general">
                    <h3 class="settings-section-title">General Settings</h3>
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

                <!-- Company Settings -->
                <div class="settings-tab-content" data-content="company">
                    <h3 class="settings-section-title">Company Information</h3>
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

                <!-- Tax & Pricing Settings -->
                <div class="settings-tab-content" data-content="tax">
                    <h3 class="settings-section-title">Tax & Pricing Settings</h3>
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

                <!-- Quote Settings -->
                <div class="settings-tab-content" data-content="quote">
                    <h3 class="settings-section-title">Quote Settings</h3>
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

                <!-- Email Settings -->
                <div class="settings-tab-content" data-content="email">
                    <h3 class="settings-section-title">Email Settings</h3>
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

                <!-- PDF Settings -->
                <div class="settings-tab-content" data-content="pdf">
                    <h3 class="settings-section-title">PDF Settings</h3>
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

                <button type="submit" class="save-settings-btn" id="saveBtn">
                    <i class="fa-solid fa-save"></i> Save Settings
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Tab switching
    $('.settings-tab').on('click', function() {
        const tab = $(this).data('tab');
        
        $('.settings-tab').removeClass('active');
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


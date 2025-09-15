@extends('layouts.admin')

@section('title', 'Add Quote')

@push('css')
    <style>
        .container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
        }

        .icon {
            width: 80px;
            height: 80px;
            background-color: rgb(22, 163, 74, 1);
            border-radius: 50%;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .icon::before {
            content: "🔨";
            font-size: 40px;
            color: white;
        }

        .title {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .title .brand {
            color: rgb(22, 163, 74, 1);
        }

        .description {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 40px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .quote-options {
            margin-bottom: 40px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .quote-options h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .checkbox-group {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 140px;
        }

        .checkbox-item:hover {
            border-color: rgb(22, 163, 74, 1);
            background-color: #f0fdf4;
        }

        .checkbox-item.selected {
            border-color: rgb(22, 163, 74, 1);
            background-color: #e8f5e8;
        }

        .checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: rgb(22, 163, 74, 1);
            cursor: pointer;
        }

        .checkbox-item label {
            font-weight: 500;
            color: #333;
            cursor: pointer;
            user-select: none;
        }

        .btn {
            background-color: rgb(22, 163, 74, 1);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .btn:hover {
            background-color: #1b5e20;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
        }

        .btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .time-estimate {
            font-size: 14px;
            color: #999;
            margin-top: 8px;
        }

        .error-message {
            color: #d32f2f;
            font-size: 14px;
            margin-top: 12px;
            display: none;
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }
            
            .title {
                font-size: 28px;
            }
            
            .checkbox-group {
                flex-direction: column;
                align-items: center;
            }
            
            .checkbox-item {
                width: 100%;
                max-width: 200px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="icon"></div>

        <h1 class="title">
            Welcome to<br><span class="brand">The Stone Cobblers</span>
        </h1>

        <p class="description">
            Transform your outdoor space with our premium stone cobbling services.
            Let's gather some details to provide you with a personalized quote.
        </p>

        <div class="quote-options">
            <h3>Select Quote Type</h3>
            <div class="checkbox-group">
                <div class="checkbox-item" onclick="toggleCheckbox('kitchen')">
                    <input type="checkbox" id="kitchen-quote" name="quote-type" value="kitchen">
                    <label for="kitchen-quote">Kitchen Quote</label>
                </div>
                <div class="checkbox-item" onclick="toggleCheckbox('vanity')">
                    <input type="checkbox" id="vanity-quote" name="quote-type" value="vanity" disabled>
                    <label for="vanity-quote">Vanity Quote</label>
                </div>
            </div>
        </div>

        <button class="btn" id="begin-btn" onclick="beginQuote()">
            Let's Begin <span>→</span>
        </button>

        <div class="time-estimate">Takes about 3 minutes to complete</div>

        <div class="error-message" id="error-message">
            Please select at least one quote type to continue.
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleCheckbox(type) {
            const checkbox = document.getElementById(type + '-quote');
            const item = checkbox.closest('.checkbox-item');

            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                item.classList.add('selected');
            } else {
                item.classList.remove('selected');
            }

            updateButtonState();
        }

        function updateButtonState() {
            const kitchenChecked = document.getElementById('kitchen-quote').checked;
            const vanityChecked = document.getElementById('vanity-quote').checked;
            const beginBtn = document.getElementById('begin-btn');
            const errorMessage = document.getElementById('error-message');

            if (kitchenChecked || vanityChecked) {
                beginBtn.disabled = false;
                errorMessage.style.display = 'none';
            } else {
                beginBtn.disabled = true;
                errorMessage.style.display = 'block';
            }
        }

        function beginQuote() {
            const kitchenChecked = document.getElementById('kitchen-quote').checked;
            const vanityChecked = document.getElementById('vanity-quote').checked;

            // Save selection to localStorage
            const quoteTypes = [];
            if (kitchenChecked) quoteTypes.push('kitchen');
            if (vanityChecked) quoteTypes.push('vanity');

            localStorage.setItem('selectedQuoteTypes', JSON.stringify(quoteTypes));

            // Navigate to the first selected quote type
            if (kitchenChecked) {
                window.location.href = "{{ route('admin.quote.form.show', ['type' => 'kitchen']) }}";
            } else if (vanityChecked) {
                window.location.href = 'vanity-form.html';
            }
        }

        // Initialize button state
        document.addEventListener('DOMContentLoaded', function() {
            updateButtonState();
        });
    </script>
@endpush

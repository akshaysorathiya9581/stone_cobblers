@extends('layouts.admin')

@section('title', 'Kitchen Quotes — Prices')

@push('css')

@endpush

@section('content')
  <div class="main-content">

    <!-- Header -->
    <div class="header">
      <div class="search-bar">
        <i>🔍</i>
        <input type="text" placeholder="Search quotes, customers...">
      </div>

      <div class="header-actions">
        <button class="header-btn secondary">
          <i>📤</i> Export
        </button>
        <button class="header-btn primary">
          <i>➕</i> New Quote
        </button>
        <a href="{{ route('admin.profile.edit') }}" class="user-avatar" aria-label="Open profile">BM</a>
      </div>
    </div>

    <div class="content bg-content">
      <div class="quote-details">
        <h2 class="title">Kitchen Top — Unit Price Update</h2>
        <form id="unit-price-form" method="POST" action="{{ route('admin.kitchen-quotes.store') }}">
          @csrf
  
          {{-- Kitchen Top table --}}
          <table class="table">
            <thead>
              <tr>
                <th style="width: 40%;">Item</th>
                <th>Unit Price</th>
              </tr>
            </thead>
            <tbody>
              @foreach($kitchen_tops as $name => $cost)
                @php
                  $n = (float) $cost;
                  $dec = ($n !== 0.0 && abs($n) < 1.0) ? 4 : 2;
                  $formatted = number_format($n, $dec, '.', '');
                @endphp
                <tr>
                  <td class="item-label">
                    {{ $name }}
                    <input type="hidden" name="kitchen[name][]" value="{{ $name }}">
                  </td>
                  <td>
                    <input type="number" name="kitchen[unit_price][]" class="qty-input money-input" step="0.0001" min="0"
                      value="{{ old('kitchen.unit_price.' . $loop->index, $formatted) }}">
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
  
          {{-- Cabinet Manufacturer table --}}
          <div class="quote-details__inside">
            <h3 class="subtitle">Cabinet Manufacturer — Unit Price Update</h3>
            <table class="table">
              <thead>
                <tr>
                  <th style="width: 40%;">Manufacturer</th>
                  <th>Unit Price</th>
                </tr>
              </thead>
              <tbody>
                @foreach($manufacturers as $name => $cost)
                  @php
                    $n = (float) $cost;
                    $dec = ($n !== 0.0 && abs($n) < 1.0) ? 4 : 2;
                    $formatted = number_format($n, $dec, '.', '');
                  @endphp
                  <tr>
                    <td class="item-label">
                      {{ $name }}
                      <input type="hidden" name="manufacturer[name][]" value="{{ $name }}">
                    </td>
                    <td>
                      <input type="number" name="manufacturer[unit_price][]" class="qty-input money-input" step="0.0001" min="0"
                        value="{{ old('manufacturer.unit_price.' . $loop->index, $formatted) }}">
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div style="display:flex;justify-content:flex-end;gap:12px;">
            <button type="submit" id="save-btn" class="btn theme">Save Prices</button>
          </div>
        </form>
      </div> 
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    $(function () {
      // revert back to DB values (reload page or restore original values stored in data-attrs)
      $('#revert-btn').on('click', function () { location.reload(); });

      $('#unit-price-form').on('submit', function (e) {
        e.preventDefault();
        // basic validation
        let ok = true;
        $(this).find('input[type="number"]').each(function () {
          const v = $(this).val();
          if (v === '' || isNaN(Number(v))) { $(this).addClass('is-invalid'); ok = false; }
          else $(this).removeClass('is-invalid');
        });
        if (!ok) { alert('Fix invalid price inputs'); return; }

        const fd = new FormData(this);
        const $btn = $('#save-btn');
        $btn.prop('disabled', true).text('Saving...');

        $.ajax({
          url: $(this).attr('action'),
          method: 'POST',
          data: fd,
          processData: false,
          contentType: false,
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val() }
        }).done(function (resp) {
          $btn.prop('disabled', false).text('Save Prices');
          if (resp && resp.success) {
            location.reload();
          } else {
            alert((resp && resp.message) ? resp.message : 'Save failed');
          }
        }).fail(function (xhr) {
          $btn.prop('disabled', false).text('Save Prices');
          alert('Save error');
        });
      });
    });
  </script>
@endpush
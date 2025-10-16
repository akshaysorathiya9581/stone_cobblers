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
        {{-- <button class="header-btn secondary">
          <i>📤</i> Export
        </button>
        <button class="header-btn primary">
          <i>➕</i> New Quote
        </button> --}}
        <a href="{{ route('admin.profile.edit') }}"
          class="user-avatar">{{ auth()->user() ? Str::upper(Str::substr(auth()->user()->name ?? 'U', 0, 2)) : 'U' }}</a>
      </div>
    </div>

    <div class="content bg-content">
      <div class="quote-details">
        <form id="unit-price-form" method="POST" action="{{ route('admin.kitchen-quotes.store') }}">
          <div class="content-header">
            <h2 class="title">Kitchen Top — Unit Price Update</h2>
            <a href="javascript:;" class="btn primary open-modal" data-target="#addQuoteModal">
              <i>➕</i> Add New Quote
            </a>
          </div>
          @csrf

          {{-- Kitchen Top table --}}
          <table class="table">
            <thead>
              <tr>
                <th style="width: 40%;">Item</th>
                <th>Unit Price</th>
                <th style="width: 7%;">Actions</th>
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
                  <td>
                    <div class="actions">
                      <a href="javascript:;" class="action-btn open-modal edit" title="Edit"
                        data-target="#editQuoteModal"><i class="fa-solid fa-pen-to-square"></i></a>
                      <a href="javascript:;" class="action-btn delete" title="Delete"><i class="fa-solid fa-trash"></i></a>
                    </div>
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
                  <th style="width: 7%;">Actions</th>
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
                      <input type="number" name="manufacturer[unit_price][]" class="qty-input money-input" step="0.0001"
                        min="0" value="{{ old('manufacturer.unit_price.' . $loop->index, $formatted) }}">
                    </td>
                    <td>
                      <div class="actions">
                        <a href="javascript:;" class="action-btn open-modal edit" title="Edit"
                          data-target="#editQuoteModal"><i class="fa-solid fa-pen-to-square"></i></a>
                        <a href="javascript:;" class="action-btn delete" title="Delete"><i
                            class="fa-solid fa-trash"></i></a>
                      </div>
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

  <!-- Custom Modal For Add Quotes -->
  <div class="modal" id="addQuoteModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="modalTitle">Add New Quotes</h2>
        <button class="close-btn" onclick="closeModal()">&times;</button>
      </div>

      <form class="quote-form">
        <div class="form-group">
          <label for="item" class="form-label">Item</label>
          <input type="text" name="" class="form-input" placeholder="Enter Item" required="">
        </div>
        <div class="form-group">
          <label for="unit-price" class="form-label">Unit Price</label>
          <input type="number" name="" class="form-input" placeholder="Enter Unit Price" required="">
        </div>
        <div class="form-group">
          <label for="unit-price" class="form-label">Unit Price</label>
          <select name="" class="form-input custom-select" data-placeholder="Select Quote Category">
            <option></option>
            <option value="Kitchen Top">Kitchen Top</option>
            <option value="Cabinet Manufacturer">Cabinet Manufacturer</option>
          </select>
        </div>
        <div class="btn-group">
          <button type="submit" class="btn theme">
            Submit
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal" id="editQuoteModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="modalTitle">Edit New Quotes</h2>
        <button class="close-btn" onclick="closeModal()">&times;</button>
      </div>

      <form class="quote-form">
        <div class="form-group">
          <label for="item" class="form-label">Item</label>
          <input type="text" name="" class="form-input" placeholder="Enter Item" required="">
        </div>
        <div class="form-group">
          <label for="unit-price" class="form-label">Unit Price</label>
          <input type="number" name="" class="form-input" placeholder="Enter Unit Price" required="">
        </div>
        <div class="form-group">
          <label for="unit-price" class="form-label">Unit Price</label>
          <select name="" class="form-input custom-select" data-placeholder="Select Quote Category">
            <option></option>
            <option value="Kitchen Top">Kitchen Top</option>
            <option value="Cabinet Manufacturer">Cabinet Manufacturer</option>
          </select>
        </div>
        <div class="btn-group">
          <button type="submit" class="btn theme">
            Save
          </button>
        </div>
      </form>
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
  <script>
    $(document).ready(function () {
      // Open modal
      $('.open-modal').on('click', function () {
        const targetModal = $(this).data('target');
        $(targetModal).fadeIn(200).addClass('active');
      });

      $('.close-btn').on('click', function () {
        $(this).closest('.modal').fadeOut(200).removeClass('active');
      });

      $('.modal').on('click', function (e) {
        if ($(e.target).is('.modal')) {
          $(this).fadeOut(200).removeClass('active');
        }
      });
    });
  </script>
@endpush
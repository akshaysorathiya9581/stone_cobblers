@extends('layouts.admin')

@section('title','Kitchen Quotes — Prices')

@push('css')
<style>
  .money-input { width:140px; text-align:right; }
  .item-label { padding:6px 0; }
  .is-invalid { border-color:#dc3545; }
</style>
@endpush

@section('content')
<div class="main-content">
  <div class="card" style="background:#fff;border-radius:6px;padding:12px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
      <h3 style="margin:0;color:#214a21;">Kitchen Top — Unit Price Update</h3>
    </div>

    <form id="unit-price-form" method="POST" action="{{ route('admin.kitchen-quotes.store') }}">
      @csrf

      {{-- Kitchen Top table --}}
      <table style="width:100%; margin-bottom:16px;">
        <thead>
          <tr><th style="text-align:left;">Item</th><th style="text-align:right;width:180px;">Unit Price</th></tr>
        </thead>
        <tbody>
          @foreach($kitchen_tops as $name => $cost)
            @php
              $n = (float)$cost;
              $dec = ($n !== 0.0 && abs($n) < 1.0) ? 4 : 2;
              $formatted = number_format($n, $dec, '.', '');
            @endphp
            <tr>
              <td class="item-label">
                {{ $name }}
                <input type="hidden" name="kitchen[name][]" value="{{ $name }}">
              </td>
              <td style="text-align:right;">
                <input type="number" name="kitchen[unit_price][]" class="money-input" step="0.0001" min="0"
                       value="{{ old('kitchen.unit_price.' . $loop->index, $formatted) }}">
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      {{-- Cabinet Manufacturer table --}}
      <div style="margin-top:12px;">
        <h4 style="margin:0 0 8px 0;">Cabinet Manufacturer — Unit Price Update</h4>
        <table style="width:100%; margin-bottom:16px;">
          <thead>
            <tr><th style="text-align:left;">Manufacturer</th><th style="text-align:right;width:180px;">Unit Price</th></tr>
          </thead>
          <tbody>
            @foreach($manufacturers as $name => $cost)
              @php
                $n = (float)$cost;
                $dec = ($n !== 0.0 && abs($n) < 1.0) ? 4 : 2;
                $formatted = number_format($n, $dec, '.', '');
              @endphp
              <tr>
                <td class="item-label">
                  {{ $name }}
                  <input type="hidden" name="manufacturer[name][]" value="{{ $name }}">
                </td>
                <td style="text-align:right;">
                  <input type="number" name="manufacturer[unit_price][]" class="money-input" step="0.0001" min="0"
                         value="{{ old('manufacturer.unit_price.' . $loop->index, $formatted) }}">
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div style="display:flex;justify-content:flex-end;gap:12px;">
        <button type="button" id="revert-btn" class="btn">Revert</button>
        <button type="submit" id="save-btn" class="btn btn-primary">Save Prices</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
  // revert back to DB values (reload page or restore original values stored in data-attrs)
  $('#revert-btn').on('click', function(){ location.reload(); });

  $('#unit-price-form').on('submit', function(e){
    e.preventDefault();
    // basic validation
    let ok = true;
    $(this).find('input[type="number"]').each(function(){
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
    }).done(function(resp){
      $btn.prop('disabled', false).text('Save Prices');
      if (resp && resp.success) {
        location.reload();
      } else {
        alert((resp && resp.message) ? resp.message : 'Save failed');
      }
    }).fail(function(xhr){
      $btn.prop('disabled', false).text('Save Prices');
      alert('Save error');
    });
  });
});
</script>
@endpush

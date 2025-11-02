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
      <div class="quote-details quote-details__listing">
        <div class="content-header">
          <h2 class="title">Kitchen Top — Unit Price Update</h2>
          <a href="javascript:;" class="btn primary open-modal" data-target="#addQuoteModal">
            <i>➕</i> Add New Quote
          </a>
        </div>

        {{-- Kitchen Top table --}}
        <table class="table">
          <thead>
            <tr>
              <th style="width: 35%;">Item</th>
              <th style="width: 15%;">Unit Price</th>
              <th style="width: 20%;">Type</th>
              <th style="width: 10%;">Taxable</th>
              <th style="width: 10%;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($kitchen_tops as $quote)
              @php
                $n = (float) $quote->cost;
                $dec = $n !== 0.0 && abs($n) < 1.0 ? 4 : 2;
                $formatted = number_format($n, $dec, '.', '');
              @endphp
              <tr data-id="{{ $quote->id }}" data-taxable="{{ $quote->is_taxable ? '1' : '0' }}">
                <td class="item-label">{{ $quote->project }}</td>
                <td>{{ $formatted }}</td>
                <td>{{ get_kitchen_type_list($quote->type) }}</td>
                <td class="text-center">
                  @if($quote->is_taxable)
                    <span class="badge badge-success" style="background: #22c55e; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px;">T</span>
                  @else
                    <span class="badge badge-secondary" style="background: #94a3b8; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px;">-</span>
                  @endif
                </td>
                <td>
                  <div class="actions">
                    <a href="javascript:;" class="action-btn open-modal edit" data-id="{{ $quote->id }}"
                      data-target="#editQuoteModal"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="javascript:;" class="action-btn delete" data-id="{{ $quote->id }}"><i
                        class="fa-solid fa-trash"></i></a>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

        {{-- Cabinet Manufacturer table --}}
        {{-- <div class="quote-details__inside">
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
              @foreach ($manufacturers as $quote)
              @php
              $n = (float) $quote->cost;
              $dec = $n !== 0.0 && abs($n) < 1.0 ? 4 : 2; $formatted=number_format($n, $dec, '.' , '' ); @endphp <tr
                data-id="{{ $quote->id }}">
                <td class="item-label">{{ $quote->project }}</td>
                <td>{{ $formatted }}</td>
                <td>
                  <div class="actions">
                    <a href="javascript:;" class="action-btn open-modal edit" data-id="{{ $quote->id }}"
                      data-target="#editQuoteModal"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="javascript:;" class="action-btn delete" data-id="{{ $quote->id }}"><i
                        class="fa-solid fa-trash"></i></a>
                  </div>
                </td>
                </tr>
                @endforeach
            </tbody>
          </table>
        </div> --}}
      </div>
    </div>

  </div>

  <!-- Custom Modal For Add Quotes -->
  <div class="modal" id="addQuoteModal" style="display:none;">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Add New Quote</h2>
        <button class="close-btn" data-close>&times;</button>
      </div>

      <form id="addQuoteForm" class="quote-form" autocomplete="off">
        @csrf
        <div class="form-group">
          <label for="add_item" class="form-label">Item</label>
          <input type="text" name="project" id="add_item" class="form-input" placeholder="Enter Item" required>
          <div class="invalid-feedback" data-field="item"></div>
        </div>

        <div class="form-group">
          <label for="add_unit_price" class="form-label">Unit Price</label>
          <input type="number" step="0.0001" name="cost" id="add_unit_price" class="form-input"
            placeholder="Enter Unit Price" required>
          <div class="invalid-feedback" data-field="unit_price"></div>
        </div>

        <div class="form-group">
          <label for="add_category" class="form-label">Category</label>
          <select name="type" id="add_category" class="form-input custom-select" data-placeholder="Select Quote Category"
            required>
            <option></option>
            @foreach (get_kitchen_type_list() as $type)
              <option value="{{ $type['id'] }}">{{ $type['text'] }}</option>
            @endforeach
          </select>
          <div class="invalid-feedback" data-field="category"></div>
        </div>

        <div class="form-group">
          <label class="form-label" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
            <input type="checkbox" name="is_taxable" id="add_is_taxable" value="1" style="width: 18px; height: 18px; cursor: pointer;">
            <span>Is Taxable?</span>
          </label>
          <div class="invalid-feedback" data-field="is_taxable"></div>
        </div>

        <div class="btn-group">
          <button type="submit" class="btn theme" id="addSubmitBtn">Submit</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Quote Modal -->
  <div class="modal" id="editQuoteModal" style="display:none;">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Edit Quote</h2>
        <button class="close-btn" data-close>&times;</button>
      </div>

      <form id="editQuoteForm" class="quote-form" autocomplete="off">
        @csrf
        @method('PUT')

        <input type="hidden" name="id" id="edit_id">

        <div class="form-group">
          <label for="edit_project" class="form-label">Item</label>
          <input type="text" name="project" id="edit_project" class="form-input" required>
          <div class="invalid-feedback" data-field="project" style="display:none;"></div>
        </div>

        <div class="form-group">
          <label for="edit_cost" class="form-label">Unit Price</label>
          <input type="number" step="0.0001" name="cost" id="edit_cost" class="form-input" required>
          <div class="invalid-feedback" data-field="cost" style="display:none;"></div>
        </div>

        <div class="form-group">
          <label for="edit_type" class="form-label">Category</label>
          <select name="type" id="edit_type" class="form-input custom-select" data-placeholder="Select Quote Category"
            required>
            <option></option>
            @foreach (get_kitchen_type_list() as $type)
              <option value="{{ $type['id'] }}">{{ $type['text'] }}</option>
            @endforeach
          </select>
          <div class="invalid-feedback" data-field="type" style="display:none;"></div>
        </div>

        <div class="form-group">
          <label class="form-label" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
            <input type="checkbox" name="is_taxable" id="edit_is_taxable" value="1" style="width: 18px; height: 18px; cursor: pointer;">
            <span>Is Taxable?</span>
          </label>
          <div class="invalid-feedback" data-field="is_taxable" style="display:none;"></div>
        </div>

        <div class="btn-group">
          <button type="submit" class="btn theme" id="editSubmitBtn">Save</button>
        </div>
      </form>
    </div>
  </div>

@endsection

@push('scripts')
  <script>
    $(function () {
      // Helpers
      function escapeHtml(s) {
        return (s || '').toString()
          .replace(/[&<>"']/g, m => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
          }[m]));
      }

      // open/close modal (you already have similar)
      $('.open-modal').on('click', function () {
        const target = $(this).data('target') || '#addQuoteModal';
        $(target).fadeIn(150).addClass('active');
      });

      $('[data-close], .close-btn').on('click', function () {
        $(this).closest('.modal').fadeOut(150).removeClass('active');
        clearFormErrors('#addQuoteForm');
        $('#addQuoteForm')[0]?.reset();
      });

      // close on click outside
      $('.modal').on('click', function (e) {
        if ($(e.target).is('.modal')) $(this).fadeOut(150).removeClass('active');
      });

      // show inline validation errors
      function clearFormErrors(formSelector) {
        $(formSelector).find('.invalid-feedback').text('').hide();
        $(formSelector).find('.is-invalid').removeClass('is-invalid');
      }

      function showFormErrors(formSelector, errors, old) {
        clearFormErrors(formSelector);
        if (!errors) return;
        Object.keys(errors).forEach(function (field) {
          const msg = errors[field][0] || '';
          const $input = $(formSelector).find('[name="' + field + '"]');
          $input.addClass('is-invalid');
          $(formSelector).find('.invalid-feedback[data-field="' + field + '"]').text(msg).show();
        });
        // re-populate old input if provided
        if (old) {
          Object.keys(old).forEach(function (k) {
            $(formSelector).find('[name="' + k + '"]').val(old[k]);
          });
        }
      }

      // put this near top of your script
      const typeMap = {
        'KITCHEN_TOP': 'Kitchen Top',
        'KITCHEN_MANUFACTURER': 'Cabinet Manufacturer',
        'KITCHEN_MARGIN_MARKUP': 'Margin / Markup',
        'KITCHEN_DELIVERY': 'Delivery Charges',
        'KITCHEN_CABINET': 'Cabinet Manufacturer' // if you use this alias
      };

      // buildRowHtml now returns 5 columns: item | price | category | taxable | actions
      function buildRowHtml(quote) {
        const unitPriceFormatted = (Number(quote.cost) || 0).toFixed((Math.abs(Number(quote.cost)) < 1 && Number(quote.cost) !== 0) ? 4 : 2);

        // prefer server-provided label, else use map
        const typeLabel = quote.type_label || typeMap[quote.type] || quote.type || '';
        
        // taxable badge
        const taxableBadge = quote.is_taxable 
          ? '<span class="badge badge-success" style="background: #22c55e; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px;">T</span>'
          : '<span class="badge badge-secondary" style="background: #94a3b8; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px;">-</span>';

        return `
                              <tr data-id="${quote.id}" data-taxable="${quote.is_taxable ? '1' : '0'}">
                                <td class="item-label">${escapeHtml(quote.project)}</td>
                                <td>${unitPriceFormatted}</td>
                                <td class="type-label">${escapeHtml(typeLabel)}</td>
                                <td class="text-center">${taxableBadge}</td>
                                <td>
                                  <div class="actions">
                                    <a href="javascript:;" class="action-btn open-modal edit" title="Edit" data-target="#editQuoteModal"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="javascript:;" class="action-btn delete" title="Delete"><i class="fa-solid fa-trash"></i></a>
                                  </div>
                                </td>
                              </tr>
                            `;
      }

      // append new quote row to the appropriate table
      function appendQuoteToTable(quote) {
        const $tbody = $('table').first().find('tbody'); // first table is kitchen_tops in your markup
        if ($tbody.find('.empty').length) $tbody.empty();
        $tbody.append(buildRowHtml(quote));
      }

      // AJAX submit for Add Quote form
      $('#addQuoteForm').on('submit', function (e) {
        e.preventDefault();
        const $form = $(this);
        clearFormErrors('#addQuoteForm');

        const $btn = $('#addSubmitBtn');
        $btn.prop('disabled', true).text('Saving...');

        const data = {
          project: $form.find('[name="project"]').val(),
          cost: $form.find('[name="cost"]').val(),
          type: $form.find('[name="type"]').val(),
          is_taxable: $form.find('[name="is_taxable"]').is(':checked') ? 1 : 0,
          _token: $form.find('input[name="_token"]').val() || $('meta[name="csrf-token"]')
            .attr('content')
        };

        $.ajax({
          url: "{{ route('admin.kitchen-quotes.store') }}",
          method: 'POST',
          dataType: 'json',
          data: data
        })
          .done(function (res, textStatus, xhr) {
            if (res && res.ok && res.data) {
              // append to table
              appendQuoteToTable(res.data);
              toastr.success(res.message || 'Quote added');
              // close modal and reset
              $('#addQuoteForm')[0].reset();
              $('#addQuoteModal').fadeOut(150).removeClass('active');
            } else {
              // unexpected response shape
              toastr.error('Unexpected server response');
            }
          })
          .fail(function (xhr) {
            if (xhr.status === 422) {
              // validation error
              const json = xhr.responseJSON || {};
              showFormErrors('#addQuoteForm', json.errors || {}, json.old || null);
              toastr.warning(json.message || 'Validation failed');
            } else {
              toastr.error('Failed to save quote');
            }
          })
          .always(function () {
            $btn.prop('disabled', false).text('Submit');
          });
      });

      // --- open edit modal and prefill with AJAX ---
      $(document).on('click', '.action-btn.open-modal.edit', function (e) {
        e.preventDefault();

        // find the row id: prefer data-id on TR; if not present, allow data-id on button
        const $tr = $(this).closest('tr');
        const id = $tr.data('id') || $(this).data('id');

        if (!id) return toastr.error('Missing row id');

        // fetch quote from server
        $.getJSON("{{ url('admin/kitchen-quotes') }}/" + encodeURIComponent(id))
          .done(function (res) {
            if (!res?.ok) {
              toastr.error('Failed to load quote');
              return;
            }
            const q = res.data;
            // populate edit form
            $('#edit_id').val(q.id);
            $('#edit_project').val(q.project);
            $('#edit_cost').val(q.cost);
            $('#edit_type').val(q.type).trigger('change.select2');
            $('#edit_is_taxable').prop('checked', q.is_taxable ? true : false);

            clearFormErrors('#editQuoteForm');
            $('#editQuoteModal').fadeIn(150).addClass('active');
          })
          .fail(function () {
            toastr.error('Failed to fetch quote');
          });
      });

      // --- submit edit form via AJAX (PATCH/PUT) ---
      $('#editQuoteForm').on('submit', function (e) {
        e.preventDefault();
        const $form = $(this);
        clearFormErrors('#editQuoteForm');

        const id = $('#edit_id').val();
        if (!id) return toastr.error('Missing id');

        const data = {
          project: $('#edit_project').val(),
          cost: $('#edit_cost').val(),
          type: $('#edit_type').val(),
          is_taxable: $('#edit_is_taxable').is(':checked') ? 1 : 0,
          _token: $form.find('input[name="_token"]').val()
        };

        const $btn = $('#editSubmitBtn').prop('disabled', true).text('Saving...');

        $.ajax({
          url: "{{ url('admin/kitchen-quotes') }}/" + encodeURIComponent(id),
          method: 'PUT', // or PATCH
          dataType: 'json',
          data: data
        }).done(function (res) {
          if (res && res.ok && res.data) {
            // Replace the TR contents for this id
            const q = res.data;
            const $tr = $('tr[data-id="' + q.id + '"]');

            // if table rendered server-side uses plain text cells, update them
            if ($tr.length) {
              // update data attribute
              $tr.attr('data-taxable', q.is_taxable ? '1' : '0');
              
              // update the item label cell
              $tr.find('.item-label').first().text(q.project);

              // update price (2nd column)
              const dec = (Math.abs(Number(q.cost)) < 1 && Number(q.cost) !== 0) ? 4 : 2;
              const formatted = Number(q.cost).toFixed(dec);
              $tr.find('td').eq(1).text(formatted);

              // update category (3rd column) - prefer server-provided label or map
              const typeLabel = q.type_label || typeMap[q.type] || q.type || '';
              $tr.find('td').eq(2).text(typeLabel);
              
              // update taxable badge (4th column)
              const taxableBadge = q.is_taxable 
                ? '<span class="badge badge-success" style="background: #22c55e; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px;">T</span>'
                : '<span class="badge badge-secondary" style="background: #94a3b8; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px;">-</span>';
              $tr.find('td').eq(3).html(taxableBadge);
            } else {
              // if row missing, append to table bottom
              appendQuoteToTable(q);
            }

            toastr.success(res.message || 'Quote updated');
            $('#editQuoteModal').fadeOut(150).removeClass('active');
          } else {
            toastr.error('Unexpected server response');
          }
        }).fail(function (xhr) {
          if (xhr.status === 422) {
            const json = xhr.responseJSON || {};
            showFormErrors('#editQuoteForm', json.errors || {}, json.old || null);
            toastr.warning(json.message || 'Validation failed');
          } else {
            toastr.error('Failed to update quote');
          }
        }).always(function () {
          $btn.prop('disabled', false).text('Save');
        });
      });

      // --- delete via AJAX (optional) ---
      $(document).on('click', '.action-btn.delete', function (e) {
        e.preventDefault();
        const $tr = $(this).closest('tr');
        const id = $tr.data('id') || $(this).data('id');
        if (!id) return toastr.error('Missing id');

        if (!confirm('Delete this item?')) return;

        $.ajax({
          url: "{{ url('admin/kitchen-quotes') }}/" + encodeURIComponent(id),
          method: 'DELETE',
          dataType: 'json',
          data: { _token: $('meta[name="csrf-token"]').attr('content') }
        }).done(function (res) {
          if (res && res.ok) {
            $tr.remove();
            toastr.success(res.message || 'Deleted');
          } else {
            toastr.error(res.message || 'Delete failed');
          }
        }).fail(function () {
          toastr.error('Failed to delete');
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
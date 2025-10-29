@extends('layouts.admin')

@section('title', 'Kitchen Quotes — Prices')

@push('css')
@endpush

@section('content')

  <div class="main-content" style="display: none;">
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
							<th style="width: 40%;">Item</th>
							<th>Unit Price</th>
							<th>Type</th>
							<th style="width: 7%;">Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($kitchen_tops as $quote)
							@php
								$n = (float) $quote->cost;
								$dec = $n !== 0.0 && abs($n) < 1.0 ? 4 : 2;
								$formatted = number_format($n, $dec, '.', '');
							@endphp
							<tr data-id="{{ $quote->id }}">
								<td class="item-label">{{ $quote->project }}</td>
								<td>{{ $formatted }}</td>
								<td>{{ get_kitchen_type_list($quote->type) }}</td>
								<td>
									<div class="actions">
										<a href="javascript:;" class="action-btn open-modal edit" data-id="{{ $quote->id }}" data-target="#editQuoteModal"><i class="fa-solid fa-pen-to-square"></i></a>
										<a href="javascript:;" class="action-btn delete" data-id="{{ $quote->id }}"><i class="fa-solid fa-trash"></i></a>
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
									$dec = $n !== 0.0 && abs($n) < 1.0 ? 4 : 2;
									$formatted = number_format($n, $dec, '.', '');
								@endphp
								<tr data-id="{{ $quote->id }}">
									<td class="item-label">{{ $quote->project }}</td>
									<td>{{ $formatted }}</td>
									<td>
										<div class="actions">
											<a href="javascript:;" class="action-btn open-modal edit" data-id="{{ $quote->id }}" data-target="#editQuoteModal"><i class="fa-solid fa-pen-to-square"></i></a>
											<a href="javascript:;" class="action-btn delete" data-id="{{ $quote->id }}"><i class="fa-solid fa-trash"></i></a>
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
                    <input type="text" name="project" id="add_item" class="form-input" placeholder="Enter Item"
                        required>
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
					<select name="type" id="add_category" class="form-input custom-select"
						data-placeholder="Select Quote Category" required>
						<option></option>
						@foreach (get_kitchen_type_list() as $type)
							<option value="{{ $type['id'] }}">{{ $type['text'] }}</option>
						@endforeach
					</select>
					<div class="invalid-feedback" data-field="category"></div>
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
				<select name="type" id="edit_type" class="form-input custom-select" data-placeholder="Select Quote Category" required>
					<option></option>
					@foreach (get_kitchen_type_list() as $type)
						<option value="{{ $type['id'] }}">{{ $type['text'] }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback" data-field="type" style="display:none;"></div>
			</div>

			<div class="btn-group">
				<button type="submit" class="btn theme" id="editSubmitBtn">Save</button>
			</div>
			</form>
		</div>
	</div>

  <!-- New Step Design -->
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

    <div class="content bg-white">
      <div class="quote-details">
        <div class="breadcrumb mb-8">
          <span class="breadcrumb-item">Quote Generation – Step 1 of 3</span>
        </div>
        <div class="content-header d-block">
          <h2 class="title">Stone by Stone: Your Perfect Kitchen Quote</h2>
          <h3 class="subtitle">Step 1 of 3 – Enter Item Quantities</h3>
          <div class="quote-steps">
            <!-- Progress Indicator -->
            <div class="progress-container">
              <div class="progress-step active">1</div>
              <div class="progress-line"></div>
              <div class="progress-step inactive">2</div>
              <div class="progress-line"></div>
              <div class="progress-step inactive">3</div>
            </div>
            <div class="progress-labels">
              <div class="progress-label active">Quantities</div>
              <div class="progress-label">Details</div>
              <div class="progress-label">Review</div>
            </div>
          </div>
        </div>
        <div class="quote-stepview">
          <div class="quote-stepview__left">
            <div class="stepview-title">
              <h3 class="title mb-8">Quote Items</h3>
              <p>Adjust quantities for each item</p>
            </div>
            <div class="custom-table">
              <table class="table">
                <thead>
                  <tr>
                    <th style="width: 15%;">Project/Item Name</th>
                    <th>Scope/Material</th>
                    <th>QTY</th>
                    <th>Unit Cost</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="label">Kitchen - Sq Ft <span class="t_tag">T</span> </td>
                    <td class="label">Granite</td>
                    <td class="label">
                      <div class="quantity-controls">
                        <button class="quantity-btn minus">−</button>
                        <input type="number" class="quantity-input" value="0" min="0" />
                        <button class="quantity-btn plus">+</button>
                      </div>
                    </td>
                    <td class="label">$75.00</td>
                    <td class="label">$3,750.00</td>
                  </tr>
                  <tr>
                    <td class="label">Labor Charge <span class="t_tag">T</span> </td>
                    <td class="label">Service</td>
                    <td class="label">
                      <div class="quantity-controls">
                        <button class="quantity-btn minus">−</button>
                        <input type="number" class="quantity-input" value="12" min="0" />
                        <button class="quantity-btn plus">+</button>
                      </div>
                    </td>
                    <td class="label">$120.00</td>
                    <td class="label">$1440.00</td>
                  </tr>
                  <tr>
                    <td class="label">Edge - Lin Ft</td>
                    <td class="label">Stone</td>
                    <td class="label">
                      <div class="quantity-controls">
                        <button class="quantity-btn minus">−</button>
                        <input type="number" class="quantity-input" value="16" min="0" />
                        <button class="quantity-btn plus">+</button>
                      </div>
                    </td>
                    <td class="label">$85.00</td>
                    <td class="label">$1,360.00</td>
                  </tr>
                  <tr>
                    <td class="label">Arc Charges <span class="t_tag">T</span> </td>
                    <td class="label">-</td>
                    <td class="label">
                      <div class="quantity-controls">
                        <button class="quantity-btn minus">−</button>
                        <input type="number" class="quantity-input" value="50" min="0" />
                        <button class="quantity-btn plus">+</button>
                      </div>
                    </td>
                    <td class="label">$250.00</td>
                    <td class="label">$250.00</td>
                  </tr>
                  <tr>
                    <td class="label">Bump-Outs</td>
                    <td class="label">-</td>
                    <td class="label">
                      <div class="quantity-controls">
                        <button class="quantity-btn minus">−</button>
                        <input type="number" class="quantity-input" value="62" min="0" />
                        <button class="quantity-btn plus">+</button>
                      </div>
                    </td>
                    <td class="label">$15.00</td>
                    <td class="label">$930.00</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="quote-stepview__right">
            <div class="stepview-title">
              <h3 class="title">Quote Summary</h3>
            </div>
            <div class="summary-item">
              <div class="summary-label">Subtotal</div>
              <div class="summary-amount" id="subtotal">$7,730.00</div>
            </div>
            <div class="summary-item">
              <div class="summary-label">Tax (8%)</div>
              <div class="summary-amount" id="tax">$618.40</div>
            </div>
            <div class="summary-item grand-total">
              <div class="summary-label">Grand Total</div>
              <div class="summary-amount" id="grand-total">$8,348.40</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="step-footer">
      <div class="footer-content">
        <div class="step-indicator">Step 1 of 3</div>
        <div class="footer-actions">
          <button class="btn secondary">Previous</button>
          <button class="btn theme">Next</button>
        </div>
      </div>
    </footer>

  </div>

@endsection

@push('scripts')
    <script>
        $(function() {
            // Helpers
            function escapeHtml(s) {
                return (s || '').toString()
                    .replace(/[&<>"']/g, m => ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    } [m]));
            }

            // open/close modal (you already have similar)
            $('.open-modal').on('click', function() {
                const target = $(this).data('target') || '#addQuoteModal';
                $(target).fadeIn(150).addClass('active');
            });

            $('[data-close], .close-btn').on('click', function() {
                $(this).closest('.modal').fadeOut(150).removeClass('active');
                clearFormErrors('#addQuoteForm');
                $('#addQuoteForm')[0]?.reset();
            });

            // close on click outside
            $('.modal').on('click', function(e) {
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
                Object.keys(errors).forEach(function(field) {
                    const msg = errors[field][0] || '';
                    const $input = $(formSelector).find('[name="' + field + '"]');
                    $input.addClass('is-invalid');
                    $(formSelector).find('.invalid-feedback[data-field="' + field + '"]').text(msg).show();
                });
                // re-populate old input if provided
                if (old) {
                    Object.keys(old).forEach(function(k) {
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

			// buildRowHtml now returns 4 columns: item | price | category | actions
			function buildRowHtml(quote) {
				const unitPriceFormatted = (Number(quote.cost) || 0).toFixed((Math.abs(Number(quote.cost)) < 1 && Number(quote.cost) !== 0) ? 4 : 2);

				// prefer server-provided label, else use map
				const typeLabel = quote.type_label || typeMap[quote.type] || quote.type || '';

				return `
					<tr data-id="${quote.id}">
						<td class="item-label">${escapeHtml(quote.project)}</td>
						<td>${unitPriceFormatted}</td>
						<td class="type-label">${escapeHtml(typeLabel)}</td>  <!-- NEW -->
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
            $('#addQuoteForm').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                clearFormErrors('#addQuoteForm');

                const $btn = $('#addSubmitBtn');
                $btn.prop('disabled', true).text('Saving...');

                const data = {
                    project: $form.find('[name="project"]').val(),
                    cost: $form.find('[name="cost"]').val(),
                    type: $form.find('[name="type"]').val(),
                    _token: $form.find('input[name="_token"]').val() || $('meta[name="csrf-token"]')
                        .attr('content')
                };

                $.ajax({
                        url: "{{ route('admin.kitchen-quotes.store') }}",
                        method: 'POST',
                        dataType: 'json',
                        data: data
                    })
                    .done(function(res, textStatus, xhr) {
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
                    .fail(function(xhr) {
                        if (xhr.status === 422) {
                            // validation error
                            const json = xhr.responseJSON || {};
                            showFormErrors('#addQuoteForm', json.errors || {}, json.old || null);
                            toastr.warning(json.message || 'Validation failed');
                        } else {
                            toastr.error('Failed to save quote');
                        }
                    })
                    .always(function() {
                        $btn.prop('disabled', false).text('Submit');
                    });
            });
			
			// --- open edit modal and prefill with AJAX ---
			$(document).on('click', '.action-btn.open-modal.edit', function(e) {
				e.preventDefault();

				// find the row id: prefer data-id on TR; if not present, allow data-id on button
				const $tr = $(this).closest('tr');
				const id = $tr.data('id') || $(this).data('id');

				if (!id) return toastr.error('Missing row id');

				// fetch quote from server
				$.getJSON("{{ url('admin/kitchen-quotes') }}/" + encodeURIComponent(id))
					.done(function(res) {
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

						clearFormErrors('#editQuoteForm');
						$('#editQuoteModal').fadeIn(150).addClass('active');
					})
					.fail(function() {
						toastr.error('Failed to fetch quote');
					});
			});

			// --- submit edit form via AJAX (PATCH/PUT) ---
			$('#editQuoteForm').on('submit', function(e) {
				e.preventDefault();
				const $form = $(this);
				clearFormErrors('#editQuoteForm');

				const id = $('#edit_id').val();
				if (!id) return toastr.error('Missing id');

				const data = {
					project: $('#edit_project').val(),
					cost: $('#edit_cost').val(),
					type: $('#edit_type').val(),
					_token: $form.find('input[name="_token"]').val()
				};

				const $btn = $('#editSubmitBtn').prop('disabled', true).text('Saving...');

				$.ajax({
					url: "{{ url('admin/kitchen-quotes') }}/" + encodeURIComponent(id),
					method: 'PUT', // or PATCH
					dataType: 'json',
					data: data
				}).done(function(res) {
					if (res && res.ok && res.data) {
						// Replace the TR contents for this id
						const q = res.data;
						const $tr = $('tr[data-id="' + q.id + '"]');

						// if table rendered server-side uses plain text cells, update them
						if ($tr.length) {
							// update the item label cell
							$tr.find('.item-label').first().text(q.project);

							// update price (2nd column)
							const dec = (Math.abs(Number(q.cost)) < 1 && Number(q.cost) !== 0) ? 4 : 2;
							const formatted = Number(q.cost).toFixed(dec);
							$tr.find('td').eq(1).text(formatted);

							// update category (3rd column) - prefer server-provided label or map
							const typeLabel = q.type_label || typeMap[q.type] || q.type || '';
							$tr.find('td').eq(2).text(typeLabel);
						} else {
							// if row missing, append to table bottom
							appendQuoteToTable(q);
						}

						toastr.success(res.message || 'Quote updated');
						$('#editQuoteModal').fadeOut(150).removeClass('active');
					} else {
						toastr.error('Unexpected server response');
					}
				}).fail(function(xhr) {
					if (xhr.status === 422) {
						const json = xhr.responseJSON || {};
						showFormErrors('#editQuoteForm', json.errors || {}, json.old || null);
						toastr.warning(json.message || 'Validation failed');
					} else {
						toastr.error('Failed to update quote');
					}
				}).always(function() {
					$btn.prop('disabled', false).text('Save');
				});
			});

			// --- delete via AJAX (optional) ---
			$(document).on('click', '.action-btn.delete', function(e) {
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
				}).done(function(res) {
					if (res && res.ok) {
						$tr.remove();
						toastr.success(res.message || 'Deleted');
					} else {
						toastr.error(res.message || 'Delete failed');
					}
				}).fail(function() {
					toastr.error('Failed to delete');
				});
			});

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

  <script>
    $(document).ready(function () {
      $(".quantity-controls").each(function () {
        const $container = $(this);

        $container.find(".plus").click(function () {
          let $input = $container.find(".quantity-input");
          let value = parseInt($input.val()) || 0;
          $input.val(value + 1);
        });

        $container.find(".minus").click(function () {
          let $input = $container.find(".quantity-input");
          let value = parseInt($input.val()) || 0;
          if (value > 0) {
            $input.val(value - 1);
          }
        });
      });
    });
  </script>

@endpush

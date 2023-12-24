@extends('web::layouts.grids.12', ['viewname' => 'seat-ratting-taxes::index'])

@section('page_header', 'Ratting Taxes')

@section('full')
<style>
.icon {
  width: 3rem;
  height: 3rem;
}

.icon i {
  font-size: 2.25rem;
}

.icon-shape {
  display: inline-flex;
  padding: 12px;
  text-align: center;
  border-radius: 50%;
  align-items: center;
  justify-content: center;
}

.icon-shape i {
  font-size: 1.25rem;
}
.dropdown-menu {
    max-height: 300px; /* Adjust the height as needed */
    overflow-y: auto; /* Enable vertical scrolling */
}
</style>
<div class="row">
    <div class="col-xl-4 col-lg-6">
        <div class="card card-stats mb-4 mb-xl-0">
        <div class="card-body">
            <div class="row">
            <div class="col">
                <span class="h2 font-weight-bold mb-0">{{number_format($totalAmountThisMonth, 2)}} <sup>ISK</sup></span>
            </div>
            <div class="col-auto">
                <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                <i class="fas fa-chart-bar"></i>
                </div>
            </div>
            </div>
            <p class="mt-3 mb-0 text-muted text-sm">
            <span class="text-nowrap">Total Ratting Taxes for {{ now()->format('F Y') }}</span>
            </p>
        </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mt-4">
        <div class="card shadow">

        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">Ratting Taxes</h3>
                </div>
                <div class="col text-right">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="systemNameFilterDropdown">Filter by System Name</label>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="systemNameFilterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Select Systems
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="systemNameFilterDropdown">
                                        @foreach($uniqueSystemNames as $region => $systems)
                                            <h6 class="dropdown-header" style="color: white;font-weight: 600;">{{ $region }}</h6>
                                            @foreach($systems as $system)
                                                <label class="dropdown-item">
                                                    <input type="checkbox" class="system-name-filter" value="{{ $system['name'] }}">
                                                    {{ $system['name'] }}
                                                </label>
                                            @endforeach
                                            <div class="dropdown-divider"></div> <!-- Optional divider between regions -->
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive" id="wallet_journal">
                <!-- Projects table -->
                <table id="walletJournalTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>System</th>
                            <th>From</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>
</div>

@push('javascript')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="/vendor/datatables/buttons.server-side.js"></script>
<script>
$(document).ready(function() {
    var table = $('#walletJournalTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("seat-ratting-taxes::journal-data") }}',
            data: function (d) {
                // Collect all checked system names
                d.systemNames = [];
                $('input.system-name-filter:checked').each(function() {
                    d.systemNames.push(this.value);
                });
            }
        },
        columns: [
            { data: 'date', name: 'date' },
            { data: 'amount', name: 'amount' },
            { data: 'system_name', name: 'system_name' },
            {
                data: 'second_party',
                name: 'second_party',
                searchable: true,
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            'excel'
        ],
        // Initialize the Buttons extension
        searching: false,
        lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, "All"]], // Add length menu for pagination
        pageLength: 50, // Set default page length
    });

    $('input.system-name-filter').on('change', function() {
        table.draw(); // Redraw the table to apply the filters
    });
});
</script>
@endpush

@stop

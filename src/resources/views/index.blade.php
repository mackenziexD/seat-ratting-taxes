@extends('web::layouts.grids.12', ['viewname' => 'seat-ratting-taxes::index'])

@section('page_header', 'Ratting Taxes')

@section('full')
<style>
.dropdown-menu {
    max-height: 300px; /* Adjust the height as needed */
    overflow-y: auto; /* Enable vertical scrolling */
}
</style>
<div class="row">
    <div class="col-md-4 col-sm-6">
        <!-- Online Badge -->
        <div class="info-box">
        <span class="info-box-icon elevation-1 bg-green"><i class="far fa-money-bill-alt"></i></span>
        <div class="info-box-content">
            <span class="info-box-text">Total Ratting Taxes for <span id="currentMonth">{{ now()->format('F Y') }}</span> (This Month)</span>
            <span class="info-box-number">
                <span id="totalAmountThisMonth">{{ number_format($totalAmountThisMonth, 2) }}</span> <sup>ISK</sup>
            </span>
        </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
    </div>

    <div class="col-md-4 col-sm-6">
        <!-- Online Badge -->
        <div class="info-box">
        <span class="info-box-icon elevation-1 bg-red"><i class="far fa-money-bill-alt"></i></span>
        <div class="info-box-content">
            <span class="info-box-text">Total Ratting Taxes for <span id="lastMonth">{{ now()->subMonth()->format('F Y') }}</span> (Last Month)</span>
            <span class="info-box-number">
                <span id="totalAmountLastMonth">{{ number_format($totalAmountLastMonth, 2) }}</span> <sup>ISK</sup>
            </span>
        </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
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
                        <div class="col">
                            <div class="form-group">
                                <label for="monthPicker">Select Month</label>
                                <select id="monthPicker" class="form-control">
                                    @for ($i = 0; $i < 12; $i++)
                                        <option value="{{ now()->subMonths($i)->format('Y-m') }}">{{ now()->subMonths($i)->format('F Y') }}</option>
                                    @endfor
                                </select>
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

                // Get selected month
                var selectedMonth = $('#monthPicker').val();
                if (selectedMonth) {
                    d.start_date = new Date(selectedMonth + '-01').toISOString();
                    d.end_date = new Date(new Date(selectedMonth + '-01').setMonth(new Date(selectedMonth + '-01').getMonth() + 1) - 1).toISOString();
                }
            }
        },
        columns: [
            { data: 'formatted_date', name: 'date' },
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
            'excel',
        ],
        // Initialize the Buttons extension
        searching: false,
        lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, "All"]], // Add length menu for pagination
        pageLength: 50, // Set default page length
        drawCallback: function() {
            hideLoadingIndicator();
        }
    });

    function showLoadingIndicator() {
        console.log("show");
        $('#walletJournalTable tbody').hide();
        $('#walletJournalTable_processing').show();
    }

    function hideLoadingIndicator() {
        console.log("hide")
        $('#walletJournalTable tbody').show();
        $('#walletJournalTable_processing').hide();
    }

    $('#monthPicker').on('change', function() {
        var selectedMonth = $(this).val();
        showLoadingIndicator();
        updateCards(selectedMonth);
        table.draw();
    });

    $('input.system-name-filter').on('change', function() {
        showLoadingIndicator();
        table.draw();
    });

    function updateCards(selectedMonth) {
        $.ajax({
            url: '{{ route("seat-ratting-taxes::get-monthly-data") }}',
            method: 'GET',
            data: { month: selectedMonth },
            success: function(data) {
                $('#currentMonth').text(data.currentMonthName);
                $('#totalAmountThisMonth').text(data.totalAmountThisMonth);
                $('#lastMonth').text(data.lastMonthName);
                $('#totalAmountLastMonth').text(data.totalAmountLastMonth);
                hideLoadingIndicator();
            },
            error: function() {
                hideLoadingIndicator();
            }
        });
    }
});
</script>
@endpush

@stop
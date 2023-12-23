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
</style>
<div class="row">
    <div class="col-xl-4 col-lg-6">
        <div class="card card-stats mb-4 mb-xl-0">
        <div class="card-body">
            <div class="row">
            <div class="col">
                <span class="h2 font-weight-bold mb-0">{{number_format($totalAmountThisMonth, 2)}}</span>
            </div>
            <div class="col-auto">
                <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
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
                <a href="#!" class="btn btn-sm btn-primary">See all</a>
            </div>
            </div>
        </div>
        <div class="table-responsive">
            <!-- Projects table -->
            <table class="table align-items-center table-flush">
            <thead class="thead-light">
                <tr>
                <th scope="col">Date</th>
                <th scope="col">Amount</th>
                <th scope="col">System</th>
                <th scope="col">Pilot</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wallet_journal as $journal)
                    <tr>
                        <th scope="row">
                            {{ \Carbon\Carbon::parse($journal->date)->format('Y-m-d') }}
                        </th>
                        <td>
                            {{ number_format($journal->amount, 2) }}
                        </td>
                        <td>
                            {{ $journal->description }}
                        </td>
                        <td>
                            {{ $journal->second_party->name }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            </table>
        </div>
        <div class="card-footer py-4">
            <nav aria-label="...">
            {{ $wallet_journal->links() }}
            </nav>
        </div>
        </div>
    </div>
</div>
@stop

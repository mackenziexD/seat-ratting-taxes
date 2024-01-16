<?php

namespace Helious\SeatRattingTaxes\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Seat\Eveapi\Models\Wallet\CorporationWalletJournal;
use Seat\Eveapi\Models\Sde\SolarSystem;
use Helious\SeatRattingTaxes\Services\SystemNameExtractor;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Cache;
use Maatwebsite\Excel\Facades\Excel;
use Helious\SeatRattingTaxes\Exports\JournalExport;

use Helious\SeatBeacons\Http\Datatables\BeaconsDataTable;

/**
 * Class HomeController.
 *
 * @package Author\Seat\YourPackage\Http\Controllers
 */
class RattingTaxController extends Controller
{

    public function getUniqueSystemNames()
    {
        return Cache::remember('unique_system_names', 60 * 60, function () {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            $descriptions = CorporationWalletJournal::where('corporation_id', 2014367342)
                ->where('ref_type', 'bounty_prizes')
                ->where('amount', '>', 0)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->pluck('description'); // Get a collection of descriptions
        
            $systemNames = collect($descriptions)
                ->map(function ($description) {
                    // Use the service to extract the system name
                    return SystemNameExtractor::extract($description);
                })
                ->unique()
                ->reject(function ($name) {
                    // Reject 'Unknown System' if you do not wish to include it in the filters
                    return $name === 'Unknown System';
                })
                ->values();
        
                $systems = collect($systemNames)->map(function ($systemName) {
                    // Find the system in the SolarSystem model
                    $system = SolarSystem::with('region')->where('name', $systemName)->first();
            
                    return [
                        'name' => $systemName,
                        'region' => $system ? $system->region->name : 'Unknown Region'
                    ];
                });
            
                // Group the systems by region
                $groupedSystems = $systems->groupBy('region');
        
            return $groupedSystems;
        });
    }

    public function index()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $totalAmountThisMonth = CorporationWalletJournal::where('corporation_id', 2014367342)
            ->where('ref_type', 'bounty_prizes')
            ->where('amount', '>', 0)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $uniqueSystemNames = $this->getUniqueSystemNames();
        return view('seat-ratting-taxes::index', compact('totalAmountThisMonth', 'uniqueSystemNames'));
    }

    public function getJournalData(Request $request)
    {
        $startOfMonth = $request->input('start_date', Carbon::now()->startOfMonth());
        $endOfMonth = $request->input('end_date', Carbon::now()->endOfMonth());

        $query = CorporationWalletJournal::query()
            ->where('corporation_id', 2014367342)
            ->where('ref_type', 'bounty_prizes')
            ->where('amount', '>', 0)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->select(['date', 'amount', 'description', 'second_party_id']);

        if ($request->has('systemNames')) {
            $systemNames = $request->systemNames;
            $query->where(function($q) use ($systemNames) {
                foreach ($systemNames as $systemName) {
                    $q->orWhere('description', 'like', '%' . $systemName . '%');
                }
            });
        }

        if($request->has('action') && $request->action == 'excel'){
            if ($request->has('systemNames')) {
                $systemNames = $request->systemNames;
                $query->where(function($q) use ($systemNames) {
                    foreach ($systemNames as $systemName) {
                        $q->orWhere('description', 'like', '%' . $systemName . '%');
                    }
                });
            }

            $data = $query->get()->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('Y-m-d H:i:s'),
                    'amount' => $item->amount,
                    'system_name' => SystemNameExtractor::extract($item->description),
                    'From' => $item->second_party ? $item->second_party->name : 'Unknown'
                ];
            });
        
            return Excel::download(new JournalExport($data), 'journal.xlsx');
        }

        return DataTables::of($query)
            ->editColumn('amount', function ($row) {
                return number_format($row->amount, 2);
            })
            ->editColumn('date', function ($row) {
                return Carbon::parse($row->date)->diffForHumans();
            })
            ->addColumn('formatted_date', function ($row) {
                return Carbon::parse($row->date)->format('Y-m-d');
            })
            ->addColumn('second_party', function ($row) {
                return $row->second_party ? $row->second_party->name : 'Unknown';
            })              
            ->addColumn('system_name', function ($row) {
                $pattern = '/\b[A-Z0-9-]+\b$/';
                if (preg_match($pattern, $row->description, $matches)) {
                    return $matches[0] ?? 'Unknown System';
                }
                return 'Unknown System';
            })
            ->make(true);
    }
}

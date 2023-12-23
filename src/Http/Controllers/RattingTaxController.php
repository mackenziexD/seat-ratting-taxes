<?php

namespace Helious\SeatRattingTaxes\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Seat\Eveapi\Models\Wallet\CorporationWalletJournal;
use Carbon\Carbon;

/**
 * Class HomeController.
 *
 * @package Author\Seat\YourPackage\Http\Controllers
 */
class RattingTaxController extends Controller
{
    /** 
     * @return \Illuminate\View\View
     */
    public function index()
    {

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now();

        $wallet_journal = CorporationWalletJournal::where('corporation_id', 2014367342)
            ->where('ref_type', 'bounty_prizes')
            ->where('amount', '>', 0)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date', 'desc')
            ->paginate(50);

        //     $systemNames = ['UER-TH', 'JE1-36', 'XYZ-12'];
        //     $regexPattern = '(' . implode('|', $systemNames) . ')$';

        //     $wallet_journal = CorporationWalletJournal::where('corporation_id', 2014367342)
        //         ->where('ref_type', 'bounty_prizes')
        //         ->where('amount', '>', 0)
        //         ->where('description', 'REGEXP', $regexPattern)
        //         ->orderBy('date', 'desc')
        //         ->paginate(50);

        //     dd($wallet_journal);

        $totalAmountThisMonth = CorporationWalletJournal::where('corporation_id', 2014367342)
            ->where('ref_type', 'bounty_prizes')
            ->where('amount', '>', 0)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        
        return view('seat-ratting-taxes::index', compact('wallet_journal', 'totalAmountThisMonth'));
    }
}

<?php

namespace Helious\SeatMoons\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Seat\Eveapi\Models\Industry\CorporationIndustryMiningExtraction;
use Seat\Eveapi\Models\Corporation\CorporationInfo;

/**
 * Class HomeController.
 *
 * @package Author\Seat\YourPackage\Http\Controllers
 */
class MoonsController extends Controller
{
    /** 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        dd("Hello World!");
    }
}

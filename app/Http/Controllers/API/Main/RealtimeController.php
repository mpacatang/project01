<?php
namespace App\Http\Controllers\API\Main;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\RealtimeRepository;

class RealtimeController extends Controller
{
    protected $realtimeRepository;

    public function __construct(RealtimeRepository $realtimeRepository)
    {
        $this->realtimeRepository = $realtimeRepository;
    }

    /**
     * Get Realtime Data
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function getRealtime(Request $request)
    {
        return response()->json(
            $this->realtimeRepository->getRealtime($request), 200, [], JSON_NUMERIC_CHECK
        );
    }
}

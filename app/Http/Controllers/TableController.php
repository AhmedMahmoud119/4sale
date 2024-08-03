<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Reservation;
use Carbon\Carbon;

class TableController extends Controller
{
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'guests' => 'required|integer',
            'from_time' => 'required|date_format:Y-m-d H:i:s',
            'to_time' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $guests = $request->input('guests');
        $fromTime = Carbon::parse($request->input('from_time'));
        $toTime = Carbon::parse($request->input('to_time'));

        $availableTables = Table::where('capacity', '>=', $guests)
            ->whereDoesntHave('reservations', function ($query) use ($fromTime, $toTime) {
                $query->where(function ($query) use ($fromTime, $toTime) {
                    $query->where('from_time', '<', $toTime)
                          ->where('to_time', '>', $fromTime);
                });
            })->get();

        if ($availableTables->isEmpty()) {
            return response()->json(['message' => 'No tables available'], 404);
        }

        return response()->json(['available_tables' => $availableTables], 200);
    }
}

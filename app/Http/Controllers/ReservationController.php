<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Reservation;
use App\Models\Customer;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function reserveTable(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'customer_id' => 'required|exists:customers,id',
            'from_time' => 'required|date_format:Y-m-d H:i:s',
            'to_time' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $tableId = $request->input('table_id');
        $customerId = $request->input('customer_id');
        $fromTime = Carbon::parse($request->input('from_time'));
        $toTime = Carbon::parse($request->input('to_time'));

        $isAvailable = !Reservation::where('table_id', $tableId)
            ->where(function ($query) use ($fromTime, $toTime) {
                $query->where(function ($query) use ($fromTime, $toTime) {
                    $query->where('from_time', '<', $toTime)
                          ->where('to_time', '>', $fromTime);
                });
            })
            ->exists();

        if (!$isAvailable) {
            return response()->json(['message' => 'Table is not available for the selected time'], 409);
        }

        $reservation = Reservation::create([
            'table_id' => $tableId,
            'customer_id' => $customerId,
            'from_time' => $fromTime,
            'to_time' => $toTime,
        ]);

        return response()->json(['reservation' => $reservation], 201);
    }
}

<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Stay;
use App\Models\RoomType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ReservationAvailabilityService
{
    /**
     * Check if a room type is available for a given date range.
     *
     * @param int $roomTypeId
     * @param string|Carbon $checkIn
     * @param string|Carbon $checkOut
     * @param int|null $excludeOrderId Exclude this order from the count (for updates)
     * @return bool
     */
    public function isAvailable($roomTypeId, $checkIn, $checkOut, $excludeOrderId = null)
    {
        $checkIn = Carbon::parse($checkIn)->startOfDay();
        $checkOut = Carbon::parse($checkOut)->startOfDay();

        // Total rooms of this type
        $roomType = RoomType::withCount('rooms')->findOrFail($roomTypeId);
        $totalRooms = $roomType->rooms_count;

        if ($totalRooms <= 0) {
            return false;
        }

        // We check availability for each night
        $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());

        foreach ($period as $date) {
            $occupiedCount = $this->getOccupiedCountForDate($roomTypeId, $date, $excludeOrderId);

            if ($occupiedCount >= $totalRooms) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the number of occupied rooms for a specific date and room type.
     */
    private function getOccupiedCountForDate($roomTypeId, Carbon $date, $excludeOrderId = null)
    {
        // 1. Count Orders (Reservations) that overlap with this date
        // An order overlaps if: check_in <= date AND check_out > date
        $ordersCount = Order::where('room_type_id', $roomTypeId)
            ->whereIn('status', ['pendiente', 'confirmada', 'reserva_previa', 'ocupada'])
            ->where('check_in', '<=', $date)
            ->where('check_out', '>', $date)
            ->when($excludeOrderId, function ($query) use ($excludeOrderId) {
                return $query->where('id', '!=', $excludeOrderId);
            })
            ->count();

        // 2. Count active Stays that might not be linked to an Order (Walk-ins)
        // Note: Usually Stays are linked to an Order (reservation_id), 
        // we should avoid double counting if they are linked.
        // But in this system, it seems Stay status 'InHouse' is for active guests.
        
        $staysCount = Stay::whereHas('room', function($q) use ($roomTypeId) {
                $q->where('room_type_id', $roomTypeId);
            })
            ->where('arrival_at', '<=', $date)
            ->where('departure_at', '>', $date)
            ->where('status', 'InHouse')
            ->whereNull('reservation_id') // Only count those NOT linked to an Order to avoid double counting
            ->count();

        return $ordersCount + $staysCount;
    }
}

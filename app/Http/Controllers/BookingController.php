<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function getBookings()
    {
        // Read JSON files
        $bookingsJson = Storage::get('bookings.json');
        $workshopsJson = Storage::get('workshops.json');

        // Decode JSON to associative arrays
        $bookings = json_decode($bookingsJson, true);
        $workshops = json_decode($workshopsJson, true);

        // Create a lookup array for workshops
        $workshopLookup = [];
        foreach ($workshops['data'] as $workshop) {
            $workshopLookup[$workshop['code']] = $workshop;
        }

        // Manipulate the booking data
        $manipulatedData = [];
        foreach ($bookings['data'] as $booking) {
            $workshopCode = $booking['booking']['workshop']['code'];
            $workshop = $workshopLookup[$workshopCode] ?? [
                'address' => '',
                'phone_number' => '',
                'distance' => 0
            ];

            $manipulatedData[] = [
                'name' => $booking['name'],
                'email' => $booking['email'],
                'booking_number' => $booking['booking']['booking_number'],
                'book_date' => $booking['booking']['book_date'],
                'ahass_code' => $workshopCode,
                'ahass_name' => $booking['booking']['workshop']['name'],
                'ahass_address' => $workshop['address'],
                'ahass_contact' => $workshop['phone_number'],
                'ahass_distance' => $workshop['distance'],
                'motorcycle_ut_code' => $booking['booking']['motorcycle']['ut_code'],
                'motorcycle' => $booking['booking']['motorcycle']['name']
            ];
        }

        // Sort data by ahass_distance
        usort($manipulatedData, function ($a, $b) {
            return $a['ahass_distance'] <=> $b['ahass_distance'];
        });

        // Prepare the final response
        $response = [
            'status' => 1,
            'message' => 'Data Successfully Retrieved.',
            'data' => $manipulatedData
        ];

        return response()->json($response);
    }
}

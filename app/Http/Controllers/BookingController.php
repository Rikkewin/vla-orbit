<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use File;
use App\Booking;
use App\BookingDocument;
use App\SentSms;
use App\ServiceProvider;

class BookingController extends Controller
{
    
    public function __construct()
    {       
        $this->middleware('auth');
    }
    
    public function index()
    {       
        $booking_obj = new Booking(); 
        return view("booking.index");
    }

    public function show( $bk_id )
    {
        $service_providers_obj  = new ServiceProvider();
        $service_providers      = $service_providers_obj->getAllServiceProviders();

        return view( "booking.show", compact( 'service_providers' ) );
    }
    
    public function create()
    {
        $service_providers_obj  = new ServiceProvider();
        $service_providers      = $service_providers_obj->getAllServiceProviders();

        return view("booking.create", compact( 'service_providers' ) );
    }

    public function destroy( $bo_id )
    {
        $booking_obj = new Booking();
        $response = $booking_obj->deleteBooking( $bo_id );

        return redirect('/booking')->with($response['success'], $response['message']);        
    }

    public function getServiceDatesByDate( $year, $month, $sv_id )
    {
        $init_year = $finish_year = $year;
        $finish_month = $month + 1;
        $init_date   = $init_year . "-" . $month . "-01";
        
        if( $month > 11 )
        {
            $finish_year += 1;
            $finish_month = "01";
        }
        $finish_date = $finish_year . "-" . $finish_month . "-01";

        $booking_obj = new Booking(); 
        return $booking_obj->getBookableServiesByDayWithTime( $sv_id, $init_date, $finish_date);
    }

    public function store()
    {           
        $service_name = request('ServiceName');
        $client_details = request('client');
        $client_details['ClientEmail']  = ( !isset( $client_details['ClientEmail'] ) || is_null( $client_details['ClientEmail'] ) ? '' : $client_details['ClientEmail'] );
        $client_details['Mobile']       = ( !isset( $client_details['Mobile'] ) || is_null( $client_details['Mobile'] ) ? '' : $client_details['Mobile'] );
        $service_time = explode( 'T', request('serviceTime') );
        $serviceId = explode( '-', request('ServiceId') );
        $booking = [
                        'Date' => $service_time[0],
                        'Time' => $service_time[1],
                        'ServiceId' => (is_null( request('Language') ) ? $serviceId[0] : $serviceId[1] ),
                        'Desc'      => (is_null( request('Desc') ) ? '' : request('Desc') ),
                        'Language'  => (is_null( request('Language') ) ? '' : request('Language') ),
                        'Safe'      => (is_null( request('Safe') ) ? 'true' : request('Safe') ),
                        'CIRNumber' => (is_null( request('CIRNumber') ) ? '' : request('CIRNumber') ),
                    ];
        

        $sp_id = request('service_provider_id');
        $service_providers_obj   = new ServiceProvider();
        $service_provider_result = $service_providers_obj->getServiceProviderByID( $sp_id );
        $service_provider = json_decode( $service_provider_result['data'] )[0];

        $booking_obj = new Booking(); 
        $reservation = $booking_obj->createBooking( $client_details, $booking, $service_provider, $service_name );        

        $reservation_details = json_decode( $reservation['reservation'] );

        //Upload attached files
        $files = request('files');
        if( !empty( $files ) )
        {
            $booking_document = new BookingDocument();
            foreach ($files as $file) 
            {                
                $fileName = $file->getClientOriginalName();
                $file->move( public_path('booking_docs') . '/' . $reservation_details->id , $fileName );
                //Get booking refer from clients name 
                $clientBokingRefNo = explode(' ',  $reservation_details->client_name );
                $booking_document->saveBookingDocument( $fileName , $clientBokingRefNo [0] );
            }
        }     

        return redirect('/booking')->with('success', 'Booking saved.');
    }

    public function list()
    {
        $booking_obj = new Booking(); 
        return $booking_obj->getAllBookingsPerMonth("2017-07-01", "2017-07-31") ;
    }

    public function listCalendar()
    {
        $booking_obj = new Booking(); 
        return $booking_obj->getAllBookingsPerMonthCalendar( request('start'), request('end') ) ;
    }

    public function listCalendarByUser()
    {
        $booking_obj = new Booking(); 
        $bookings = $booking_obj->getBookingsByUser() ;
        return array( 'data' => $bookings );
    }

    public function calendar()
    {
        return view("booking.calendar");
    }

    public function updateBooking( $booking_ref, $date_time )
    {
        $booking_obj = new Booking(); 
        $result = $booking_obj->updateBooking( $booking_ref, $date_time ) ;

        return $result;
    }

    public function updateBookingDetails()
    {
        $booking = request('booking');

        $booking_obj = new Booking(); 
        $result = $booking_obj->updateBookingDetails( json_decode($booking) ) ;

        return $result;
    }

    public function sendSmsReminder()
    {
        $reminder = request('reminder');
        $booking = json_decode( json_encode( request('booking') ), FALSE );
        $sent_sms_obj = new SentSms();
        $result = $sent_sms_obj->sendReminder( $booking );
        return $result;
    }
}

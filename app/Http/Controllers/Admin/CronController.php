<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Airport;
use Illuminate\Http\Request;
use App\UserHotelLoyaltyPrograms;
use App\UserAirlineLoyaltyPrograms;
use App\UserCalendarData;
use App\NotificationStatus;
use Illuminate\Support\Facades\Auth;
use DB;
use Yajra\Datatables\Datatables;

class CronController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        // $this->middleware('auth');
        //  $this->middleware('isuser');

       ini_set('max_execution_time', 5000);
       ini_set('memory_limit', '512M');
        
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
       
        $phparra = $this->userinfo->getAllAirports();
        
      

        if (count((array) $phparra)) {


            // $phparra = \GuzzleHttp\json_decode($jsonObj);

            $airport_array = array();

            $existing_data = Airport::all();

            if ($existing_data->isEmpty()) {

                $this->freshEntryBatch($phparra);
            } else {
            Airport::truncate();
                $this->freshEntryBatch($phparra);
            }
        }
    }

    public function freshEntryBatch($data) {

        foreach ($data->airports as $index => $airport) {


            $insertData[$index]['ciriumGlobalId'] = $airport->fs;
            $insertData[$index]['iataCode'] = $this->checkResponseData($airport, 'iata');
            $insertData[$index]['icaoCode'] = $this->checkResponseData($airport, 'icao');
            $insertData[$index]['faaCode'] = $this->checkResponseData($airport, 'faa');
            $insertData[$index]['name'] = $this->checkResponseData($airport, 'name');
            $insertData[$index]['city'] = $this->checkResponseData($airport, 'city');
            $insertData[$index]['cityCode'] = $this->checkResponseData($airport, 'cityCode');
            $insertData[$index]['postalCode'] = $this->checkResponseData($airport, 'postalCode');
            $insertData[$index]['stateCode'] = $this->checkResponseData($airport, 'stateCode');
            $insertData[$index]['countryCode'] = $this->checkResponseData($airport, 'countryCode');
            $insertData[$index]['countryName'] = $this->checkResponseData($airport, 'countryName');
            $insertData[$index]['regionName'] = $this->checkResponseData($airport, 'regionName');
            $insertData[$index]['timeZoneName'] = $this->checkResponseData($airport, 'timeZoneRegionName');
            $insertData[$index]['weatherZone'] = $this->checkResponseData($airport, 'weatherZone');
            $insertData[$index]['localTime'] = $this->checkResponseData($airport, 'localTime');
            $insertData[$index]['utcOffset'] = $this->checkResponseData($airport, 'utcOffsetHours');
            $insertData[$index]['elevationFeet'] = $this->checkResponseData($airport, 'elevationFeet');
            $insertData[$index]['latitude'] = $this->checkResponseData($airport, 'latitude');
            $insertData[$index]['longitude'] = $this->checkResponseData($airport, 'longitude');
            $insertData[$index]['classification'] = $airport->classification;
            $insertData[$index]['active'] = $this->checkResponseData($airport, 'active');
        }
        
        $data_insert_chunk = array_chunk($insertData, 1000); 

       foreach($data_insert_chunk as $index=>$chunk_insert){
                   Airport::insert($chunk_insert);
                  // echo $index; 

       }
        
       // \DB::connection()->disableQueryLog();
       
    }

    public function checkResponseData($data, $key) {

        if (isset($data->$key)) {
            return $data->$key;
        } else {
            return '';
        }
    }

    public function freshEntry($airport) {

        //  print_r($airport); exit;
        $insertData['ciriumGlobalId'] = $airport->fs;

        if (isset($airport->iata)) {
            $insertData['iataCode'] = $airport->iata;
        } else {
            $insertData['iataCode'] = '';
        }


        if (isset($airport->icao)) {
            $insertData['icaoCode'] = $airport->icao;
        } else {
            $insertData['icaoCode'] = '';
        }

        if (isset($airport->faa)) {
            $insertData['faaCode'] = $airport->faa;
        } else {
            $insertData['faaCode'] = '';
        }

        $insertData['name'] = $airport->name;
        if (isset($airport->street1)) {
            $insertData['street'] = $airport->street1;
        } else {
            $insertData['street'] = '';
        }

        $insertData['city'] = $airport->city;

        if (isset($airport->cityCode)) {
            $insertData['cityCode'] = $airport->cityCode;
        } else {
            $insertData['cityCode'] = '';
        }
        if (isset($airport->postalCode)) {
            $insertData['postalCode'] = $airport->postalCode;
        } else {
            $insertData['postalCode'] = '';
        }

        if (isset($airport->stateCode)) {
            $insertData['stateCode'] = $airport->stateCode;
        } else {
            $insertData['stateCode'] = '';
        }
        $insertData['countryCode'] = $airport->countryCode;
        $insertData['countryName'] = $airport->countryName;
        $insertData['regionName'] = $airport->regionName;
        $insertData['timeZoneName'] = $airport->timeZoneRegionName;
        if (isset($airport->weatherZone)) {
            $insertData['weatherZone'] = $airport->weatherZone;
        } else {
            $insertData['weatherZone'] = '';
        }
        $insertData['localTime'] = $airport->localTime;
        $insertData['utcOffset'] = $airport->utcOffsetHours;
        $insertData['elevationFeet'] = $airport->elevationFeet;
        $insertData['latitude'] = $airport->latitude;
        $insertData['longitude'] = $airport->longitude;
        $insertData['classification'] = $airport->classification;
        $insertData['classification'] = $airport->classification;
        $insertData['active'] = $airport->active;

        Airport::insert($insertData);
    }

    public function updateEntry($airport, $key_value) {


        //$insertData['ciriumGlobalId'] = $airport->fs;

        if (isset($airport->iata)) {
            $updateData['iataCode'] = $airport->iata;
        } else {
            $updateData['iataCode'] = '';
        }

        if (isset($airport->icao)) {
            $updateData['icaoCode'] = $airport->icao;
        } else {
            $updateData['icaoCode'] = '';
        }

        if (isset($airport->faa)) {
            $updateData['faaCode'] = $airport->faa;
        } else {
            $updateData['faaCode'] = '';
        }

        $updateData['name'] = $airport->name;
        if (isset($airport->street1)) {
            $updateData['street'] = $airport->street1;
        } else {
            $updateData['street'] = '';
        }

        $updateData['city'] = $airport->city;

        if (isset($airport->cityCode)) {
            $updateData['cityCode'] = $airport->cityCode;
        } else {
            $updateData['cityCode'] = '';
        }
        if (isset($airport->postalCode)) {
            $updateData['postalCode'] = $airport->postalCode;
        } else {
            $updateData['postalCode'] = '';
        }

        if (isset($airport->postalCode)) {
            $updateData['stateCode'] = $airport->stateCode;
        } else {
            $updateData['stateCode'] = '';
        }

        //   $updateData['stateCode'] = $airport->stateCode;
        $updateData['countryCode'] = $airport->countryCode;
        $updateData['countryName'] = $airport->countryName;
        $updateData['regionName'] = $airport->regionName;
        $updateData['timeZoneName'] = $airport->timeZoneRegionName;

        if (isset($airport->weatherZone)) {
            $updateData['weatherZone'] = $airport->weatherZone;
        } else {
            $updateData['weatherZone'] = '';
        }
        $updateData['localTime'] = $airport->localTime;
        $updateData['utcOffset'] = $airport->utcOffsetHours;
        $updateData['elevationFeet'] = $airport->elevationFeet;
        $updateData['latitude'] = $airport->latitude;
        $updateData['longitude'] = $airport->longitude;
        $updateData['classification'] = $airport->classification;
        $updateData['classification'] = $airport->classification;
        $updateData['active'] = $airport->active;

        //   Airport::insert($insertData);

        Airport::where('ciriumGlobalId', '=', $key_value)->update($updateData);
    }

}

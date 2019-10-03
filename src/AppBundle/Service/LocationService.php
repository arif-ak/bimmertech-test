<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\Request;

class LocationService
{
    public function location(Request $request)
    {
        $ip = $request->getClientIp();

        if ($ip == "127.0.0.1") {
            $ip = "194.44.192.201";
        }

        $result = [
            'country_code' => "",
            'longitude' => "",
            'latitude' => ""
        ];

        // get country from IP
        try {
            $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            if (count($ip_data) > 0 && isset($ip_data->geoplugin_countryName)) {
                $result['country_code'] = $ip_data->geoplugin_countryCode;
                $result['latitude'] = $ip_data->geoplugin_latitude;
                $result['longitude'] = $ip_data->geoplugin_longitude;
            }

            return $result;
        } catch (\Exception $exception) {
            try {
                $geoLocation = file_get_contents("https://www.iplocate.io/api/lookup/$ip");
                $decodeGeoLocation = json_decode($geoLocation, true);

                if (isset($decodeGeoLocation['country_code']) && isset($decodeGeoLocation['longitude'])) {
                    $result['country_code'] =
                        isset($decodeGeoLocation['country_code']) ? $decodeGeoLocation['country_code'] : null;
                    $result['longitude'] =
                        isset($decodeGeoLocation['longitude']) ? $decodeGeoLocation['longitude'] : null;
                    $result['latitude'] =
                        isset($decodeGeoLocation['latitude']) ? $decodeGeoLocation['latitude'] : null;

                    return $result;
                }
            } catch (\Exception $exception) {
            } finally {
                return $result;
            }
        }
    }
}

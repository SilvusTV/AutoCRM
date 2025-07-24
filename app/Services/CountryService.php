<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Log;

class CountryService
{
    /**
     * Get a list of countries in French from the French government API.
     */
    public static function getCountries(): array
    {
        // Cache the countries for 24 hours to avoid making too many API calls
        //        return Cache::remember('countries_list', 84400, function () {
        try {
            $urlBase = 'https://data.enseignementsup-recherche.gouv.fr/api/explore/v2.1/catalog/datasets/';
            // Using the French government's API for countries
            $response = Http::get($urlBase.'curiexplore-pays/records?select=name_fr%2C%20iso3&order_by=name_fr&limit=100');
            $response2 = Http::get($urlBase.'curiexplore-pays/records?select=name_fr%2C%20iso3&order_by=name_fr&limit=100&offset=100');
            $response3 = Http::get($urlBase.'curiexplore-pays/records?select=name_fr%2C%20iso3&order_by=name_fr&limit=100&offset=200');
            if ($response->successful() && $response2->successful() && $response3->successful()) {
                $countries = array_merge(
                    $response->json()['results'],
                    $response2->json()['results'],
                    $response3->json()['results']
                );

                // Return an array with country names as keys and values
                $formattedCountries = [];
                foreach ($countries as $country) {
                    $formattedCountries[$country['iso3']] = $country['name_fr'];
                }

                return $formattedCountries;
            }
        } catch (Exception $e) {
            // Log the error
            Log::error('Error fetching countries: '.$e->getMessage());
        }

        // Fallback to a basic list of countries if the API call fails
        return [
            'FRA' => 'France',
            'BEL' => 'Belgique',
            'CHE' => 'Suisse',
            'LUX' => 'Luxembourg',
            'DEU' => 'Allemagne',
            'ITA' => 'Italie',
            'ESP' => 'Espagne',
            'GBR' => 'Royaume-Uni',
            'USA' => 'États-Unis',
            'CAN' => 'Canada',
        ];
        //        });
    }
}

<?php

/** @var User $record */

use App\Models\User\User;

$record = $getRecord();
?>
@if($record->longitude)
    <div class="w-full">
        <script src="https://api-maps.yandex.ru/2.1/?lang=en_RU&amp;apikey={{env('YANDEX_API_KEY')}}"
                type="text/javascript"></script>

        <script>
            ymaps.ready(init);

            function init() {
                const location = [{{$record->longitude}}, {{$record->latitude}}];
                const map = new ymaps.Map("map", {
                    center: location,
                    zoom: 7
                });

                const myGeoObject = new ymaps.GeoObject({
                    geometry: {
                        type: "Point",
                        coordinates: location
                    }
                })

                map.geoObjects
                    .add(myGeoObject)

            }
        </script>

        <div id="map" style=" height: 400px"></div>
    </div>
@endif

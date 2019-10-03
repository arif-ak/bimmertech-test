var installer = new Vue({
    el: '#installer',

    data: {
        installers: [],
        query: '',
        activeFilters: [],
        isInstallerOnLocation: true,
        map: {},
        showRemoveButton: false,
        activeMarkers: [],
        allMarkers: [],
        markers: [],
        marker: null,
        distances: [],
        autocomplete: {},
        autocompletePlace: {},
        geocoder: {},
        input: "",
        myLatlng: {
            lat: 38.83652654113248,
            lng: -96.26320850084204
        },
        zoom: 6
    },

    mounted: function () {
        var that = this;
        var map, marker;
        const mapElement = document.getElementById('map');
        const myLatlng = {
            lat: 38.83652654113248,
            lng: -96.26320850084204
        };
        that.input = document.getElementById('pac-input');
        var styledMapType = this.mapStyle();
        const option = {
            zoom: 4,
            center: myLatlng,
            fullscreenControl: false,
            // clickableIcons: false,
            mapTypeControlOptions: {
                mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain',
                    'styled_map'
                ]
            }
        };

        that.map = new google.maps.Map(mapElement, option);
        that.map.mapTypes.set('styled_map', styledMapType);
        that.map.setMapTypeId('styled_map');
        that.geocoder = new google.maps.Geocoder();
        that.autocomplete = new google.maps.places.SearchBox(that.input);

        that.autocomplete.addListener('places_changed', function () {
            that.autocompletePlace = null;
            var autocompleteValue = that.autocomplete.getPlaces();
            that.autocompletePlace = autocompleteValue[0];

            that.query = that.autocompletePlace.formatted_address;

            if (that.autocompletePlace) {
                if (that.autocompletePlace.geometry.viewport) {
                    that.showRemoveButton = true;
                    that.map.fitBounds(that.autocompletePlace.geometry.viewport);
                    var _coordinates = that.autocompletePlace.geometry.location; //a google.maps.LatLng object
                    that.installerActivateStyle(null, true);
                    that.findClosestMarker(_coordinates)
                }
            } else {
                that.map.setCenter(place.geometry.location);
                that.map.setZoom(17); // Why 17? Because it looks good.
            }
        });

        this.loadCoordinates(that.map);
        this.loadLocation();
    },

    methods: {
        showMarker(i) {
            var that = this;
            that.map.setCenter({
                lat: i.latitude,
                lng: i.longitude
            });
            that.map.setZoom(17);
            that.installerActivateStyle(i);
        },

        filtered: function () {
            var that = this;
            that.installerActivateStyle(null, true);
            if (!that.query) {
                that.showRemoveButton = false;
                that.activeFilters = that.installers;
                that.clearMarkers();
                that.markers = that.installers;
                that.addMarkers(that.map, that.markers)
            } else {
                that.showRemoveButton = true;
                this.geocodeAddress();
            }
        },

        removeQuery: function () {
            var that = this;
            that.query = "";
            that.installerActivateStyle(null, true);
            that.showRemoveButton = false;
            that.isInstallerOnLocation = true;
            that.activeFilters = that.installers;
            that.clearMarkers();
            that.markers = that.installers;
            that.addMarkers(that.map, that.markers);
            if (that.marker) {
                that.marker.setMap(null);
                that.marker = null;
            }


            // change position
            that.map.setCenter(that.myLatlng);
            that.map.setZoom(that.zoom);
        },

        lowerCase: function (string) {
            if (!string) {
                return "";
            }
            string = string.toLowerCase();
            return string;
        },

        loadCoordinates: function (map) {
            var that = this;
            this.$http.get('/api2/installer').then(response => {
                that.installers = response.data;
                that.activeFilters = response.data;

                this.addMarkers(map, response.data);
                this.getAllMarkers(map, response.data);
            }).catch(error => {
                // this.reviewsLoader = false;
            }).finally(() => {
                $(".filter.twig").remove();
                //this.checkLoadMoreBtn(this.$data.productList.length);
            });
        },

        loadLocation: function () {
            var that = this;
            this.$http.get('/api2/installer-location').then(response => {
                var data = response.data.data;
                if (data.latitude && data.longitude) {
                    that.myLatlng.lat = parseFloat(data.latitude);
                    that.myLatlng.lng = parseFloat(data.longitude);
                    console.log(that.myLatlng);
                    // change position
                    that.map.setCenter(that.myLatlng);
                    that.map.setZoom(that.zoom);
                }
            }).catch(error => {}).finally(() => {

            });
        },
        addMarkers: function (map, markers) {
            var that = this;
            var markersArray = [];
            $.each(markers, function (key, coordinate) {
                var image;
                image = that.findIcon(coordinate.type);

                var marker = new google.maps.Marker({
                    id: coordinate.id,
                    position: {
                        lat: coordinate.latitude,
                        lng: coordinate.longitude
                    },
                    icon: image,
                    title: coordinate.name,
                    cursor: 'pointer',
                });

                marker.setMap(map);
                marker.addListener('click', function (e) {
                    Object.keys(e).forEach(key => {
                        if (e[key] instanceof Event) {
                            $(e[key].target)
                                .btpopup({
                                    className: {
                                        loading: 'loading',
                                        btpopup: 'cui btpopup ' + coordinate.type,
                                        position: 'top left center bottom right',
                                        visible: 'visible'
                                    },
                                    html: pHtml({
                                        title: coordinate.name,
                                        content: coordinate.address,
                                        link: coordinate.link
                                    }),
                                    position: 'top center',
                                    on: 'click',
                                    distanceAway: 5,
                                }).btpopup('toggle');
                        }
                    });
                    map.setZoom(17);
                    //map.setCenter(marker.getPosition());
                    that.installerActivateStyle(marker);
                });
                markersArray.push(marker);
            });
            that.markers = markersArray;
        },

        getAllMarkers: function (map, markers) {
            var that = this;
            var markersArray = [];
            $.each(markers, function (key, coordinate) {
                var image;
                image = that.findIcon(coordinate.type);

                var marker = new google.maps.Marker({
                    position: {
                        lat: coordinate.latitude,
                        lng: coordinate.longitude
                    },
                    icon: image,
                    title: coordinate.name,
                });
                markersArray.push(marker);

            });

            that.allMarkers = markersArray;
        },

        deleteMarkers: function () {
            this.clearMarkers();
            this.markers = [];
        },

        clearMarkers: function () {
            var that = this;
            for (var i = 0; i < that.markers.length; i++) {
                that.markers[i].setMap(null);
            }
        },

        findIcon: function (type) {
            switch (type) {
                case "platinum":
                    return '/assets/shop/img/svg/black_icon.svg';
                case "gold":
                    return '/assets/shop/img/svg/gold_icon.svg';
                case "silver":
                    return '/assets/shop/img/svg/silver_icon.svg';
                case "bronze":
                    return '/assets/shop/img/svg/bronze_icon.svg';
                default:
                    return "";
            }
        },

        findClosestMarker: function (coordinates, parse = false) {
            if (parse) {
                coordinates = {}
            }

            var that = this;
            var distances = [];
            var closest = -1;

            if (that.marker) {
                that.marker.setMap(null);
                that.marker = null;
            }

            that.marker = new google.maps.Marker({
                map: that.map,
                title: that.query,
                position: coordinates
            });

            var markersWithDistance = [];
            for (i = 0; i < this.allMarkers.length; i++) {
                var d = google.maps.geometry.spherical.computeDistanceBetween(this.allMarkers[i].position, coordinates);
                distances[i] = d;
                this.allMarkers[i].distance = d;
                this.installers[i].distance = d;
                markersWithDistance.push(this.installers[i]);

                if (closest == -1 || d < distances[closest]) {
                    closest = i;
                }
            }

            markersWithDistance.sort(function (a, b) {
                var comparison = 0;

                if (a.distance > b.distance) {
                    comparison = 1;
                } else if (b.distance > a.distance) {
                    comparison = -1;
                }

                return comparison;
            });

            var threeNearestMarkers = [];
            for (j = 0; j < markersWithDistance.length && j < 3; j++) {
                if (markersWithDistance[j].distance < 300000) {
                    threeNearestMarkers.push(markersWithDistance[j]);
                }
            }
            console.log(threeNearestMarkers);

            this.activeFilters = threeNearestMarkers;
            this.deleteMarkers();
            this.addMarkers(that.map, threeNearestMarkers);
            if (threeNearestMarkers.length > 0) {
                that.isInstallerOnLocation = true
            } else {
                that.isInstallerOnLocation = false
            }
        },

        geocodeAddress: function () {
            var that = this;
            var address = document.getElementById('pac-input').value;
            this.geocoder.geocode({
                'address': address
            }, function (results, status) {
                if (status === 'OK') {
                    that.map.fitBounds(results[0].geometry.viewport);
                    that.query = results[0].formatted_address;
                    that.findClosestMarker(results[0].geometry.location);
                } else {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
            });
        },

        installerActivateStyle: function (marker = null, setToFalse = false) {
            var that = this;

            $.each(that.activeFilters, function (key, value) {
                that.activeFilters[key].active = false;
                if (!setToFalse) {
                    if (marker.id == value.id) {
                        that.activeFilters[key].active = true;

                        var childEl = document.getElementById(marker.id);
                        var childPix = childEl.offsetTop;

                        var parentElement = document.getElementById("filters_container");
                        var parentPix = parentElement.offsetTop;

                        var differenceBetweenPixels = childPix - parentPix;

                        $('.filters_container').animate({
                            scrollTop: differenceBetweenPixels
                        }, 1500);
                    }
                }
            });
        },

        mapStyle: function () {
            var styledMapType = new google.maps.StyledMapType(
                [{
                        "elementType": "geometry",
                        "stylers": [{
                            "color": "#f5f5f5"
                        }]
                    },
                    {
                        "elementType": "labels.icon",
                        "stylers": [{
                            "visibility": "off"
                        }]
                    },
                    {
                        "elementType": "labels.text.fill",
                        "stylers": [{
                            "color": "#616161"
                        }]
                    },
                    {
                        "elementType": "labels.text.stroke",
                        "stylers": [{
                            "color": "#f5f5f5"
                        }]
                    },
                    {
                        "featureType": "administrative.land_parcel",
                        "elementType": "labels.text.fill",
                        "stylers": [{
                            "color": "#bdbdbd"
                        }]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "geometry",
                        "stylers": [{
                            "color": "#eeeeee"
                        }]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "labels.text.fill",
                        "stylers": [{
                            "color": "#757575"
                        }]
                    },
                    {
                        "featureType": "poi.park",
                        "elementType": "geometry",
                        "stylers": [{
                            "color": "#e5e5e5"
                        }]
                    },
                    {
                        "featureType": "poi.park",
                        "elementType": "labels.text.fill",
                        "stylers": [{
                            "color": "#9e9e9e"
                        }]
                    },
                    {
                        "featureType": "road",
                        "elementType": "geometry",
                        "stylers": [{
                            "color": "#ffffff"
                        }]
                    },
                    {
                        "featureType": "road.arterial",
                        "elementType": "labels.text.fill",
                        "stylers": [{
                            "color": "#757575"
                        }]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "geometry",
                        "stylers": [{
                            "color": "#dadada"
                        }]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "labels.text.fill",
                        "stylers": [{
                            "color": "#616161"
                        }]
                    },
                    {
                        "featureType": "road.local",
                        "elementType": "labels.text.fill",
                        "stylers": [{
                            "color": "#9e9e9e"
                        }]
                    },
                    {
                        "featureType": "transit.line",
                        "elementType": "geometry",
                        "stylers": [{
                            "color": "#e5e5e5"
                        }]
                    },
                    {
                        "featureType": "transit.station",
                        "elementType": "geometry",
                        "stylers": [{
                            "color": "#eeeeee"
                        }]
                    },
                    {
                        "featureType": "water",
                        "elementType": "geometry",
                        "stylers": [{
                            "color": "#c9c9c9"
                        }]
                    },
                    {
                        "featureType": "water",
                        "elementType": "labels.text.fill",
                        "stylers": [{
                            "color": "#9e9e9e"
                        }]
                    }
                ], {
                    name: 'Styled Map'
                });

            return styledMapType;
        }
    },

    filters: {
        replaceNRToBR: function (value) {
            if (!value) return '';

            value = value.replace(/\n/g, '<br>');
            return value
        }
    }
});


//
// roadmap displays the default road map view. This is the default map type.
//     satellite displays Google Earth satellite images.
//     hybrid displays a mixture of normal and satellite views.
//     terrain displays a physical map based on terrain information.
//
// var _street = $('#address').val();
// var place = autocomplete.getPlace();
// google.maps.event.trigger(that.input, 'focus');
// var _coordinates = place.geometry.location; //a google.maps.LatLng object
// var _kCord = new google.maps.LatLng(-36.874694, 174.735292);
// var _pCord = new google.maps.LatLng(-36.858317, 174.782284);
//
// console.log(_coordinates);
//
// console.log(google.maps.geometry.spherical.computeDistanceBetween(_pCord, _coordinates));
// console.log(google.maps.geometry.spherical.computeDistanceBetween(_kCord, _coordinates))
// console.log(that.input.value);
//
// google.maps.event.trigger(that.input, 'keydown', {keyCode: 13});
// google.maps.event.trigger(that.autocomplete, 'places_changed');
//
// console.log(that.input.value);
//
// return false;


// var autocompleteValue = that.autocomplete.getPlaces();
// that.autocompletePlace = autocompleteValue[0];
// console.log(that.autocompletePlace);
// if (that.autocompletePlace) {
//     if (that.autocompletePlace.geometry.viewport) {
//         that.map.fitBounds(that.autocompletePlace.geometry.viewport);
//         var _coordinates = that.autocompletePlace.geometry.location; //a google.maps.LatLng object
//         that.findClosestMarker(_coordinates)
//     }
// } else {
//     that.map.setCenter(place.geometry.location);
//     that.map.setZoom(17);  // Why 17? Because it looks good.
// }

// var filteredResults = [];
// var markersArray = [];
// that.clearMarkers();
// that.addMarkers(that.map, that.markers);
// $.each(that.installers, function (key, value) {
//     if ((that.lowerCase(value.name).indexOf(that.lowerCase(that.query)) !== -1) ||
//         (that.lowerCase(value.address).indexOf(that.lowerCase(that.query)) !== -1)) {
//         filteredResults.push(value);
//         markersArray.push(value);
//     }
// });
//
// that.activeFilters = filteredResults;
// that.markers = markersArray;
// that.addMarkers(that.map, that.markers)
// that.autocompletePlace = that.autocomplete.getPlace();
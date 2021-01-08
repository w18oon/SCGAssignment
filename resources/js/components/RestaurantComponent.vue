<template>
    <div class="container-fluid full-height">
        <div class="row justify-content-center full-height">
            <div class="col-4 full-height">
                <div class="input-group mt-3 mb-3">
                    <input type="text" class="form-control" placeholder="Enter location for search restaurant." v-model="keyword">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" @click.prevent="getRestaurant">Search</button>
                    </div>
                </div>
                <div class="list-group" style="height: 90%; overflow: auto;">
                    <div class="list-group-item list-group-item-action flex-column" v-for="restaurant in restaurants" :key="restaurant.name">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{ restaurant.name }}</h5>
                        </div>
                        <p class="mb-1">Address: {{ restaurant.addr }}</p>
                    </div>
                </div>
            </div>
            <div class="col-8">
                <div id="map" class="full-height"></div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                map: '',
                markers: [],
                positopnCenter: '',
                keyword: 'Bangsue',
                restaurants: {},
            }
        },
        methods: {
            // init google map
            initMap() {
                this.map = new google.maps.Map(document.getElementById("map"), {
                    // set senter to bangsue location
                    center: { "lat": 13.827570434859394, "lng": 100.52571566288754 },
                    zoom: 14,
                });
            },

            // add marker to map
            addMarker() {
                for (let i = 0; i < this.restaurants.length; i++) {
                    // declare info window for popup
                    const contentString = '<div>' + this.restaurants[i].name + '</div>'

                    const infowindow = new google.maps.InfoWindow({
                        content: contentString,
                    })

                    const marker = new google.maps.Marker({
                        position: this.restaurants[i].location,
                        map: this.map,
                        title: this.restaurants[i].name,
                    })

                    // add listerner for open & close info windows
                    marker.addListener("mouseover", () => {
                        infowindow.open(this.map, marker);
                    })

                    marker.addListener("mouseout", () => {
                        infowindow.close(this.map, marker);
                    })

                    // store to array for delete next search
                    this.markers.push(marker);
                }
            },

            // delete marker from map
            deleteMarker() {
                for (let i = 0; i < this.markers.length; i++) {
                    markers[i].setMap(null)
                }
            },

            // call api with parameter and get result to restaurants
            getRestaurant() {
                axios.get('/api/restaurant', {
                    params: {
                        location: this.keyword
                    }
                }).then(response => {
                    // get result from api
                    this.restaurants = response.data.results
                    // render map with first location from result
                    this.map.setCenter(this.restaurants[0].location)
                    // add marker from results
                    this.addMarker()
                }).catch(error => {
                    console.log(error)
                })
            }
        },
        mounted() {
            // when instance has been mounted
            this.initMap()
            this.getRestaurant()
        }
    }
</script>

<template>
    <span>
        <div class="back map">
			<font-awesome-icon icon="reply" @click="$router.go(-1)"></font-awesome-icon>
		</div> 
        <div id="map" class="map">
    		<b-btn variant="link" v-b-tooltip.hover id="popup"></b-btn>
            <b-tooltip ref="tooltip" target="popup">
                <span v-html="city_name"></span>
            </b-tooltip>
            <div id="click_info" v-html="city_name_click" :class="twoLine(city_name_click)"></div>
            <!--  -->
            <transition name="carousel">
                <div id="pic_carousel" v-show="click_pin">
        			<button id="pic_list_close_btn" @click="closeModal">&times;</button>
        			<div id="pic_list_wrapper" @click="showInfo">
                        <b-carousel id="carousel" controls indicators :interval="3000">
                            <b-carousel-slide v-for="(urlArray,index) in picture_url" :key="index" :img-src="urlArray.url"></b-carousel-slide>
                        </b-carousel>
        			</div>
        			<div class="pic_info" v-show="!checkIsMobile">
        				<div id="pic_info_title" class="font_Nunito" v-html="click_location"></div>
        				<div class="font_RoundFont pic_info_content_wrapper">
        					<div id="pic_info_content" v-html="handleInfo(click_info)"></div>
        					<div id="travel_time">{{travel_time}}</div>
        				</div>
        			</div>
                    <div id="mobile_diary_modal" class="mobile_diary_modal" v-show="showModal">
                        <div id="mobile_diary_modal_btn" class="close_btn" @click="closeModalInfo">&times;</div>
                        <div id="mobile_diary_text" class="font_RoundFont" v-html="handleInfo(click_info)"></div>
                        <div id="mobile_diary_time" class="font_RoundFont">{{travel_time}}</div>
                    </div>
        		</div>
            </transition>
    	</div>
    </span>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import axios from 'axios';

export default {

    data() {
        return {
            map: [],
            select: [],
            click_overlay: [],
            //
            click_pin: false,
            city_name: '',
            city_name_click: '',
            closeResolution: 2900,
            //
            click_location: '',
            click_info: '',
            travel_time: '',
            picture_url: [],
            showModal: false,
        }
    },
    mounted() {
        let _this = this;
        _this.$store.dispatch('isMobile');
        let zoom;
        let padding_right;
        let padding_top;
        let closeResolution;
        if (_this.checkIsMobile) {
            // console.log('mobile');
            zoom = 3;
            padding_right = 0;
            padding_top = -300;
            closeResolution = 10000;
            _this.closeResolution = closeResolution;
        } else {
            // console.log('desktop');
            zoom = 4.7;
            padding_right = 600;
            padding_top = 0;
            closeResolution = _this.closeResolution;
        }

        //remove openlayer logo
        this.$nextTick(() => {
            document.querySelectorAll('.ol-overlaycontainer-stopevent').forEach(function (a) {
                a.remove()
            })
        });

        let styles = {
            'pin_red': new ol.style.Style({
                image: new ol.style.Icon({
                    anchor: [0.5, 1],
                    src: '/images/location_pin_red_sm.png'
                })
            }),
            'pin_grey': new ol.style.Style({
                image: new ol.style.Icon({
                    anchor: [0.5, 1],
                    src: '/images/location_pin_grey_sm.png'
                })
            }),
            'route': new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: '#CB4F40',
                    width: 2
                })
            }),
            'state': new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: '#000',
                    lineDash: [2, 10],
                    width: 2,
                })
            })
        };

        //location data
        let locationSource = new ol.source.Vector();
        axios({
            url: `/data/location.json`,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            }
        }).then(function (response) {
            _this.LocationHandler(response.data, locationSource);
        })

        let locationLayer = new ol.layer.Vector({
            source: locationSource,
            style: styles['pin_grey'],
            name: "location"
        });

        _this.map = new ol.Map({
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.Stamen({
                        layer: 'watercolor',
                    })
                }),
                new ol.layer.Tile({
                    source: new ol.source.Stamen({
                        layer: 'terrain-labels',
                    })
                }),
                new ol.layer.Tile({
                    source: new ol.source.Stamen({
                        layer: 'terrain-lines',
                    })
                }),
                locationLayer,
            ],
            target: 'map',
            loadTilesWhileAnimating: true,
            view: new ol.View({
                center: ol.proj.transform([-94.595002, 38.116592], 'EPSG:4326', 'EPSG:3857'), //[經緯度相反]
                zoom: zoom,
                minZoom: 3,
                maxZoom: 9,
            })
        });
        //pop位置資訊 for hover
        let pin_info = document.getElementById('popup');
        let popup = new ol.Overlay({
            element: pin_info,
            positioning: 'top-center',
            stopEvent: false
        });
        _this.map.addOverlay(popup);
        //pop位置資訊 for click
        let click_info = document.getElementById('click_info');
        _this.click_overlay = new ol.Overlay({
            element: click_info,
            positioning: 'top-center',
            stopEvent: false
        });
        _this.map.addOverlay(_this.click_overlay);

        //hover pin
        let hover = new ol.interaction.Select({
            condition: ol.events.condition.pointerMove,
            style: function (feature) {
                let geometry = feature.getGeometry();
                let coord = geometry.getCoordinates();

                popup.setPosition(coord);
                _this.city_name = feature.get('name');
                return styles['pin_red'];
            },
            filter: function (feature, layer) {
                return layer === locationLayer;
            }
        });

        //click pin
        let click = new ol.interaction.Select({
            condition: ol.events.condition.click,
            style: function (feature) {
                _this.$refs.tooltip.$emit('close');

                let geometry = feature.getGeometry();
                let coord = geometry.getCoordinates();
                _this.city_name_click = feature.get('name');
                _this.click_overlay.setPosition(coord);
                _this.map.addOverlay(_this.click_overlay);

                return styles['pin_red'];
            },
            filter: function (feature, layer) {
                return layer === locationLayer;
            }
        });

        _this.map.addInteraction(hover);
        _this.map.addInteraction(click);

        _this.select = click;
        _this.select.on('select', function (e) {
            _this.showModal = false;//有的地方會自己跳出來，所以在關一次
            let view = _this.map.getView();
            let tmp = e.target.getFeatures().getArray()[0];
            if (!tmp) {//不知道為什麼點完pin後點擊其他地方第一次還會進來
                return false;
            }
            let extent = e.target.getFeatures().getArray()[0].getGeometry().getExtent();
            if (e.target.getFeatures().getArray()[0].get('type') == 'marker') {
                view.fit(extent, {
                    padding: [padding_top, padding_right, 0, 0], //top right bottom left
                    minResolution: 3300,
                    duration: 1000,
                    nearest: true
                });

                _this.click_pin = true;

                //塞入文字和時間
                _this.click_location = e.target.getFeatures().getArray()[0].get('name');
                _this.click_info = e.target.getFeatures().getArray()[0].get('info');
                _this.travel_time = e.target.getFeatures().getArray()[0].get('time');

                switch (_this.click_location) {
                    case 'Horseshoe Bend &</br> Antelope Canyon':
                        _this.click_location = 'Horseshoe Bend';
                        break;
                    case 'Twin Falls & Snake River':
                        _this.click_location = 'Twin Falls';
                        break;
                }
                axios({
                    url: `/php/api/getMapPic.php`,
                    method: 'POST',
                    data: {
                        location: _this.click_location,
                    }
                }).then(function (response) {
                    _this.picture_url = response.data;
                    // console.log(_this.picture_url);
                })
            }
        });
        // leave pin clear info
        _this.map.on('click', function (e) {
            let pixel = _this.map.getEventPixel(e.originalEvent);
            let hit = _this.map.hasFeatureAtPixel(pixel, {
                layerFilter: function (layer) {
                    return layer.get('name') === 'location';
                }
            });
            if (!hit) {
                _this.map.removeOverlay(_this.click_overlay);
            }
        });

        //路線動畫
        let flightsSource;
        let addLater = function (feature, timeout) {
            window.setTimeout(function () {
                feature.set('start', new Date().getTime());
                flightsSource.addFeature(feature);
            }, timeout);
        };

        let pointsPerMs = 0.1;
        let animateFlights = function (event) {
            let vectorContext = event.vectorContext;
            let frameState = event.frameState;
            vectorContext.setStyle(styles['route']);

            let features = flightsSource.getFeatures();
            for (let i = 0; i < features.length; i++) {
                let feature = features[i];
                if (!feature.get('finished')) {
                    // only draw the lines for which the animation has not finished yet
                    let coords = feature.getGeometry().getCoordinates();
                    let elapsedTime = frameState.time - feature.get('start');
                    let elapsedPoints = elapsedTime * pointsPerMs;

                    if (elapsedPoints >= coords.length) {
                        feature.set('finished', true);
                    }

                    let maxIndex = Math.min(elapsedPoints, coords.length);
                    let currentLine = new ol.geom.LineString(coords.slice(0, maxIndex));

                    // directly draw the line with the vector context
                    vectorContext.drawGeometry(currentLine);
                }
            }
            // tell OpenLayers to continue the animation
            _this.map.render();
        };

        flightsSource = new ol.source.Vector({
            loader: function () {
                let url = '/data/flight.json';
                fetch(url).then(function (response) {
                    return response.json();
                }).then(function (json) {
                    let flightsData = json.flights;
                    for (let i = 0; i < flightsData.length; i++) {
                        let flight = flightsData[i];
                        let from = flight.from;
                        let to = flight.to;

                        // create an arc circle between the two locations
                        let arcGenerator = new arc.GreatCircle({
                            x: from[1],
                            y: from[0]
                        }, {
                                x: to[1],
                                y: to[0]
                            });

                        let arcLine = arcGenerator.Arc(100, {
                            offset: 10
                        });
                        if (arcLine.geometries.length === 1) {
                            let line = new ol.geom.LineString(arcLine.geometries[0].coords);
                            line.transform(ol.proj.get('EPSG:4326'), ol.proj.get('EPSG:3857'));

                            let feature = new ol.Feature({
                                geometry: line,
                                finished: false,
                            });
                            // add the feature with a delay so that the animation
                            // for all features does not start at the same time
                            addLater(feature, (i + 2) * 1000);
                        }
                    }
                    _this.map.on('postcompose', animateFlights);
                });
            }
        });

        var flightsLayer = new ol.layer.Vector({
            source: flightsSource,
            style: function (feature) {
                // if the animation is still active for a feature, do not
                // render the feature with the layer style
                if (feature.get('finished')) {
                    return styles['route'];
                } else {
                    return null;
                }
            }
        });
        _this.map.addLayer(flightsLayer);


    },
    methods: {
        ...mapActions([
            'isMobile',
        ]),

        LocationHandler(data, locationSource) {
            let transform = ol.proj.getTransform('EPSG:4326', 'EPSG:3857');
            data.items.forEach(function (item) {
                let feature = new ol.Feature(item);
                let coordinate = transform([parseFloat(item.longitude), parseFloat(item.latitude)]);
                let geometry = new ol.geom.Point(coordinate);
                feature.setGeometry(geometry);
                locationSource.addFeature(feature);
            });
        },
        twoLine(word) {
            if (word.includes('</br>')) {
                return 'two-line';
            }
        },
        closeModal() {
            let _this = this;
            this.click_pin = false;
            this.showModal = false;
            this.map.removeOverlay(this.click_overlay);
            this.select.getFeatures().clear();
            //set center to kansas city
            this.map.getView().fit([-10755260.080893833, 4738386.162999855, -10755260.080893833, 4738386.162999855], {
                minResolution: _this.closeResolution,
                duration: 1000
            });
        },
        handleInfo(info) {
            if (info == '') {
                return 'Nothing left, just enjoy the picture I took!';
            }
            return info;
        },
        showInfo() {
            if (this.checkIsMobile) {
                console.log('showInfo')
                this.showModal = true;
            }
            else {
                this.showModal = false;
            }
        },
        closeModalInfo() {
            this.showModal = false;
        }
    },
    computed: {
        ...mapGetters([
            'checkIsMobile'
        ])
    }

}
</script>

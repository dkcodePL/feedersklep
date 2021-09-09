
easyPackConfig = {
    apiEndpoint: 'https://api-pl-points.easypack24.net/v1',
    locales: ['pl'],
    defaultLocale: 'pl',
    descriptionInWindow: false,
    addressFormat: '{street} {building_number} <br> {post_code} {city}', 

    assetsServer: 'https://geowidget.easypack24.net',
    infoboxLibraryUrl: '/js/lib/infobox.min.js',
    markersUrl: '/images/desktop/markers/',
    iconsUrl: '/images/desktop/icons/',
    loadingIcon: '/images/desktop/icons/ajax-loader.gif',
    mobileSize: 768,
    closeTooltip: true,
    langSelection: false,
    formatOpenHours: false,
    filters: false,
    extendedTypes: [{
      parcel_locker: {
        enabled: true
      }
    },
    {
      pop: {
        enabled: true
      }
    }],
    mobileFiltersAsCheckbox: true,
    points: {
        types: ['pop', 'parcel_locker'],
        subtypes: ['parcel_locker_superpop'],
        allowedToolTips: ['pok', 'pop'],
        functions: [],
        fields: ['name', 'type', 'location', 'address', 'address_details', 'functions'] 
    },
    showOverLoadedLockers: false,
    searchPointsResultLimit: 5,
    customDetailsCallback: false,
    customMapAndListInRow: {
      enabled: false,
      itemsPerPage: 8
    },
    listItemFormat: [
      "<b>{name}</b>",
      "{address_details.street} {address_details.building_number}"
    ],
    map: {
        googleKey: 'AIzaSyDZc6Ajf0PqhUAzbktozQyHFpi5V7TZW_o',
        clusterer: {
            zoomOnClick: true,
            gridSize: 140,
            maxZoom: 16,
            minimumClusterSize: 10,
            styles: [
                {
                    url: '/images/desktop/map-elements/cluster1.png',
                    height: 61,
                    width: 61
                },
                {
                    url: '/images/desktop/map-elements/cluster2.png',
                    height: 74,
                    width: 74
                },
                {
                    url: '/images/desktop/map-elements/cluster3.png',
                    height: 90,
                    width: 90
                }
            ]
        },
        useGeolocation: true,
        initialZoom: 13,
        detailsMinZoom: 15, 
        defaultLocation: [52.229807, 21.011595],
        distanceMultiplier: 1000, 
        closestLimit: 200, 
        preloadLimit: 1000, 
        requestLimit: 4, 
        defaultDistance: 1500, 
        initialTypes: ['pop', 'parcel_locker'], 
        reloadDelay: 250, 
        country: 'pl', 
        typeSelectedIcon: '/images/desktop/icons/selected.png',
        typeSelectedRadio: '/images/mobile/radio.png',
        closeIcon: '/images/desktop/icons/close.png',
        pointIcon: '/images/desktop/icons/point.png',
        pointIconDark: '/images/desktop/icons/point-dark.png',
        detailsIcon: '/images/desktop/icons/info.png',
        selectIcon: '/images/desktop/icons/select.png',
        pointerIcon: '/images/desktop/icons/pointer.png',
        filtersIcon: '/images/desktop/icons/filters.png',
        tooltipPointerIcon: '/images/desktop/icons/half-pointer.png',
        photosUrl: '/uploads/{locale}/images/',
        mapIcon: '/images/mobile/map.png',
        listIcon: '/images/mobile/list.png'
    }
};

instanceConfig = {
  "pl": {
    apiEndpoint: 'https://api-pl-points.easypack24.net/v1',
    extendedTypes: [{
      parcel_locker: {
        enabled: true
      }
    },
      {
        pop: {
          enabled: true,
          additional: "parcel_locker_superpop"
        }
      }],
    listItemFormat: [
      "<b>{name}</b>",
      "{address_details.street} {address_details.building_number}"
    ],
    map: {
      searchCountry: 'Polska'
    }
  },
  "fr": {
    apiEndpoint: 'https://api-fr-points.easypack24.net/v1',
    addressFormat: '{building_number} {street}, {post_code} {city}',
    listItemFormat: [
      "<b>{name}</b>",
      "{address_details.street} {address_details.building_number}, {address_details.post_code} {address_details.city} "
    ],
    map: {
      searchCountry: 'France'
    }
  },
  "uk": {
    apiEndpoint: 'https://api-uk-points.easypack24.net/v1',
    listItemFormat: [
      "<b>{name}</b>",
      "{address_details.street} {address_details.building_number}"
    ],
    map: {
      searchCountry: 'United Kingdom'
    }
  },
  "ca": {
    apiEndpoint: 'https://api-ca-points.easypack24.net/v1',
    listItemFormat: [
      "<b>{name}</b>",
      "{address_details.street} {address_details.building_number}"
    ],
    map: {
      searchCountry: 'Canada'
    }
  }
};






function MarkerClusterer(map, opt_markers, opt_options) {
  this.extend(MarkerClusterer, google.maps.OverlayView);
  this.map_ = map;

  this.markers_ = [];

  this.clusters_ = [];

  this.sizes = [53, 56, 66, 78, 90];

  this.styles_ = [];

  this.ready_ = false;

  var options = opt_options || {};

  this.gridSize_ = options['gridSize'] || 60;

  this.minClusterSize_ = options['minimumClusterSize'] || 2;


  this.maxZoom_ = options['maxZoom'] || null;

  this.styles_ = options['styles'] || [];

  this.imagePath_ = options['imagePath'] ||
    this.MARKER_CLUSTER_IMAGE_PATH_;

  this.imageExtension_ = options['imageExtension'] ||
    this.MARKER_CLUSTER_IMAGE_EXTENSION_;

  this.zoomOnClick_ = true;

  if (options['zoomOnClick'] != undefined) {
    this.zoomOnClick_ = options['zoomOnClick'];
  }

  this.averageCenter_ = false;

  if (options['averageCenter'] != undefined) {
    this.averageCenter_ = options['averageCenter'];
  }

  this.setupStyles_();

  this.setMap(map);

  this.prevZoom_ = this.map_.getZoom();

  var that = this;
  google.maps.event.addListener(this.map_, 'zoom_changed', function() {
    var zoom = that.map_.getZoom();
    if (that.prevZoom_ != zoom) {

      that.resetViewport();
      that.prevZoom_ = zoom;
    }
  });

  google.maps.event.addListener(this.map_, 'idle', function() {
    that.redraw();
  });

  if (opt_markers && opt_markers.length) {
    this.addMarkers(opt_markers, false);
  }
}


MarkerClusterer.prototype.MARKER_CLUSTER_IMAGE_PATH_ =
  'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/' +
  'images/m';


MarkerClusterer.prototype.MARKER_CLUSTER_IMAGE_EXTENSION_ = 'png';


MarkerClusterer.prototype.extend = function(obj1, obj2) {
  return (function(object) {
    for (var property in object.prototype) {
      this.prototype[property] = object.prototype[property];
    }
    return this;
  }).apply(obj1, [obj2]);
};


MarkerClusterer.prototype.onAdd = function() {
  this.setReady_(true);
};

MarkerClusterer.prototype.draw = function() {};

MarkerClusterer.prototype.setupStyles_ = function() {
  if (this.styles_.length) {
    return;
  }

  for (var i = 0, size; size = this.sizes[i]; i++) {
    this.styles_.push({
      url: this.imagePath_ + (i + 1) + '.' + this.imageExtension_,
      height: size,
      width: size
    });
  }
};

MarkerClusterer.prototype.fitMapToMarkers = function() {
  var markers = this.getMarkers();
  var bounds = new google.maps.LatLngBounds();
  for (var i = 0, marker; marker = markers[i]; i++) {
    bounds.extend(marker.getPosition());
  }

  this.map_.fitBounds(bounds);
};


MarkerClusterer.prototype.setStyles = function(styles) {
  this.styles_ = styles;
};


MarkerClusterer.prototype.getStyles = function() {
  return this.styles_;
};


MarkerClusterer.prototype.isZoomOnClick = function() {
  return this.zoomOnClick_;
};

MarkerClusterer.prototype.isAverageCenter = function() {
  return this.averageCenter_;
};


MarkerClusterer.prototype.getMarkers = function() {
  return this.markers_;
};


MarkerClusterer.prototype.getTotalMarkers = function() {
  return this.markers_.length;
};


MarkerClusterer.prototype.setMaxZoom = function(maxZoom) {
  this.maxZoom_ = maxZoom;
};


MarkerClusterer.prototype.getMaxZoom = function() {
  return this.maxZoom_;
};


MarkerClusterer.prototype.calculator_ = function(markers, numStyles) {
  var index = 0;
  var count = markers.length;
  var dv = count;
  while (dv !== 0) {
    dv = parseInt(dv / 10, 10);
    index++;
  }

  index = Math.min(index, numStyles);
  return {
    text: count,
    index: index
  };
};


MarkerClusterer.prototype.setCalculator = function(calculator) {
  this.calculator_ = calculator;
};


MarkerClusterer.prototype.getCalculator = function() {
  return this.calculator_;
};


MarkerClusterer.prototype.addMarkers = function(markers, opt_nodraw) {
  for (var i = 0, marker; marker = markers[i]; i++) {
    this.pushMarkerTo_(marker);
  }
  if (!opt_nodraw) {
    this.redraw();
  }
};


MarkerClusterer.prototype.pushMarkerTo_ = function(marker) {
  marker.isAdded = false;
  if (marker['draggable']) {
    var that = this;
    google.maps.event.addListener(marker, 'dragend', function() {
      marker.isAdded = false;
      that.repaint();
    });
  }
  this.markers_.push(marker);
};


MarkerClusterer.prototype.addMarker = function(marker, opt_nodraw) {
  this.pushMarkerTo_(marker);
  if (!opt_nodraw) {
    this.redraw();
  }
};


MarkerClusterer.prototype.removeMarker_ = function(marker) {
  var index = -1;
  if (this.markers_.indexOf) {
    index = this.markers_.indexOf(marker);
  } else {
    for (var i = 0, m; m = this.markers_[i]; i++) {
      if (m == marker) {
        index = i;
        break;
      }
    }
  }

  if (index == -1) {
    return false;
  }

  marker.setMap(null);

  this.markers_.splice(index, 1);

  return true;
};


MarkerClusterer.prototype.removeMarker = function(marker, opt_nodraw) {
  var removed = this.removeMarker_(marker);

  if (!opt_nodraw && removed) {
    this.resetViewport();
    this.redraw();
    return true;
  } else {
    return false;
  }
};


MarkerClusterer.prototype.removeMarkers = function(markers, opt_nodraw) {
  var removed = false;

  for (var i = 0, marker; marker = markers[i]; i++) {
    var r = this.removeMarker_(marker);
    removed = removed || r;
  }

  if (!opt_nodraw && removed) {
    this.resetViewport();
    this.redraw();
    return true;
  }
};


MarkerClusterer.prototype.setReady_ = function(ready) {
  if (!this.ready_) {
    this.ready_ = ready;
    this.createClusters_();
  }
};


MarkerClusterer.prototype.getTotalClusters = function() {
  return this.clusters_.length;
};


MarkerClusterer.prototype.getMap = function() {
  return this.map_;
};


MarkerClusterer.prototype.setMap = function(map) {
  this.map_ = map;
};


MarkerClusterer.prototype.getGridSize = function() {
  return this.gridSize_;
};


MarkerClusterer.prototype.setGridSize = function(size) {
  this.gridSize_ = size;
};


MarkerClusterer.prototype.getMinClusterSize = function() {
  return this.minClusterSize_;
};

MarkerClusterer.prototype.setMinClusterSize = function(size) {
  this.minClusterSize_ = size;
};


MarkerClusterer.prototype.getExtendedBounds = function(bounds) {
  var projection = this.getProjection();

  var tr = new google.maps.LatLng(bounds.getNorthEast().lat(),
    bounds.getNorthEast().lng());
  var bl = new google.maps.LatLng(bounds.getSouthWest().lat(),
    bounds.getSouthWest().lng());

  var trPix = projection.fromLatLngToDivPixel(tr);
  trPix.x += this.gridSize_;
  trPix.y -= this.gridSize_;

  var blPix = projection.fromLatLngToDivPixel(bl);
  blPix.x -= this.gridSize_;
  blPix.y += this.gridSize_;

  var ne = projection.fromDivPixelToLatLng(trPix);
  var sw = projection.fromDivPixelToLatLng(blPix);

  bounds.extend(ne);
  bounds.extend(sw);

  return bounds;
};


MarkerClusterer.prototype.isMarkerInBounds_ = function(marker, bounds) {
  return bounds.contains(marker.getPosition());
};


MarkerClusterer.prototype.clearMarkers = function() {
  this.resetViewport(true);

  this.markers_ = [];
};


MarkerClusterer.prototype.resetViewport = function(opt_hide) {
  for (var i = 0, cluster; cluster = this.clusters_[i]; i++) {
    cluster.remove();
  }

  for (var i = 0, marker; marker = this.markers_[i]; i++) {
    marker.isAdded = false;
    if (opt_hide) {
      marker.setMap(null);
    }
  }

  this.clusters_ = [];
};

MarkerClusterer.prototype.repaint = function() {
  var oldClusters = this.clusters_.slice();
  this.clusters_.length = 0;
  this.resetViewport();
  this.redraw();

  window.setTimeout(function() {
    for (var i = 0, cluster; cluster = oldClusters[i]; i++) {
      cluster.remove();
    }
  }, 0);
};


MarkerClusterer.prototype.redraw = function() {
  this.createClusters_();
};


MarkerClusterer.prototype.distanceBetweenPoints_ = function(p1, p2) {
  if (!p1 || !p2) {
    return 0;
  }

  var R = 6371; 
  var dLat = (p2.lat() - p1.lat()) * Math.PI / 180;
  var dLon = (p2.lng() - p1.lng()) * Math.PI / 180;
  var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(p1.lat() * Math.PI / 180) * Math.cos(p2.lat() * Math.PI / 180) *
    Math.sin(dLon / 2) * Math.sin(dLon / 2);
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  var d = R * c;
  return d;
};


MarkerClusterer.prototype.addToClosestCluster_ = function(marker) {
  var distance = 40000; 
  var clusterToAddTo = null;
  var pos = marker.getPosition();
  for (var i = 0, cluster; cluster = this.clusters_[i]; i++) {
    var center = cluster.getCenter();
    if (center) {
      var d = this.distanceBetweenPoints_(center, marker.getPosition());
      if (d < distance) {
        distance = d;
        clusterToAddTo = cluster;
      }
    }
  }

  if (clusterToAddTo && clusterToAddTo.isMarkerInClusterBounds(marker)) {
    clusterToAddTo.addMarker(marker);
  } else {
    var cluster = new Cluster(this);
    cluster.addMarker(marker);
    this.clusters_.push(cluster);
  }
};


MarkerClusterer.prototype.createClusters_ = function() {
  if (!this.ready_) {
    return;
  }

  var mapBounds = new google.maps.LatLngBounds(this.map_.getBounds().getSouthWest(),
    this.map_.getBounds().getNorthEast());
  var bounds = this.getExtendedBounds(mapBounds);

  for (var i = 0, marker; marker = this.markers_[i]; i++) {
    if (!marker.isAdded && this.isMarkerInBounds_(marker, bounds)) {
      this.addToClosestCluster_(marker);
    }
  }
};


function Cluster(markerClusterer) {
  this.markerClusterer_ = markerClusterer;
  this.map_ = markerClusterer.getMap();
  this.gridSize_ = markerClusterer.getGridSize();
  this.minClusterSize_ = markerClusterer.getMinClusterSize();
  this.averageCenter_ = markerClusterer.isAverageCenter();
  this.center_ = null;
  this.markers_ = [];
  this.bounds_ = null;
  this.clusterIcon_ = new ClusterIcon(this, markerClusterer.getStyles(),
    markerClusterer.getGridSize());
}

Cluster.prototype.isMarkerAlreadyAdded = function(marker) {
  if (this.markers_.indexOf) {
    return this.markers_.indexOf(marker) != -1;
  } else {
    for (var i = 0, m; m = this.markers_[i]; i++) {
      if (m == marker) {
        return true;
      }
    }
  }
  return false;
};


Cluster.prototype.addMarker = function(marker) {
  if (this.isMarkerAlreadyAdded(marker)) {
    return false;
  }

  if (!this.center_) {
    this.center_ = marker.getPosition();
    this.calculateBounds_();
  } else {
    if (this.averageCenter_) {
      var l = this.markers_.length + 1;
      var lat = (this.center_.lat() * (l-1) + marker.getPosition().lat()) / l;
      var lng = (this.center_.lng() * (l-1) + marker.getPosition().lng()) / l;
      this.center_ = new google.maps.LatLng(lat, lng);
      this.calculateBounds_();
    }
  }

  marker.isAdded = true;
  this.markers_.push(marker);

  var len = this.markers_.length;
  if (len < this.minClusterSize_ && marker.getMap() != this.map_) {
    marker.setMap(this.map_);
  }

  if(this.map_.getZoom() <= this.markerClusterer_.maxZoom_) {
    if (len == this.minClusterSize_) {
      for (var i = 0; i < len; i++) {
        this.markers_[i].setMap(null);
      }
    }

    if (len >= this.minClusterSize_) {
      marker.setMap(null);
    }
  }

  this.updateIcon();
  return true;
};


Cluster.prototype.getMarkerClusterer = function() {
  return this.markerClusterer_;
};


Cluster.prototype.getBounds = function() {
  var bounds = new google.maps.LatLngBounds(this.center_, this.center_);
  var markers = this.getMarkers();
  for (var i = 0, marker; marker = markers[i]; i++) {
    bounds.extend(marker.getPosition());
  }
  return bounds;
};


Cluster.prototype.remove = function() {
  this.clusterIcon_.remove();
  this.markers_.length = 0;
  delete this.markers_;
};


Cluster.prototype.getSize = function() {
  return this.markers_.length;
};


Cluster.prototype.getMarkers = function() {
  return this.markers_;
};


Cluster.prototype.getCenter = function() {
  return this.center_;
};


Cluster.prototype.calculateBounds_ = function() {
  var bounds = new google.maps.LatLngBounds(this.center_, this.center_);
  this.bounds_ = this.markerClusterer_.getExtendedBounds(bounds);
};


Cluster.prototype.isMarkerInClusterBounds = function(marker) {
  return this.bounds_.contains(marker.getPosition());
};


Cluster.prototype.getMap = function() {
  return this.map_;
};


Cluster.prototype.updateIcon = function() {
  var zoom = this.map_.getZoom();
  var mz = this.markerClusterer_.getMaxZoom();

  if (mz && zoom > mz) {
    for (var i = 0, marker; marker = this.markers_[i]; i++) {
    }
    return;
  }

  if (this.markers_.length < this.minClusterSize_) {
    this.clusterIcon_.hide();
    return;
  }

  var numStyles = this.markerClusterer_.getStyles().length;
  var sums = this.markerClusterer_.getCalculator()(this.markers_, numStyles);
  this.clusterIcon_.setCenter(this.center_);
  this.clusterIcon_.setSums(sums);
  this.clusterIcon_.show();
};


function ClusterIcon(cluster, styles, opt_padding) {
  cluster.getMarkerClusterer().extend(ClusterIcon, google.maps.OverlayView);

  this.styles_ = styles;
  this.padding_ = opt_padding || 0;
  this.cluster_ = cluster;
  this.center_ = null;
  this.map_ = cluster.getMap();
  this.div_ = null;
  this.sums_ = null;
  this.visible_ = false;

  this.setMap(this.map_);
}


ClusterIcon.prototype.triggerClusterClick = function() {
  var markerClusterer = this.cluster_.getMarkerClusterer();

  google.maps.event.trigger(markerClusterer, 'clusterclick', this.cluster_);

  if (markerClusterer.isZoomOnClick()) {
    this.map_.fitBounds(this.cluster_.getBounds());
    this.map_.setZoom(this.map_.getZoom() + 1)
  }
};


ClusterIcon.prototype.onAdd = function() {
  this.div_ = document.createElement('DIV');
  if (this.visible_) {
    var pos = this.getPosFromLatLng_(this.center_);
    this.div_.style.cssText = this.createCss(pos);
    this.div_.innerHTML = this.sums_.text;
  }

  var panes = this.getPanes();
  panes.overlayMouseTarget.appendChild(this.div_);

  var that = this;
  google.maps.event.addDomListener(this.div_, 'click', function() {
    that.triggerClusterClick();
  });
};


ClusterIcon.prototype.getPosFromLatLng_ = function(latlng) {
  var pos = this.getProjection().fromLatLngToDivPixel(latlng);
  pos.x -= parseInt(this.width_ / 2, 10);
  pos.y -= parseInt(this.height_ / 2, 10);
  return pos;
};


ClusterIcon.prototype.draw = function() {
  if (this.visible_) {
    var pos = this.getPosFromLatLng_(this.center_);
    this.div_.style.top = pos.y + 'px';
    this.div_.style.left = pos.x + 'px';
  }
};


ClusterIcon.prototype.hide = function() {
  if (this.div_) {
    this.div_.style.display = 'none';
  }
  this.visible_ = false;
};


ClusterIcon.prototype.show = function() {
  if (this.div_) {
    var pos = this.getPosFromLatLng_(this.center_);
    this.div_.style.cssText = this.createCss(pos);
    this.div_.style.display = '';
  }
  this.visible_ = true;
};


ClusterIcon.prototype.remove = function() {
  this.setMap(null);
};


ClusterIcon.prototype.onRemove = function() {
  if (this.div_ && this.div_.parentNode) {
    this.hide();
    this.div_.parentNode.removeChild(this.div_);
    this.div_ = null;
  }
};


ClusterIcon.prototype.setSums = function(sums) {
  this.sums_ = sums;
  this.text_ = sums.text;
  this.index_ = sums.index;
  if (this.div_) {
    this.div_.innerHTML = sums.text;
  }

  this.useStyle();
};


ClusterIcon.prototype.useStyle = function() {
  var index = Math.max(0, this.sums_.index - 1);
  index = Math.min(this.styles_.length - 1, index);
  var style = this.styles_[index];
  this.url_ = style['url'];
  this.height_ = style['height'];
  this.width_ = style['width'];
  this.textColor_ = style['textColor'];
  this.anchor_ = style['anchor'];
  this.textSize_ = style['textSize'];
  this.backgroundPosition_ = style['backgroundPosition'];
};


ClusterIcon.prototype.setCenter = function(center) {
  this.center_ = center;
};


ClusterIcon.prototype.createCss = function(pos) {
  var style = [];
  style.push('background-image:url(' + this.url_ + ');');
  var backgroundPosition = this.backgroundPosition_ ? this.backgroundPosition_ : '0 0';
  style.push('background-position:' + backgroundPosition + ';');

  if (typeof this.anchor_ === 'object') {
    if (typeof this.anchor_[0] === 'number' && this.anchor_[0] > 0 &&
      this.anchor_[0] < this.height_) {
      style.push('height:' + (this.height_ - this.anchor_[0]) +
        'px; padding-top:' + this.anchor_[0] + 'px;');
    } else {
      style.push('height:' + this.height_ + 'px; line-height:' + this.height_ +
        'px;');
    }
    if (typeof this.anchor_[1] === 'number' && this.anchor_[1] > 0 &&
      this.anchor_[1] < this.width_) {
      style.push('width:' + (this.width_ - this.anchor_[1]) +
        'px; padding-left:' + this.anchor_[1] + 'px;');
    } else {
      style.push('width:' + this.width_ + 'px; text-align:center;');
    }
  } else {
    style.push('height:' + this.height_ + 'px; line-height:' +
      this.height_ + 'px; width:' + this.width_ + 'px; text-align:center;');
  }

  var txtColor = this.textColor_ ? this.textColor_ : 'black';
  var txtSize = this.textSize_ ? this.textSize_ : 11;

  style.push('cursor:pointer; top:' + pos.y + 'px; left:' +
    pos.x + 'px; color:' + txtColor + '; position:absolute; font-size:' +
    txtSize + 'px; font-family:Arial,sans-serif; font-weight:bold');
  return style.join('');
};


window['MarkerClusterer'] = MarkerClusterer;
MarkerClusterer.prototype['addMarker'] = MarkerClusterer.prototype.addMarker;
MarkerClusterer.prototype['addMarkers'] = MarkerClusterer.prototype.addMarkers;
MarkerClusterer.prototype['clearMarkers'] =
  MarkerClusterer.prototype.clearMarkers;
MarkerClusterer.prototype['fitMapToMarkers'] =
  MarkerClusterer.prototype.fitMapToMarkers;
MarkerClusterer.prototype['getCalculator'] =
  MarkerClusterer.prototype.getCalculator;
MarkerClusterer.prototype['getGridSize'] =
  MarkerClusterer.prototype.getGridSize;
MarkerClusterer.prototype['getExtendedBounds'] =
  MarkerClusterer.prototype.getExtendedBounds;
MarkerClusterer.prototype['getMap'] = MarkerClusterer.prototype.getMap;
MarkerClusterer.prototype['getMarkers'] = MarkerClusterer.prototype.getMarkers;
MarkerClusterer.prototype['getMaxZoom'] = MarkerClusterer.prototype.getMaxZoom;
MarkerClusterer.prototype['getStyles'] = MarkerClusterer.prototype.getStyles;
MarkerClusterer.prototype['getTotalClusters'] =
  MarkerClusterer.prototype.getTotalClusters;
MarkerClusterer.prototype['getTotalMarkers'] =
  MarkerClusterer.prototype.getTotalMarkers;
MarkerClusterer.prototype['redraw'] = MarkerClusterer.prototype.redraw;
MarkerClusterer.prototype['removeMarker'] =
  MarkerClusterer.prototype.removeMarker;
MarkerClusterer.prototype['removeMarkers'] =
  MarkerClusterer.prototype.removeMarkers;
MarkerClusterer.prototype['resetViewport'] =
  MarkerClusterer.prototype.resetViewport;
MarkerClusterer.prototype['repaint'] =
  MarkerClusterer.prototype.repaint;
MarkerClusterer.prototype['setCalculator'] =
  MarkerClusterer.prototype.setCalculator;
MarkerClusterer.prototype['setGridSize'] =
  MarkerClusterer.prototype.setGridSize;
MarkerClusterer.prototype['setMaxZoom'] =
  MarkerClusterer.prototype.setMaxZoom;
MarkerClusterer.prototype['onAdd'] = MarkerClusterer.prototype.onAdd;
MarkerClusterer.prototype['draw'] = MarkerClusterer.prototype.draw;

Cluster.prototype['getCenter'] = Cluster.prototype.getCenter;
Cluster.prototype['getSize'] = Cluster.prototype.getSize;
Cluster.prototype['getMarkers'] = Cluster.prototype.getMarkers;

ClusterIcon.prototype['onAdd'] = ClusterIcon.prototype.onAdd;
ClusterIcon.prototype['draw'] = ClusterIcon.prototype.draw;
ClusterIcon.prototype['onRemove'] = ClusterIcon.prototype.onRemove;
;(function(window, undefined) {
  "use strict";

  function extend() {
    for(var i=1; i < arguments.length; i++) {
      for(var key in arguments[i]) {
        if(arguments[i].hasOwnProperty(key)) {
          arguments[0][key] = arguments[i][key];
        }
      }
    }
    return arguments[0];
  }

  var pluginName = "tinyscrollbar"
      ,   defaults = {
        axis: 'y'
        ,   wheel: true
        ,   wheelSpeed: 40
        ,   wheelLock: true
        ,   touchLock: true
        ,   trackSize: false
        ,   thumbSize: false
        ,   thumbSizeMin: 20
      }
      ;

  function Plugin($container, options) {
    this.options = extend({}, defaults, options);

    this._defaults = defaults;

    this._name = pluginName;

    var self = this
        ,   $body = document.querySelectorAll("body")[0]
        ,   $viewport = $container.querySelectorAll(".viewport")[0]
        ,   $overview = $container.querySelectorAll(".overview")[0]
        ,   $scrollbar = $container.querySelectorAll(".scrollbar")[0]
        ,   $track = $scrollbar.querySelectorAll(".track")[0]
        ,   $thumb = $scrollbar.querySelectorAll(".thumb")[0]

        ,   mousePosition = 0
        ,   isHorizontal = this.options.axis === 'x'
        ,   hasTouchEvents = ("ontouchstart" in document.documentElement)
        ,   wheelEvent = "onwheel" in document.createElement("div") ? "wheel" : 
            document.onmousewheel !== undefined ? "mousewheel" : 
                "DOMMouseScroll" 

        ,   sizeLabel = isHorizontal ? "width" : "height"
        ,   posiLabel = isHorizontal ? "left" : "top"
        ,   moveEvent = document.createEvent("HTMLEvents")
        ;

    moveEvent.initEvent("move", true, true);

    this.contentPosition = 0;

    this.viewportSize = 0;

    this.contentSize = 0;

    this.contentRatio = 0;

    this.trackSize = 0;

    this.trackRatio = 0;

    this.thumbSize = 0;

    this.thumbPosition = 0;

    this.hasContentToSroll = false;

    function _initialize() {
      self.update();
      _setEvents();

      return self;
    }

    this.update = function(scrollTo) {
      var sizeLabelCap = sizeLabel.charAt(0).toUpperCase() + sizeLabel.slice(1).toLowerCase();
      var scrcls = $scrollbar.className;

      this.viewportSize = $viewport['offset'+ sizeLabelCap];
      this.contentSize = $overview['scroll'+ sizeLabelCap];
      this.contentRatio = this.viewportSize / this.contentSize;
      this.trackSize = this.options.trackSize || this.viewportSize;
      this.thumbSize = Math.min(this.trackSize, Math.max(this.options.thumbSizeMin, (this.options.thumbSize || (this.trackSize * this.contentRatio))));
      this.trackRatio = (this.contentSize - this.viewportSize) / (this.trackSize - this.thumbSize);
      this.hasContentToSroll = this.contentRatio < 1;

      $scrollbar.className = this.hasContentToSroll ? scrcls.replace(/disable/g, "") : scrcls.replace(/ disable/g, "") + " disable";

      switch (scrollTo) {
        case "bottom":
          this.contentPosition = Math.max(this.contentSize - this.viewportSize, 0);
          break;

        case "relative":
          this.contentPosition = Math.min(Math.max(this.contentSize - this.viewportSize, 0), Math.max(0, this.contentPosition));
          break;

        default:
          this.contentPosition = parseInt(scrollTo, 10) || 0;
      }

      this.thumbPosition = self.contentPosition / self.trackRatio;

      _setCss();

      return self;
    };

    function _setCss() {
      $thumb.style[posiLabel] = self.thumbPosition + "px";
      $overview.style[posiLabel] = -self.contentPosition + "px";
      $scrollbar.style[sizeLabel] = self.trackSize + "px";
      $track.style[sizeLabel] = self.trackSize + "px";
      $thumb.style[sizeLabel] = self.thumbSize + "px";
    }

    function _setEvents() {
      if(hasTouchEvents) {
        $viewport.ontouchstart = function(event) {
          if(1 === event.touches.length) {
            start(event.touches[0]);
            event.stopPropagation();
          }
        };
      }
      else {
        $thumb.onmousedown = function(event) {
          event.stopPropagation();
          _start(event);
        };

        $track.onmousedown = function(event) {
          _start(event, true);
        };
      }

      window.addEventListener("resize", function() {
        self.update("relative");
      }, true);

      if(self.options.wheel && window.addEventListener) {
        $container.addEventListener(wheelEvent, _wheel, false );
      }
      else if(self.options.wheel) {
        $container.onmousewheel = _wheel;
      }
    }

    function _isAtBegin() {
      return self.contentPosition > 0;
    }

    function _isAtEnd() {
      return self.contentPosition <= (self.contentSize - self.viewportSize) - 5;
    }

    function _start(event, gotoMouse) {
      if(self.hasContentToSroll) {
        var posiLabelCap = posiLabel.charAt(0).toUpperCase() + posiLabel.slice(1).toLowerCase();
        mousePosition = gotoMouse ? $thumb.getBoundingClientRect()[posiLabel] : (isHorizontal ? event.clientX : event.clientY);

        $body.className += " noSelect";

        if(hasTouchEvents) {
          document.ontouchmove = function(event) {
            if(self.options.touchLock || _isAtBegin() && _isAtEnd()) {
              event.preventDefault();
            }
            drag(event.touches[0]);
          };
          document.ontouchend = _end;
        }
        else {
          document.onmousemove = _drag;
          document.onmouseup = $thumb.onmouseup = _end;
        }

        _drag(event);
      }
    }

    function _wheel(event) {
      if(self.hasContentToSroll) {
        var evntObj = event || window.event
            ,   wheelSpeedDelta = -(evntObj.deltaY || evntObj.detail || (-1 / 3 * evntObj.wheelDelta)) / 40
            ,   multiply = (evntObj.deltaMode === 1) ? self.options.wheelSpeed : 1
            ;

        self.contentPosition -= wheelSpeedDelta * self.options.wheelSpeed;
        self.contentPosition = Math.min((self.contentSize - self.viewportSize), Math.max(0, self.contentPosition));
        self.thumbPosition = self.contentPosition / self.trackRatio;

        $container.dispatchEvent(moveEvent);

        $thumb.style[posiLabel] = self.thumbPosition + "px";
        $overview.style[posiLabel] = -self.contentPosition + "px";

        if(self.options.wheelLock || _isAtBegin() && _isAtEnd()) {
          evntObj.preventDefault();
        }
      }
    }

    function _drag(event) {
      if(self.hasContentToSroll)
      {
        var mousePositionNew = isHorizontal ? event.clientX : event.clientY
            ,   thumbPositionDelta = hasTouchEvents ? (mousePosition - mousePositionNew) : (mousePositionNew - mousePosition)
            ,   thumbPositionNew = Math.min((self.trackSize - self.thumbSize), Math.max(0, self.thumbPosition + thumbPositionDelta))
            ;

        self.contentPosition = thumbPositionNew * self.trackRatio;

        $container.dispatchEvent(moveEvent);

        $thumb.style[posiLabel] = thumbPositionNew + "px";
        $overview.style[posiLabel] = -self.contentPosition + "px";
      }
    }


    function _end() {
      self.thumbPosition = parseInt($thumb.style[posiLabel], 10) || 0;

      $body.className = $body.className.replace(" noSelect", "");
      document.onmousemove = document.onmouseup = null;
      $thumb.onmouseup = null;
      $track.onmouseup = null;
      document.ontouchmove = document.ontouchend = null;
    }

    return _initialize();
  }

  var tinyscrollbar = function($container, options) {
    return new Plugin($container, options);
  };

  if(typeof define == 'function' && define.amd) {
    define(function(){ return tinyscrollbar; });
  }
  else if(typeof module === 'object' && module.exports) {
    module.exports = tinyscrollbar;
  }
  else {
    window.tinyscrollbar = tinyscrollbar;
  }
})(window);
easyPackLocales = {
  "pl":
    {
      "map":"Mapa",
      "list":"Lista",
      "search_by_city_or_address":"Szukaj po mieście, adresie i nazwie paczkomatu",
      "search":"Szukaj",
      "select_point": "Wybierz punkt...",
      "parcel_locker":"Paczkomat",
      "parcel_locker_group": "Typy paczkomatów",
      "parcel_locker_only":"Paczkomat",
      "laundry_locker":"Pralniomat",
      "avizo_locker":"Awizomaty24",
      "pok":"POP",
      "pop":"POP",
      "allegro_courier" : "POP",
      "nfk":"Oddział NFK",
      "avizo":"Punkt awizo",
      "office":"Lokalizacje biur",
      "plan_route":"Zaplanuj trasę",
      "details":"Szczegóły",
      "select":"Wybierz",
      "locationDescription":"Położenie",
      "openingHours":"Godziny otwarcia",
      "pok_name":"Punkt Obsługi Przesyłek",
      "pok_name_short": "POP",
      "parcel_locker_superpop":"Punkt Obsługi Przesyłek",
      "parcel_locker_superpop_short":"POP",
      "allegro_courier_name": "Punkt Obsługi Przesyłek",
      "parcel_locker_name": "Paczkomat",
      "avizo_name":"Punkt Awizo",
      "pok_description":"Punkt Obsługi Przesyłek",
      "avizo_description":"Punkt odbioru przesyłki listowej lub kurierskiej",
      "parcel_locker_description":"Maszyna do nadawania i odbioru przesyłek 24/7",
      "avizo_locker_description":"Maszyna do odbioru przesyłek awizowanych 24/7",
      "air_on_airport":"Maszyna na lotnisku",
      "air_outside_airport":"Maszyna poza lotniskiem",
      "air_on_airport_description":"Maszyna znajdująca się na terenie lotniska",
      "air_outside_airport_description":"Maszyna znajdująca się poza terenem lotniska",
      "nfk_description":"Siedziba główna (magazyn) InPost w danym mieście lub regionie",
      "pop_description":"Placówka, w której można nadać lub odebrać przesyłkę paczkomatową",
      "office_description": "Centrala i oddziały firmy",
      "allegro_courier_description": "Punkt Obsługi Przesyłek",
      "of" : "z",
      "points_loaded" : "punktów załadowanych.",
      "phone_short" : 'tel. ',
      "pay_by_link" : 'Formy płatności',
      "is_next" : 'Brak możliwości nadania bez etykiety "Wygodnie wprost z Paczkomatu"',
      "show_filters" : "Chcę zrealizować usługę...",
      "MON" : "Poniedziałek",
      "TUE" : "Wtorek",
      "WED" : "Środa",
      "THU" : "Czwartek",
      "FRI" : "Piątek",
      "SAT" : "Sobota",
      "SUN" : "Niedziela",
      "show_on_map" : "Pokaż na mapie",
      "more" : "więcej",
      "next" : "Następna",
      "prev" : "Poprzednia"
    },
    "pl-PL":
    {
      "map":"Mapa",
      "list":"Lista",
      "search_by_city_or_address":"Szukaj po mieście, adresie i nazwie paczkomatu",
      "search":"Szukaj",
      "select_point": "Wybierz punkt...",
      "parcel_locker":"Paczkomat",
      "laundry_locker":"Pralniomat",
      "avizo_locker":"Awizomaty24",
      "pok":"POP",
      "pop":"POP",
      "allegro_courier" : "POP",
      "nfk":"Oddział NFK",
      "avizo":"Punkt awizo",
      "office":"Lokalizacje biur",
      "plan_route":"Zaplanuj trasę",
      "details":"Szczegóły",
      "select":"Wybierz",
      "locationDescription":"Położenie",
      "openingHours":"Godziny otwarcia",
      "pok_name_short": "POP",
      "pop_name":"Punkt Obsługi Przesyłek",
      "parcel_locker_superpop":"Punkt Obsługi Przesyłek",
      "parcel_locker_superpop_short":"POP",
      "allegro_courier_name": "Punkt Obsługi Przesyłek",
      "parcel_locker_name": "Paczkomat",
      "avizo_name":"Punkt Awizo",
      "pop_description":"Punkt Obsługi Przesyłek",
      "avizo_description":"Punkt odbioru przesyłki listowej lub kurierskiej",
      "parcel_locker_description":"Maszyna do nadawania i odbioru przesyłek 24/7",
      "avizo_locker_description":"Maszyna do odbioru przesyłek awizowanych 24/7",
      "air_on_airport":"Maszyna na lotnisku",
      "air_outside_airport":"Maszyna poza lotniskiem",
      "air_on_airport_description":"Maszyna znajdująca się na terenie lotniska",
      "air_outside_airport_description":"Maszyna znajdująca się poza terenem lotniska",
      "nfk_description":"Siedziba główna (magazyn) InPost w danym mieście lub regionie",
      "pop_description":"Placówka, w której można nadać lub odebrać przesyłkę paczkomatową",
      "office_description": "Centrala i oddziały firmy",
      "allegro_courier_description": "Punkt Obsługi Przesyłek",
      "of" : "z",
      "points_loaded" : "punktów załadowanych.",
      "phone_short" : 'tel. ',
      "pay_by_link" : 'Formy płatności',
      "is_next" : 'Brak możliwości nadania bez etykiety "Wygodnie wprost z Paczkomatu"',
      "show_filters" : "Chcę zrealizować usługę...",
      "MON" : "Poniedziałek",
      "TUE" : "Wtorek",
      "WED" : "Środa",
      "THU" : "Czwartek",
      "FRI" : "Piątek",
      "SAT" : "Sobota",
      "SUN" : "Niedziela",
      "show_on_map" : "Pokaż na mapie",
      "more" : "więcej",
      "next" : "Następna",
      "prev" : "Poprzednia"
    },
    "uk":
    {
      "map":"Map",
      "list":"List",
      "search_by_city_or_address":"Type your city, address or machine name",
      "search":"Search",
      "select_point": "Select point...",
      "parcel_locker":"Parcel Locker",
      "laundry_locker":"Laundry Locker",
      "avizo_locker":"Avizo Locker",
      "pop":"InPost PUDO",
      "allegro_courier" : "POP",
      "nfk":"Oddział NFK",
      "avizo":"Avizo point",
      "office":"Office location",
      "plan_route":"Plan your route",
      "details":"Details",
      "select":"Select",
      "parcel_locker_name": "InPost Locker 24/7",
      "locationDescription":"Location description",
      "openingHours":"Opening hours",
      "pop_name":"Customer Service Point",
      "parcel_locker_superpop":"Customer Service Point",
      "avizo_name":"Avizo Point",
      "pop_description":"\u003cstrong\u003eInPost PUDO\u003c/strong\u003e location, where you can collect or send your parcel",
      "avizo_description":"Point where you can collect your Parcel or Letter for which we left attempted delivery notice",
      "parcel_locker_description":"Parcel Locker where you can collect or send your parcels 24/7",
      "avizo_locker_description":"Parcel Locker where you can collect your parcels 24/7",
      "air_on_airport":"Airport Locker",
      "air_outside_airport":"Outside Airport Locker",
      "air_on_airport_description":"Machine within airport area",
      "air_outside_airport_description":"Machine outside of airport area",
      "nfk_description":"Main InPost Hub in city or region",
      "pop_description":"Point where you can collect or send your parcels",
      "office_description": "InPost HQ",
      "allegro_courier_description": "Punkty Nadania Allegro Kurier InPost",
      "of" : "z",
      "points_loaded" : "locations loaded",
      "phone_short" : 'tel ',
      "pay_by_link" : 'Payment options',
      "is_next" : 'Only parcel collection and pre-labeled parcel lodgement available at this location',
      "MON" : "Monday",
      "TUE" : "Tuesday",
      "WED" : "Wednesday",
      "THU" : "Thursday",
      "FRI" : "Friday",
      "SAT" : "Saturday",
      "SUN" : "Sunday",
      "show_on_map" : "Show on map",
      "more" : "more"
    },
    "fr":
    {
      "map":"Carte",
      "list":"Liste",
      "search_by_city_or_address":"Saisissez votre ville, adresse ou casier à colis",
      "search":"Rechercher",
      "parcel_locker":"Consigne Abricolis",
      "laundry_locker":"Casier de blanchisserie",
      "avizo_locker":"Casier Avizo",
      "pop":"Point de retrait InPost",
      "allegro_courier" : "POP",
      "nfk":"Nouvelle Agence Courrier",
      "avizo":"Point Avizo",
      "office":"Bureau",
      "plan_route":"Itinéraire",
      "details":"Détails",
      "select":"Selectionner",
      "parcel_locker_name": "InPost Consigne Abricolis",
      "locationDescription":"Où se situe la consigne?",
      "openingHours":"Heures d'ouverture",
      "pop_name":"Point de service à la clientèle",
      "parcel_locker_superpop":"Point de service à la clientèle",
      "avizo_name":"Point Avizo",
      "pop_description":"Point d'envoi et de réception de colis",
      "avizo_description":"Point de réception de lettres et de colis après l'avisage",
      "parcel_locker_description":"Abricolis InPost 24h/24 et 7j/7",
      "avizo_locker_description":"Abricolis InPost 24h/24 et 7j/7",
      "air_on_airport":"Distributeur de Colis Aéroport",
      "air_outside_airport":"Distributeur de Colis en dehors Aéroport",
      "air_on_airport_description":"Machine dans zone d'aéroport",
      "air_outside_airport_description":"Machine à l'extérieur de zone d'aéroport",
      "nfk_description":"Agence principale d'InPost",
      "pop_description":"Point de réception et d'envoi de colis",
      "office_description": "Siège sociale d'InPost",
      "allegro_courier_description": "Punkty Nadania Allegro Kurier InPost",
      "of" : "",
      "points_loaded" : "Emplacement chargés",
      "phone_short" : 'tél ',
      "pay_by_link" : 'Modes de paiement ',
      "is_next" : 'Uniquement réception de colis et envoi de colis pré-étiquetés',
      "MON" : "lundi",
      "TUE" : "mardi",
      "WED" : "mercredi",
      "THU" : "jeudi",
      "FRI" : "vendredi",
      "SAT" : "samedi",
      "SUN" : "dimanche",
      "show_on_map" : "Show on map",
      "more" : "more"
    }
  };

easyPack = (function () {

  var module = {};


  module.init = function (userConfig) {

    configure(userConfig);
    helpers.loadWebfonts();
    module.config = easyPackConfig;
    module.userConfig = userConfig;
    easyPack.locale = easyPackConfig.defaultLocale;
  };

  module.asyncInit = function() {
    if (typeof window.easyPackAsyncInit !== 'undefined') {
      window.easyPackAsyncInit();
    } else {
      setTimeout(module.asyncInit, 250);
    }
  };

  module.pointsToSearch = [];

  module.mapWidget = function(placeholder, pointCallback, callback) {
    return new map(placeholder, pointCallback, callback, module);
  };

  module.dropdownWidget = function(container, callback) {
    return new dropdownWidget(container, callback, module);
  };

  module.modalMap = function(callback, options) {
    if(!document.getElementById('widget-modal')) {
      new modal(options);
      module.map = new map('widget-modal__map', callback, null, module);
      module.map.isModal = true;
    } else {
      if(module.map.isMobile && module.map.viewChooserObj !== undefined) {
        module.map.viewChooserObj.resetState();
      }
      document.getElementById('widget-modal').className = 'widget-modal';
    }

    return module.map;
  };

  module.points = (function(){

    var submodule = {};

    submodule.find = function(name, callback) {
      module.api.point(name, function(response){
        callback(response);
      });
    };

    submodule.closest = function(location, distance, params, callback) {
      params.relative_point = location;
      params.max_distance = distance;
      params.limit = params.limit || easyPackConfig.map.closestLimit;

      var loader = new loaderFn(params, callback);
      loader.closest();
    };

    submodule.allAsync = function(location, distance, params, callback, abortCallback) {
      params.relative_point = location;
      params.per_page = easyPackConfig.map.preloadLimit;

      var loader = new loaderFn(params, callback, abortCallback);
      loader.allAsync();
    };

    submodule.markerIcon = function(point, typesSequence, currentType) {
      return easyPackConfig.markersUrl + mainType(point, typesSequence).replace('_only', '') + '.png';
    };

    submodule.listIcon = function(point, typesSequence, currentType) {
      return easyPackConfig.iconsUrl + mainType(point, typesSequence).replace('_only', '') + '.png';
    };

    submodule.typeCheck = function(point, types) {
      var pointTypes = point.type;
      var i;
      for(i = 0; i < pointTypes.length; i++) {
        var type = pointTypes[i];
        if(helpers.in(type, types)){
          return true;
        }
      }
    };

    var loaderFn = function(params, callback, abortCallback) {
      this.callback = callback;
      this.abortCallback = abortCallback;
      var fields = params.optimized ? [easyPackConfig.points.fields[1], easyPackConfig.points.fields[2]] : easyPackConfig.points.fields;

      this.params = {
          fields: fields,
          status: ['Operating', 'NonOperating']
      };
      if (params.functions && params.functions.length === 0) {
          delete params.functions
      }
      if (true === easyPackConfig.showOverLoadedLockers) {
        this.params.status.push('Overloaded');
      }

      this.params = helpers.merge(this.params, params);

      return this;
    };

    loaderFn.prototype = {
      closest: function() {
        var self = this;
        module.api.points(self.params, function (response) {
          self.callback(response.items);
        });
      },
      allAsync: function() {
        var self = this,
            page = 1,
            chunk = 1,
            totalPages = 0;

        self.params.type = typesHelpers.getUniqueValues(self.params.type);

        function request() {
          for(var i = 0; i < easyPackConfig.map.requestLimit; i++) {
            if (page > totalPages) {
              return;
            }
            self.params.page = page;
            module.api.points(self.params, function (response) {
              self.callback(response);
              if (i === easyPackConfig.map.requestLimit && totalPages >= page) {
                request()
              }
            }, self.abortCallback);
            page++;
          }
        }

        self.params.page = page;

        if (easyPackConfig.points.functions.length > 0) {
          this.params = helpers.merge(this.params, {
            functions: easyPackConfig.points.functions
          })
        }

        module.api.points(self.params, function (response) {
          self.callback(response);
          totalPages = response.total_pages;
          if(totalPages === undefined) {
            totalPages = 0;
          }
          page++;

          if(totalPages > 0) {
            request();
          }
        }, self.abortCallback);
      }
    };

    var mainType = function(point, typesSequence) {

      if(point.type.length > 1) {
        point.type = typesHelpers.sortByPriorities(point.type);
        if(typesSequence.length > 0 && typesSequence[0] !== undefined) {
          typesSequence = typesHelpers.sortByPriorities(typesSequence)
          for(var i = 0; i < point.type.length; i++) {
            var type = point.type[i].replace("_only", "");
            if(typesHelpers.in(type, typesSequence)) {
              return type;
            }
          }
          return point.type[0]
        } else {
          return point.type[0]
        }

      } else {
        return point.type[0];
      }
    };

    return submodule;
  })();
;

  module.googleMapsApi = {};
module.googleMapsApi.initialize = function() {
    easyPack.googleMapsApi.ready = true;
    helpers.asyncLoad(easyPackConfig.infoboxLibraryUrl);
};

module.googleMapsApi.initializeDropdown = function() {
    easyPack.googleMapsApi.ready = true;
    module.dropdownWidgetObj.afterLoad();
};;

  module.api = {
    points_path: '/points',
    filters_path: '/functions',
    pendingRequests: [],
    url: function(path, params) {
      var endpoint = easyPackConfig.apiEndpoint;
      var locale = easyPackConfig.defaultLocale.split('-')[0];
      endpoint = endpoint.replace('{locale}', locale);

      var url = endpoint + path;
      if (params) {
        var urlParams = helpers.serialize(params);
        url += '?' + urlParams;
      }
      return url;
    },
    request: function(path, method, params, callback, abortCallback) {
      helpers.checkArguments('module.api.request()', 5, arguments);

      if (params && params.type) {
          params.type = typesHelpers.getUniqueValues(params.type || []);
      }
      var req = ajax(this.url(path, params),method ,callback);

      req.onabort = function() {
        if(abortCallback !== undefined) {
          abortCallback(params.type[0]);
        }
      };
      this.pendingRequests.push(req);
    },
    point: function(id, callback, abortCallback) {
      module.api.request(module.api.points_path + '/' + id, 'get', null, callback, abortCallback);
    },
    points: function(params, callback, abortCallback) {
      module.api.request(module.api.points_path, 'get', params, callback, abortCallback);
    },
    filters: function(params, callback, abortCallback) {
      module.api.request(module.api.filters_path, 'get', params, callback, abortCallback);
    }
  };;

  module.version = '4.8.2';;


  if (!Array.prototype.find) {
  Array.prototype.find = function(predicate) {
    if (this == null) {
      throw new TypeError('Array.prototype.find called on null or undefined');
    }
    if (typeof predicate !== 'function') {
      throw new TypeError('predicate must be a function');
    }
    var list = Object(this);
    var length = list.length >>> 0;
    var thisArg = arguments[1];
    var value;

    for (var i = 0; i < length; i++) {
      value = list[i];
      if (predicate.call(thisArg, value, i, list)) {
        return value;
      }
    }
    return undefined;
  };
}

var helpers = {
    checkArguments: function(func, required, args) {
      if (args.length != required) {
        throw func + ' function requires ' + required + ' arguments (' + args.length + ' given).';
      }
    },
    serialize: function(obj, prefix) {
      var str = [];
      for(var p in obj) {
        if (obj.hasOwnProperty(p)) {
          var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
          if(typeof v == 'object') {
            if(v instanceof Array) {
              str.push(encodeURIComponent(k) + '=' + encodeURIComponent(v.join(',')));
            } else {
              str.push(this.serialize(v, k));
            }
          } else {
            str.push(encodeURIComponent(k) + "=" + encodeURIComponent(v));
          }
        }
      }
      return str.join("&");
    },
    merge: function(target, src) {
      var self = this;
      var array = Array.isArray(src);
      var dst = array && [] || {};

      if (array) {
        target = target || [];
        src.forEach(function(e, i) {
          if (typeof dst[i] === 'undefined') {
            dst[i] = e;
          } else if (typeof e === 'object') {
            dst[i] = self.merge(target[i], e);
          } else {
            if (target.indexOf(e) === -1) {
              dst.push(e);
            }
          }
        });
      } else {
        if (target && typeof target === 'object') {
          Object.keys(target).forEach(function (key) {
            dst[key] = target[key];
          });
        }
        Object.keys(src).forEach(function (key) {
          if (typeof src[key] !== 'object' || !src[key]) {
            dst[key] = src[key];
          }
          else {
            if (!target[key]) {
              dst[key] = src[key];
            } else {
              dst[key] = self.merge(target[key], src[key]);
            }
          }
        });
      }

      return dst;
    },
    in: function(string, array){
      var i;
      for(i = 0; i < array.length; i++) {
        if(array.indexOf(string) >= 0) {
          return true;
        } else {
          return false;
        }
      }
    },
    findObjectByPropertyName: function (collection, type) {
      var result;

      collection.forEach(function (parentType) {
        Object.keys(parentType).forEach(function (parentTypeKey) {
          if (parentTypeKey  === type) result = parentType[parentTypeKey];
        })
      });

      return result;
    },
    intersection: function(x, y) {
      var ret = [];
      for (var i = 0; i < x.length; i++) {
        for (var z = 0; z < y.length; z++) {
          if (x[i] == y[z]) {
            ret.push(x[i]);
            break;
          }
        }
      }
      return ret;
    },
    contains: function(array1, array2, callback) {
      for(var it = 0; array1.length > it; it++) {
        if(helpers.in(array1[it], array2)) {
          callback();
          break;
        }
      }
    },
    all: function (array1, array2) {
      var result = true;

      for(var i = 0; i < array1.length; i++) {
        if (array2.indexOf(array1[i]) === -1) {
          result = false
        }
      }

      return result;
    },
    asyncLoad: function(scriptUrl, type, id) {
      if(document.body) {
        var scriptType = type || 'text/javascript';
        var script = document.createElement('script');
        if ( id ) { script.id = id; }
        script.async = true;
        script.type = scriptType;
        script.src = scriptUrl;
        document.body.appendChild(script);
      } else {
        setTimeout(function(){ helpers.asyncLoad(scriptUrl, type, id); }, 250);
      }
    },
    loadWebfonts: function() {
      window.WebFontConfig = {
        google: { families: [ 'Open+Sans:600,400:latin' ] }
      };
    },
    calculateDistance: function(location1, location2) {
      var R = 6371; 
      var dLat = this.deg2rad(location1[0] - location2[0]);  
      var dLon = this.deg2rad(location1[1] - location2[1]);
      var a =
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(this.deg2rad(location1[0])) * Math.cos(this.deg2rad(location2[0])) *
        Math.sin(dLon/2) * Math.sin(dLon/2);

      var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
      var d = R * c; 
      return d;
    },
    deg2rad: function(deg) {
      return deg * (Math.PI/180);
    },
    pointType: function(point, currentTypes) {
      var subtypes = easyPackConfig.points.subtypes;

      if (subtypes.length > 0 && subtypes[0] !== undefined) {
        for (var i = 0; i < subtypes.length; i++) {
          var subtype = subtypes[i];
          if (helpers.in(subtype, point.type)) {
            return t(subtype + '_short');
          }
        }
      }

      if (helpers.in('allegro_courier', point.type) && currentTypes[currentTypes.length - 1] === 'allegro_courier') {
        return t('allegro_courier_name');
      } else if (helpers.in('pok', point.type) || helpers.in('pop', point.type)) {
        return t('pok_name_short');
      } else if (helpers.in('avizo', point.type)) {
        return t('avizo_name');
      } else if (helpers.in('parcel_locker', point.type)) {
        return t('parcel_locker_name');
      } else {
        return '';
      }
    },
    uniqueElementInArray: function (value, index, self) {
        return self.indexOf(value) === index;
    },
    pointName: function(point, currentTypes) {
      var subtypes = easyPackConfig.points.subtypes;

      if (subtypes.length > 0 && subtypes[0] !== undefined) {
        for(var i = 0; i < subtypes.length; i++) {
          var subtype = subtypes[i];
          if(helpers.in(subtype, point.type)) {
            return t(subtype);
          }
        }
      }

      if(helpers.in('allegro_courier', point.type) && currentTypes[currentTypes.length-1] === 'allegro_courier') {
        return t('allegro_courier_name');
      } else if(helpers.in('pok', point.type) || helpers.in('pop', point.type)) {
        return t('pok_name');
      } else if(helpers.in('avizo', point.type)) {
        return t('avizo_name');
      } else if(helpers.in('parcel_locker', point.type)) {
        return t('parcel_locker_name') + ' ' + point.name;
      } else {
        return point.name;
      }
    },
    openingHours: function(hours){
      if(hours !== null) {
        var formattedHours = hours.split(',');
        return formattedHours.join(', ').replace('PT', 'PT ').replace('SB', 'SB ').replace('NIEDZIŚW', 'NIEDZIŚW ');
      }
    },
    assetUrl: function(url) {
      if(easyPackConfig.assetsServer && url.indexOf('http') == -1) {
        return easyPackConfig.assetsServer + url;
      } else {
        return url;
      }
    },
    routeLink: function(initialLocation, pointLocation) {
      var initialLocationString = initialLocation === null ? '' : initialLocation[0] + ',' + initialLocation[1];
      return 'https://www.google.com/maps/dir/' + initialLocationString + '/' + pointLocation.latitude + ',' + pointLocation.longitude;
    },
    hasCustomMapAndListInRow: function() {
      return easyPackConfig.customMapAndListInRow.enabled;
    },
    getPaginationPerPage : function() {
      return easyPackConfig.customMapAndListInRow.itemsPerPage;
    }
  };

var configure = function (userConfig) {
    window.easyPackUserConfig = userConfig;
    if(easyPackConfig.region === undefined) {
      easyPackConfig.region = userConfig.defaultLocale;
    }

    var instance = userConfig.instance || userConfig.defaultLocale || easyPackConfig.defaultLocale;
    easyPackConfig = helpers.merge(easyPackConfig, instanceConfig[instance] || {});

    easyPackConfig = helpers.merge(easyPackConfig, userConfig);

    var points_plo_index = easyPackConfig.points.types.indexOf("parcel_locker_only");
    if (-1 !== points_plo_index) {
      easyPackConfig.points.types[points_plo_index] = "parcel_locker";
    }
    var map_plo_index = easyPackConfig.map.initialTypes.indexOf("parcel_locker_only");
    if (-1 !== map_plo_index) {
      easyPackConfig.map.initialTypes[map_plo_index] = "parcel_locker";
    }

    setUrls(['infoboxLibraryUrl', 'markersUrl', 'iconsUrl', 'loadingIcon'], easyPackConfig);
    setUrls(['typeSelectedIcon', 'typeSelectedRadio', 'closeIcon', 'selectIcon', 'detailsIcon', 'pointerIcon',
      'tooltipPointerIcon', 'mapIcon', 'listIcon', 'pointIcon', 'pointIconDark'], easyPackConfig.map);
    var i;
    for(i = 0; i < easyPackConfig.map.clusterer.styles.length; i++) {
      var style = easyPackConfig.map.clusterer.styles[i];
      setUrls(['url'], style);
    }
  };

  var setUrls = function(urls, object) {
    var i;
    for(i = 0; i < urls.length; i++ ) {
      var url = urls[i];
      object[url] = helpers.assetUrl(object[url]);
    }
  };

  var ajax = function(url, method, callback) {
    helpers.checkArguments('ajax()', 3, arguments);

    var client = new ajax.client();
    client.open(method,  url);
    client.onreadystatechange = function ()
    {
      if (client.readyState == 4 && client.status == 200) {
        callback(JSON.parse(client.responseText));
      }
    };
    client.send(null);
    return client;
  };

  ajax.client = function() {
    if (window.XMLHttpRequest) {
      return new XMLHttpRequest();
    } else if (window.ActiveXObject) {
      return new ActiveXObject('Microsoft.XMLHTTP');
    } else {
      throw 'Ajax not supported.';
    }
  };

var t = function(key) {
    var translation = easyPackLocales[easyPack.locale][key];
    if ( translation ) {
      return translation;
    } else {
      return key;
    }
  };

var loadGoogleMapsApi = function() {
  if ( !easyPack.googleMapsApi.initialized ) {
    easyPack.googleMapsApi.initialized = true;
    helpers.asyncLoad('https://maps.googleapis.com/maps/api/js?v=3.exp&callback=easyPack.googleMapsApi.initialize&libraries=places&force=canvas&key=' + easyPackConfig.map.googleKey);
  }
};
;

  var typesHelpers = {
  getExtendedCollection: function () {
    return easyPackConfig.extendedTypes || []
  },
  isArrayContaintsPropWithSearchValue: function (collection, filter, propName, expectedValue, childName) {
    if (collection === undefined) return false;
    if (!collection.length) return false;

    var self = this;
    var result = false;

    collection.forEach(function (parentType) {
      Object.keys(parentType).forEach(function (parentTypeKey, index) {
        if (parentTypeKey === filter && parentType[parentTypeKey][propName] === expectedValue && result === false) {
          result = true;
        }
        if (index === Object.keys(parentType).length -1 && parentType[parentTypeKey][childName] && result === false) {
          result = self.isArrayContaintsPropWithSearchValue(parentType[parentTypeKey][childName], filter, propName, expectedValue, childName)
        }
      });
    });

    return result;

  },
  seachInArrayOfObjectsKeyWithCondition : function (collection, objectContaintPropertyName, expectedValue, childName) {
    var result = [];
    if (collection === undefined) return result;
    if (!collection.length) return result;
    var self = this;

    collection.forEach(function (parentType) {
      Object.keys(parentType).forEach(function (parentTypeKey, index) {
        if (parentType[parentTypeKey][objectContaintPropertyName] === expectedValue) {
          result.push(parentTypeKey)
        }
        if (index === Object.keys(parentType).length -1 && parentType[parentTypeKey][childName]) {
          result = result.concat(self.seachInArrayOfObjectsKeyWithCondition(parentType[parentTypeKey][childName], objectContaintPropertyName, expectedValue,childName));
        }
      });
    });

    return result;
  },
  findParentObjectsByChildType: function (collection, type) {
    var result;

    collection.forEach(function (parentType) {
      Object.keys(parentType).forEach(function (parentTypeKey) {
        if (parentType[parentTypeKey].childs) {
          parentType[parentTypeKey].childs.filter(function (value) {
            if (value === type) result = parentType[parentTypeKey];
          });
        }
      })
    });

    return result;
  },
  isParent: function (type, collection) {
    var self = this,
      result = false;

    collection.forEach(function (parentType) {
      if (parentType !== undefined ) {
        Object.keys(parentType).forEach(function (parentTypeKey, index) {
          if (parentType[parentTypeKey].childs && type === parentTypeKey) {
             result = true;
          }
        })
      }
    });

    return result
  },
  getUniqueValues: function (array) {
    var result = [];
    for(var i = 0; i < array.length; i++) {
      if(result.indexOf(array[i]) === -1) {
        result.push(array[i]);
      }
    }
    return result;
  },
  getAllAdditionalTypes: function (collection) {
    var result = [];
    if (collection === undefined) return result;
    if (!collection.length) return result;
    var self = this,
        objectContaintPropertyName = 'additional',
        childName = 'childs';

    collection.forEach(function (parentType) {
      Object.keys(parentType).forEach(function (parentTypeKey, index) {
        if (parentType[parentTypeKey][objectContaintPropertyName]) {
          result = result.concat(parentType[parentTypeKey][objectContaintPropertyName])
        }
        if (index === Object.keys(parentType).length -1 && parentType[parentTypeKey][childName]) {
          result = result.concat(self.seachInArrayOfObjectsKeyWithCondition(parentType[parentTypeKey][childName], objectContaintPropertyName, childName))
        }
      });
    });

    return self.getUniqueValues(result);
  },
  any: function(arrayFirst, arraySecond) {
    return arrayFirst.some(function (t) { return arraySecond.some(function (t2) { return t === t2 }) })
  },
  getObjectForType: function (type, collection) {
      var self = this,
        result = null;

      collection.forEach( function (item) {
          Object.keys(item).forEach(function (key) {
              if (key === type) {
                result = item[key];
              }
              if (item[key].childs !== undefined && result === null) {
                  self.getObjectForType(type, item[key].childs)
              }
          })
      });

      return result;
  },
  isAllChildSelected: function (type, selectedTypes,object) {
    if (object === undefined || object.childs === undefined) return false;
    var result = true;
    var self = this;
    object.childs.some(function (value, index) {
      if ( value[type] === undefined && object.childs.length === index -1) object.childs.unshift(JSON.parse('{'+ '"' + self.getNameForType(type) + '"' + ': { "enabled": "true"}}'));
    });
    object.childs.forEach(function (child) {
      Object.keys(child).forEach(function (childType) {
        if (!helpers.in( self.getNameForType(childType), selectedTypes)) result = false;
      })
    });
    return result
  },
  in: function(string, array) {
    var newArray = [];
    for(var i=0; i < array.length; i++) {
      newArray[i] = (array[i] || '').replace('_only', '');
    }
    return newArray.indexOf(string.valueOf()) >= 0
  },
  isNoOneChildSelected: function (type, selectedTypes,object) {
    if (object === undefined || object.childs === undefined) return false;
    var result = true;
    var self = this;
    object.childs.some(function (value, index) {
      if ( value[type] === undefined && object.childs.length === index -1) object.childs.unshift(JSON.parse('{'+ '"' + self.getNameForType(type) + '"' + ': { "enabled": "true"}}'));
    });
    object.childs.forEach(function (child) {
      Object.keys(child).forEach(function (childType) {
        if (helpers.in( self.getNameForType(childType), selectedTypes)) result = false;
      })
    });
    return result
  },
  getAllChildsForGroup: function (type, collection) {
    var self = this,
        result = [];

    collection.forEach(function (parentType) {
      if (parentType !== undefined ) {
        Object.keys(parentType).forEach(function (parentTypeKey, index) {
          if (parentType[parentTypeKey].childs && self.getRealNameForType(type) === parentTypeKey) {
            parentType[parentTypeKey].childs.forEach(function (value) {
              result = result.concat(Object.keys(value).map( function (key) { return self.getNameForType(key) }))
            })
          }
        })
      }
    });

    return result
  },
  getParentIfAvailable: function (childType, collection) {
    var result = null,
      self = this;

    collection.forEach(function (parentType) {
      Object.keys(parentType). forEach( function (key) {
        if (self.getNameForType(key) === childType) {
          result = key;
        }
        if (parentType[key].childs !== undefined && result === null)
          parentType[key].childs.forEach( function (childElm) {
            if (self.in(childType, Object.keys(childElm))) {
                result = key;
            }
          })
      })

    });

    return result
  },
  isOnlyAdditionTypes : function (types, collection) {
    var self = this,
        result = true;
    types.some( function (type) { if ( !helpers.in(type, self.getAllAdditionalTypes(collection))) result = false  })
    return result;
  },
  getNameForType: function (type) {
    switch (type) {
      case 'parcel_locker': return 'parcel_locker_only';
      default: return type
    }
  },
  getRealNameForType: function (type) {
    switch (type) {
      case 'parcel_locker_only': return 'parcel_locker';
      default: return type
    }
  },
  sortByPriorities: function (types) {
    var self = this;
    var result = types.sort( function(a, b){
        if (self.getPriorityForTypes(a) > self.getPriorityForTypes(b))
          return -1;
        if (self.getPriorityForTypes(a) < self.getPriorityForTypes(b))
          return 1;
        return 0;
    })
    return result
  },
  getPriorityForTypes: function (type) {
    switch (type) {
      case 'parcel_locker': return 1;
      case 'pop': return 2;
      case 'pok': return 3;
      case 'parcel_locker_superpop': return 9;
      default: return 0
    }
  }
};;

  var infoWindow = function(marker, params, options, response, popUpCallback, widget, isMobile) {
    this.params = params;
    this.marker = marker;
    this.map = params.map;
    this.popUpCallback = popUpCallback;
    this.placeholder = params.placeholder;
    this.placeholderId = params.placeholderId;
    this.style = params.style;
    this.closeInfoBox = params.closeInfoBox;
    this.setPointDetails = params.setPointDetails;
    this.initialLocation = params.initialLocation;
    this.pointDetails = params.pointDetails;
    this.infoBoxObj = null;
    this.widget = widget;
    this.response = response;
    this.isMobile = isMobile;
    this.prepareContent(response);

    var defaultOptions = {
      content: this.windowElement,
      disableAutoPan: false,
      maxWidth: 0,
      pixelOffset: new google.maps.Size(-170, -16),
      zIndex: null,
      closeBoxMargin: "30px",
      closeBoxURL: easyPackConfig.map.closeIcon,
      infoBoxClearance: new google.maps.Size(1, 1),
      isHidden: false,
      pane: "floatPane",
      enableEventPropagation: false,
      alignBottom: true,
    };

    this.options = helpers.merge(defaultOptions, options);

    return this;
  };

  infoWindow.prototype = {
    open: function(){
      var self = this;
      self.widget.infoWindow = this;
      if(this.params.infoBox !== undefined) { this.params.infoBox.close(); }
      this.infoBoxObj = new InfoBox(this.options);
      this.params.setInfoBox(this.infoBoxObj);
      this.infoBoxObj.open(this.map, this.marker);
      this.infoBoxObj.addListener('closeclick', function(e){
        self.params.clearDetails();
        self.params.setPointDetails(null);
      });

      setTimeout(function () {
        document.querySelector('div.infoBox').querySelector('img').addEventListener('touchstart', function () {
          self.close()
        });
      }, 250);
    },
    close: function() {
      this.infoBoxObj.close();
      var modal = document.getElementById('widget-modal');
      if(modal !== null) {
        modal.className = 'widget-modal hidden';
      }
    },
    prepareContent: function(response) {
      var self = this;

      this.windowElement = document.createElement('div');
      this.windowElement.className = 'info-window';
      this.windowElement.style.background = 'url("' + easyPackConfig.map.pointerIcon + '") no-repeat center bottom';

      this.content = document.createElement('div');
      this.content.className = 'content';

      this.pointWrapper = document.createElement('div');
      this.pointWrapper.className = 'point-wrapper';

      this.title = document.createElement('h1');

      this.title.innerHTML = helpers.pointName(this.marker.point, self.widget.currentTypes);

      this.address = document.createElement('p');
      this.address.className = 'address';
      var self = this;
      var addressParts = easyPackConfig.addressFormat.replace(/{(.*?)}/g, function(match, content) {
        var elem = match.replace('{', '').replace('}', '');
        var data = response.address_details[elem] === null ? '' : response.address_details[elem];
        if(data === undefined) {
          data = self.marker.point[elem];
        }

        return data;
      });

      if(easyPackConfig.descriptionInWindow) {
        this.address.innerHTML += response.location_description + '<br>';
      }

      this.address.innerHTML += addressParts;

      if(response.phone_number !== undefined && response.phone_number !== null) {
        this.phone = document.createElement('p');
        this.phone.className = 'phone';
        this.phone.innerHTML = t('phone_short') + response.phone_number;
      }

      if(response.name !== undefined && response.name !== null && helpers.in('pok', response.type)) {
        this.name = document.createElement('p');
        this.name.className = 'name';
        this.name.innerHTML = response.name;
      }

      this.linksElement = document.createElement('div');
      this.linksElement.className = 'links';

      this.routeLink = document.createElement('a');
      this.routeLink.className = 'route-link';

      var ininitialLocationCheck = self.params.locationFromBrowser ? self.initialLocation : null;
      this.routeLink.href = helpers.routeLink(ininitialLocationCheck, response.location);

      this.routeLink.ontouchstart = function (e) {
        window.open(e.target.href, "_new");
      }

      this.routeLink.target = '_new';
      this.routeLink.innerHTML = t('plan_route');
      this.routeLink.style.background = 'url("' + easyPackConfig.map.pointIcon + '") no-repeat';

      this.detailsLink = document.createElement('a');
      this.detailsLink.className = 'details-link';
      this.detailsLink.href = '#';
      this.detailsLink.innerHTML = t('details');
      this.detailsLink.style.background = 'url("' + easyPackConfig.map.detailsIcon + '") no-repeat';

      function detailsOnClick (e) {
        e.preventDefault();
        self.pointDetails = new pointDetails(self.marker, { setPointDetails : self.setPointDetails, pointDetails : self.pointDetails, closeInfoBox : self.closeInfoBox, style : self.style, map : self.map, placeholder : self.placeholder, initialLocation : self.initialLocation, isMobile : self.params.isMobile, widget: self.widget }, response);
        self.widget.detailsObj = self.pointDetails;
        self.pointDetails.render();
      }

      this.detailsLink.onclick = detailsOnClick;
      this.detailsLink.ontouchstart = detailsOnClick;

      this.selectLink = document.createElement('a');
      this.selectLink.className = 'select-link';
      this.selectLink.href = '#';
      this.selectLink.innerHTML = t('select');
      this.selectLink.style.background = 'url("' + easyPackConfig.map.selectIcon + '") no-repeat';

      function onClickFunc (e) {
        e.preventDefault();
        self.popUpCallback(response);
        self.close();
      }

      this.selectLink.ontouchstart = onClickFunc;
      this.selectLink.onclick = onClickFunc;

      this.pointWrapper.appendChild(this.title);
      this.pointWrapper.appendChild(this.address);

      if(this.phone !== undefined) {
        this.pointWrapper.appendChild(this.phone);
      }

      if(this.name !== undefined) {
        this.pointWrapper.appendChild(this.name);
      }

      this.content.appendChild(this.pointWrapper);

      this.linksElement.appendChild(this.routeLink);
      this.linksElement.appendChild(this.detailsLink);
      if(typeof self.popUpCallback === 'function') {
        this.linksElement.appendChild(this.selectLink);
      }

      if ((helpers.in('pop',response.type) || helpers.in('pok',response.type)) && response.opening_hours !== '') {
        this.openHoursLabel = document.createElement('p');
        this.openHoursLabel.className = 'open-hours-label';
        this.openHoursLabel.innerHTML = t('openingHours') + ':';
        this.openHours = document.createElement('p');
        this.openHours.className = 'open-hours';
        this.openHours.innerHTML = response.opening_hours;
        this.pointWrapper.appendChild(this.openHoursLabel);
        this.pointWrapper.appendChild(this.openHours);
      }

      this.content.appendChild(this.linksElement);

      this.windowElement.appendChild(this.content);
    },
    rerender: function() {
      this.detailsLink.innerHTML = t('details');
      this.selectLink.innerHTML = t('select');
      this.routeLink.innerHTML = t('plan_route');
      this.title.innerHTML = helpers.pointName(this.marker.point, this.widget.currentTypes);
      if(this.response.phone_number !== undefined && this.response.phone_number !== null) {
        this.phone.innerHTML = t('phone_short') + this.response.phone_number;
      }
    }
  };
;

  var modal = function(options) {
    this.options = options;

    this.render();
    return this;
  };

  modal.prototype = {
    render: function() {
      var modal = document.createElement('div');
      modal.className = 'widget-modal';
      modal.id = 'widget-modal';
      modal.style.width = this.options.width + 'px';
      modal.style.height = this.options.height + 'px';
      modal.style.marginLeft = -(this.options.width/2) + 'px';
      var topbar = document.createElement('div');
      topbar.className = 'widget-modal__topbar';
      var closeBtn = document.createElement('div');
      closeBtn.innerHTML = '&#10005';
      closeBtn.className = 'widget-modal__close';
      closeBtn.addEventListener('click', function() {
        modal.className += ' hidden';
      });
      topbar.appendChild(closeBtn);
      modal.appendChild(topbar);
      var element = document.createElement('div');
      element.id = 'widget-modal__map';
      modal.appendChild(element);
      document.body.appendChild(modal);
    }
  };;

  var listWidget = function(params) {
    this.params = params;
    this.build();
  };

  listWidget.prototype = {
    build: function() {
      this.listElement = document.createElement('div');
      this.listElement.className = 'list-widget';

      this.listWrapper = document.createElement('div');
      this.listWrapper.className = 'list-wrapper';

      this.scrollBox = document.createElement('div');
      this.scrollBox.id = 'scroll-box';
      this.scrollBox.className = 'scroll-box';

      this.viewport = document.createElement('div');
      this.viewport.className = 'viewport';

      this.overview = document.createElement('div');
      this.overview.className = 'overview';

      this.list = document.createElement('ul');

      this.overview.appendChild(this.list);
      this.viewport.appendChild(this.overview);
      this.scrollBox.appendChild(this.viewport);
      this.listWrapper.appendChild(this.scrollBox);
      this.listElement.appendChild(this.listWrapper);
    },
    addPoint: function(point, clickFunction, currentType, filters) {
      var item = document.createElement('li'),
          listIcon = point.dynamic ? point.icon : module.points.listIcon(point, filters || this.params.currentTypes);
      var link = document.createElement('a');
      link.href = '#' + point.name;
      link.className = 'list-point-link';

      link.style.backgroundImage = 'url(' + listIcon + ')';

      var machineName = document.createElement('div');
      var self = this;
      machineName.className = 'title';

      machineName.innerHTML = easyPackConfig.listItemFormat[0].replace(/{(.*?)}/g, function(match, content) {
        if (content === 'name') return helpers.pointName(point, self.params.currentTypes)
        return content.split('.').reduce(function(obj,i) {return obj[i]}, point)
      });

      var machineAddress = document.createElement('div');
      machineAddress.className = 'address';

      if(point.address_details !== undefined) {
        machineAddress.innerHTML = easyPackConfig.listItemFormat.filter(function (item, index) {
          return index > 0;
        }).map(function (e) {
          return e.replace(/{(.*?)}/g, function(match, content) {
            if (content === 'name') return helpers.pointName(point, self.params.currentTypes)
            if (content.split('.').reduce(function(obj,i) {return obj[i]}, point) === null) return ''
            return content.split('.').reduce(function(obj,i) {return obj[i]}, point)
          }) + '<br>';
        }).join('');
      } else {
        machineAddress.innerHTML = point.address.line1 + '&nbsp;';
      }

      link.appendChild(machineName);
      link.appendChild(machineAddress);
      link.onclick = function(e){
        e.preventDefault();
        clickFunction();
      };

      item.appendChild(link);

      this.list.appendChild(item);
    },
    render: function(placeholder) {
      placeholder.appendChild(this.listElement);
    },
    clear: function() {
      this.list.innerHTML = '';
    }
  };;

  var paginatedListWidget = function(params) {
    this.params = params;
    this.build();
  };

  paginatedListWidget.prototype = {
    build: function() {
      this.listElement = document.createElement('div');
      this.listElement.className = 'list-widget';

      this.listWrapper = document.createElement('div');
      this.listWrapper.className = 'list-wrapper';

      this.list = document.createElement('ul');

      this.listWrapper.appendChild(this.list);
      this.listElement.appendChild(this.listWrapper);
      if (helpers.hasCustomMapAndListInRow()) {
        this.paginationWrapper = document.createElement('div');
        this.paginationWrapper.className = 'pagination-wrapper';
        this.paginationList = document.createElement('ul');
        this.paginationWrapper.appendChild(this.paginationList);
        this.listElement.appendChild(this.paginationWrapper);
      }
    },
    addPoint: function(point, clickFunction, currentType, filters) {
      var item = document.createElement('li'),
          listIcon = point.dynamic ? point.icon : module.points.listIcon(point, filters || this.params.currentTypes);
      var link = document.createElement('div');
      link.className = 'row';
      link.style.backgroundImage = 'url(' + listIcon + ')';

      var title = document.createElement('div');
      title.className = 'title';

      var name = document.createElement('div');
      name.className = 'col-name';
      name.innerHTML = point.name;

      var actions = document.createElement('div');
      actions.className = 'actions';

      var linkShowOnMap = document.createElement('a');
      linkShowOnMap.className = 'details-show-on-map';
      linkShowOnMap.innerHTML = t('show_on_map');
      linkShowOnMap.href = '#' + point.name;
      linkShowOnMap.onclick = function(e){
        e.preventDefault();
        clickFunction();
      };
      actions.appendChild(linkShowOnMap);

      if (easyPackConfig.customDetailsCallback) {
        var linkDetails = document.createElement('a');
        linkDetails.className = 'details-show-more';
        linkDetails.innerHTML = t('more') + ' ➝';
        linkDetails.href = '#' + point.name;
        linkDetails.onclick = function(e){
          e.preventDefault();
          easyPackConfig.customDetailsCallback(point)
        };
        actions.appendChild(linkDetails);
      }

      var more = document.createElement('div');
      more.className = 'col-actions';
      more.appendChild(actions);

      var pointType = document.createElement('div');
      pointType.className = 'col-point-type';
      pointType.innerHTML = helpers.pointType(point, this.params.currentTypes);

      var pointTypeAndName = document.createElement('div');
      pointTypeAndName.className = 'col-point-type-name';
      pointTypeAndName.innerHTML = helpers.pointType(point, this.params.currentTypes) + '</br>' + point.name;

      var columnCity = document.createElement('div');
      columnCity.className = 'col-city';

      var columnStreet = document.createElement('div');
      columnStreet.className = 'col-sm col-street';

      var columnAddress = document.createElement('div');
      columnAddress.className = 'col-sm col-address';

      columnCity.innerHTML = (point.address_details['city'] === null)? '' : point.address_details['city'];
      columnStreet.innerHTML = this.getAddress(point, ["street", "building_number"]).replace(',', '').replace('<br>', '');
      columnAddress.innerHTML = this.getAddress(point, ["street", "building_number", "post_code", "city"]);

      link.appendChild(pointType);
      link.appendChild(pointTypeAndName);
      link.appendChild(columnCity);
      link.appendChild(columnStreet);
      link.appendChild(columnAddress);
      link.appendChild(name);
      link.appendChild(more);
      item.appendChild(link);

      this.list.appendChild(item);
    },
    getAddress: function(point, contents) {
      var addressParts = easyPackConfig.addressFormat.replace(/{(.*?)}/g, function(match, content) {

        if (-1 !== contents.indexOf(content)) {
          var elem = match.replace('{', '').replace('}', '');
          var data;

          if (point.address_details !== undefined) {
            data = point.address_details[elem] === null ? '' : point.address_details[elem];
          }

          if (data === undefined) {
            data = point[elem];
          }

          if (data) {
            return data;
          }

          return '';
        } else {
          return '';
        }
      });

      return addressParts;
    },
    paginate: function(page,perPage) {
      var elements = this.list.getElementsByTagName("li");

      Object.keys(elements).forEach(function (value, index) {
          if (index < (perPage * (page -1)) || (index >= perPage*page)) {
              elements[value].setAttribute('class', 'hidden');
          } else {
              elements[value].setAttribute('class', '');
          }
      });

      this.renderPagination(page,perPage,elements);
    },
    renderPagination: function(page,perPage,elements) {
      this.clearPagination();
      var self = this;

      page = parseInt(page);
      var interval = 2;

      if (elements.length/perPage > 1) {
        var totalPages = Math.ceil(elements.length / perPage);

        for (var i = 1; i <= totalPages; i++) {
          var item = document.createElement('li');
          item.innerHTML = i;
          item.onclick = function (e) {
            e.preventDefault();
            self.paginate(this.innerHTML, perPage);
          };

          if (i === page) {
            item.className = "current";
          } else {
            item.className = "pagingItem";
          }

          if (i === 1) {
            var prev = document.createElement('li');
            prev.innerHTML = t('prev');
            prev.className = 'pagingPrev';

            if (i === page) {
              prev.className = 'pagingPrev disabled';
            } else {
              prev.onclick = function (e) {
                e.preventDefault();
                self.paginate((page-1), perPage);
              }
            }
            this.paginationList.appendChild(prev);
          }

          if (elements.length < 5) {
            this.paginationList.appendChild(item);
          } else {
            if (((i > (page-interval)) && (i < (page+interval)))
              || ((page <= interval+2) && (i <= interval+2))
              || (i >= totalPages-(interval+2)) && (page >= totalPages-(interval+2))) {
              this.paginationList.appendChild(item);
            } else {
              var empty = document.createElement('li');
              empty.className = 'pagingSeparator';
              empty.innerHTML = '...';

              if (i === 1) {
                this.paginationList.appendChild(item);
                this.paginationList.appendChild(empty);
              }

              if (i === totalPages) {
                this.paginationList.appendChild(empty);
                this.paginationList.appendChild(item);
              }
            }
          }

          if (i === totalPages) {
            var next = document.createElement('li');
            next.innerHTML = t('next');
            next.className = 'pagingNext';

            if (i === page) {
              next.className = 'pagingNext disabled';
            } else {
              next.onclick = function (e) {
                e.preventDefault();
                self.paginate((page+1), perPage);
              }
            }
            this.paginationList.appendChild(next);
          }
        }
      }
    },
    render: function(placeholder) {
      placeholder.appendChild(this.listElement);
    },
    clear: function() {
      this.list.innerHTML = '';
    },
    clearPagination: function() {
      this.paginationList.innerHTML = '';
    }
  };;

  var statusBar = function(widget) {
    this.widget = widget;
    this.build();
  };

  statusBar.prototype = {
    build: function() {
      this.statusElement = document.createElement('div');
      this.statusElement.className = 'status-bar';

      this.statusText = document.createElement('span');
      this.statusText.className = 'current-status';

      this.statusElement.appendChild(this.statusText);
    },
    render: function(placeholder) {
      placeholder.appendChild(this.statusElement);
    },
    clear: function() {
      this.statusText.innerHTML = '';
    },
    hide: function() {
      this.statusElement.className = 'status-bar status-bar--hidden';
    },
    update: function(loaded, total) {
      if(loaded !== 0 && loaded <= total) {
        this.statusElement.className = 'status-bar';
        this.statusText.innerHTML = loaded + ' ' + t('of') + ' ' + total + ' ' + t('points_loaded');
      }
    }
  };;

  var languageBar = function(widget, module, placeholder) {
    this.widget = widget;
    this.module = module;
    this.placeholder = placeholder;
    this.build();
  };

  languageBar.prototype = {
    build: function() {
      var self = this;
      this.statusElement = document.createElement('div');
      this.statusElement.className = 'language-bar';

      this.statusText = document.createElement('span');
      this.statusText.className = 'current-status';

      var langArray = [];

      if(self.module.userConfig.languages !== undefined) {
        for (var i = 0, len = self.module.userConfig.languages.length; i < len; i++) {
          langArray.push(self.module.userConfig.languages[i]);
        }
      } else {
        for (var property in easyPackLocales) {
          if (easyPackLocales.hasOwnProperty(property)) {
            if(property !== 'pl-PL') {
              langArray.push(property);
            }
          }
        }
      }

      var selectList = document.createElement('select');
      selectList.id = 'langSelect';
      selectList.addEventListener('change', function() {
        self.module.userConfig.defaultLocale = this.value;
        easyPack.locale = this.value;
        self.module.init(self.module.userConfig);
        self.widget.refreshPoints();
        if(self.widget.infoWindow !== undefined) {
          self.widget.infoWindow.rerender();
        }
        self.widget.searchObj.rerender();
        self.widget.typesFilterObj.rerender();
        self.widget.viewChooserObj.rerender();
        if(self.widget.detailsObj !== null) {
          self.widget.detailsObj.rerender();
        }
      });
      this.statusText.appendChild(selectList);

      for (var i = 0; i < langArray.length; i++) {
        var option = document.createElement('option');
        option.value = langArray[i];
        option.text = langArray[i].toUpperCase();
        selectList.appendChild(option);
      }

      this.statusElement.appendChild(this.statusText);
    },
    render: function(placeholder) {
      placeholder.appendChild(this.statusElement);
    }
  };
;

  var autocompleteService = {

  searchObj: null,
  mapObj: null,
  placesService: null,
  params: null,
  maxPointsResult: 0,
  service: function (searchObj, mapObj, params) {
    this.searchObj = searchObj;
    this.mapObj = mapObj;
    this.params = params;
    this.maxPointsResult = easyPackConfig.searchPointsResultLimit
    this.placesService = new google.maps.places.PlacesService(this.mapObj);

    var autocomplete = new google.maps.places.AutocompleteService(),
        self = this;

    this.searchObj.searchInput.addEventListener('keyup', function (e) {
      var searchWait,
          listvillages = document.getElementById('listvillages');
      clearTimeout(searchWait);


      if (this.value.length > 2 && e.which !== 13) {

        if (listvillages) {
          var elements = listvillages.getElementsByClassName("place");

          while (elements[0]) {
            elements[0].parentNode.removeChild(elements[0]);
          }

          elements = listvillages.getElementsByClassName("point");

          while (elements[0]) {
            elements[0].parentNode.removeChild(elements[0]);
          }

        }

        searchWait = setTimeout(function(self, value) {
          input = value.replace(/ul\.\s?/i, '');
          inputValue = input
          if (input.length !== 0) {
            var inputWithCountry = input + " " + easyPackConfig.map.searchCountry;

            autocomplete.getPlacePredictions({input: inputWithCountry, types: ['geocode']}, self.displaySuggestions);
            self.displayPointsResults(input);
          }
        }(self, this.value), 250);
      } else {
        if (listvillages !== null) {
          listvillages.classList.add('hidden');
        }
      }

      if (e.which == 13) { self.selectFirstResult();}

    }, false);

    this.bindSearchEvents();
  },

  displaySuggestions: function(predictions, status) {
    var self = this;
    if (status != google.maps.places.PlacesServiceStatus.OK) { return;}

    var searchWidget = document.getElementsByClassName('search-widget')[0],
        listElement,
        placeId;

    if (document.getElementById('listvillages') === null) {
      listElement = document.createElement('div');

      listElement.id = 'listvillages';
      listElement.className = 'inpost-search__list hidden';
    } else {
      listElement = document.getElementById('listvillages');
      listElement.classList.remove('hidden');
    }

    var elements = listElement.getElementsByClassName("place");

    while (elements[0]) {
      elements[0].parentNode.removeChild(elements[0]);
    }

    var itemList = document.createElement('div'),
        itemQuery = document.createElement('span'),
        queryMatched = document.createElement('span'),
        itemSpan = document.createElement('span'),
        mainText = document.createTextNode(''),
        grayText = document.createTextNode('');

    itemList.className = 'inpost-search__item-list place';
    itemQuery.className = 'inpost-search__item-list--query';
    queryMatched.className = "pac-matched";
    itemSpan.appendChild(grayText);

    predictions
    .reverse()
    .forEach(function (prediction) {
      if (status === google.maps.places.PlacesServiceStatus.OK) {

        var queryMatchedClone = queryMatched.cloneNode(true),
            itemQueryClone = itemQuery.cloneNode(true),
            itemListClone = itemList.cloneNode(true);

        queryMatchedClone.textContent = prediction.terms[0].value;
        itemListClone.setAttribute('data-placeid', prediction.place_id);

        queryMatchedClone.appendChild(mainText);
        itemQueryClone.appendChild(queryMatchedClone);
        itemListClone.appendChild(itemQueryClone);
        if (prediction.terms[1] !== undefined) {
          var itemSpanClone = itemSpan.cloneNode(true);
          itemSpanClone.textContent = prediction.terms
            .slice(1)
            .map(function (item){ return item.value})
            .join(', ');
          itemListClone.appendChild(itemSpanClone);
        }
        itemListClone.addEventListener('click', function () {
          autocompleteService.searchObj.searchInput.value = this.childNodes[0].childNodes[0].innerHTML;
          if (this.childNodes[1] !== undefined) {
            autocompleteService.searchObj.searchInput.value += ', ' + this.childNodes[1].innerHTML;
          }
          var placeid = this.dataset.placeid;
          autocompleteService.setCenter(placeid);
        });
        listElement.insertAdjacentElement('afterbegin', itemListClone);
      }
    });

    searchWidget.appendChild(listElement);
  },
  onlyUnique: function (value, index, self) {
    return self.indexOf(self.find(function(e) { return e.name === value.name; })) === index;
  },
  displayPointsResults: function (input) {
    var self = this;

    var searchWidget = document.getElementsByClassName('search-widget')[0],
      listElement,
      placeId;

    if (document.getElementById('listvillages') === null) {
      listElement = document.createElement('div');

      listElement.id = 'listvillages';
      listElement.className = 'inpost-search__list hidden';
    } else {
      listElement = document.getElementById('listvillages');
      listElement.classList.remove('hidden');
    }

    var elements = listElement.getElementsByClassName("point");

    while (elements[0]) {
      elements[0].parentNode.removeChild(elements[0]);
    }

    var itemList = document.createElement('div'),
      itemQuery = document.createElement('span'),
      queryMatched = document.createElement('span'),
      itemSpan = document.createElement('span'),
      mainText = document.createTextNode(''),
      grayText = document.createTextNode('');

    itemList.className = 'inpost-search__item-list point';
    itemQuery.className = 'inpost-search__item-list--query';
    queryMatched.className = "pac-matched";
    itemSpan.appendChild(grayText);

    module.pointsToSearch.filter( function (point) {
      return point.types.some(function (t) { return self.params.currentTypes.indexOf(t) >= 0 } )
    })
    .filter(function (e) {
      return e.name.toUpperCase().indexOf(input.toUpperCase()) !== -1
    })
    .filter(this.onlyUnique)
    .slice(0, self.maxPointsResult)
    .forEach(function (point) {
      var queryMatchedClone = queryMatched.cloneNode(true),
          itemQueryClone = itemQuery.cloneNode(true),
          itemListClone = itemList.cloneNode(true);

      queryMatchedClone.textContent = point.name;

      queryMatchedClone.appendChild(mainText);
      itemQueryClone.appendChild(queryMatchedClone);
      itemListClone.appendChild(itemQueryClone);
      itemListClone.addEventListener('click', function() {
        autocompleteService.searchObj.searchInput.value = this.childNodes[0].childNodes[0].innerHTML;
        if (this.childNodes[1] !== undefined) {
          autocompleteService.searchObj.searchInput.value += ', ' + this.childNodes[1].innerHTML;
        }
        point.action()
      });
      listElement.insertAdjacentElement('beforeend', itemListClone);
    });

    searchWidget.appendChild(listElement);
  },
  bindSearchEvents: function(){
    var self = this;
    this.params.placeholderObj.addEventListener('click', function(e){
      var classElement = e.target.className,
          listvillages = document.getElementById('listvillages');
      if (listvillages !== null) {
        if (classElement !== 'form-control') {
          listvillages.classList.add('hidden');
        } else if (classElement !== 'inpost-search__item-list') {
          listvillages.classList.add('hidden');
        }
      }

    });

    this.searchObj.searchButton.addEventListener('click', function(){
      self.selectFirstResult();
    });
  },


  selectFirstResult: function(){
    if(this.mapObj.currentInfoWindow ) { this.mapObj.currentInfoWindow.close(); }

    var result = document.getElementsByClassName('inpost-search__item-list'),
      enteredText = document.getElementById('easypack-search').value,
      searchElement = null;

    for (var i = 0; i< result.length; i++) {
      var toSeachContent = result[i].childNodes[0].childNodes[0].innerHTML.toLowerCase()

      if (result[i].childNodes.length > 1) {
        toSeachContent += ', ' + result[i].childNodes[1].innerHTML.toLowerCase();
      }

      if (searchElement === null && toSeachContent.search(enteredText.toLowerCase()) === 0) {
        searchElement = result[i]
      }
    }

    var firstResult = document.getElementsByClassName('inpost-search__item-list')[0];

    if (searchElement !== null) {
      firstResult = searchElement
    }

    if(typeof firstResult != 'undefined') {
      if (firstResult.getAttribute('data-placeid') !== null) {
        this.setCenter(firstResult.dataset.placeid);
      } else {
        firstResult.click()
      }
    } else {
      this.searchObj.searchInput.value = null;
    }
    this.searchObj.searchInput.blur();
  },

  setCenter: function (placeid) {
    this.placesService.getDetails({
      placeId: placeid
    }, function(place, status) {
      autocompleteService.params.clearDetails();
      autocompleteService.params.closeInfoBox();
      if (place) {
        if (place.geometry.viewport) {
          autocompleteService.mapObj.fitBounds(place.geometry.viewport);
          if (autocompleteService.mapObj.getZoom() > easyPackConfig.map.detailsMinZoom) {
            autocompleteService.mapObj.setZoom(easyPackConfig.map.detailsMinZoom)
          }

        } else {
          autocompleteService.mapObj.setCenter(place.geometry.location);
          autocompleteService.mapObj.setZoom(easyPackConfig.map.detailsMinZoom);  
        }
      }
    });
    document.getElementById('listvillages').classList.add('hidden');
  }
};
;

  var map = function(placeholder, pointCallback, callback, module) {
    var placeholderId = placeholder,
        mapRendered = false,
        module = module,
        mapIdle = false,
        types = easyPackConfig.map.types,
        allTypes = easyPackConfig.points.types,
        currentTypes,
        location = easyPackConfig.map.defaultLocation,
        initialLocation = location,
        newPoints = [],
        markers = [],
        allMarkers = {},
        filteredMarkers = {},
        idMarkers = [],
        loaded = 0,
        allLoaded = false,
        clusterer = null,
        loadingIconWrapper = null,
        mapListRow = null,
        mapListFlex = null,
        listObj = null,
        reloadProcess = null,
        statusBarObj = null,
        languageBarObj = null,
        locationFromBrowser = null,
        viewChooserObj = null,
        typesFilterObj = null,
        infoBoxObj,
        searchObj = null,
        style = null,
        mapObj = null,
        mapElement = null,
        placeholderObj = null,
        isMobile = false,
        lockWhileMove = false,
        dynamicTypes = {},
        dynamicPoints = {},
        subTypesEnabled = false,
        pointDetailsObj;

    this.searchObj = null;
    this.detailsObj = null;
    this.pointsStorage = {};
    this.filteredPoints = {};
    this.isFilter = easyPackConfig.filters;
    this.isMobile = isMobile;
    this.allMarkers = allMarkers;

    var self = this;

    self.isMobile = isMobile;

    self.showType = function(type, types){
      if(mapIdle) {
        var oldTypes = currentTypes.slice(0);
        if ((isMobile && easyPackConfig.mobileFiltersAsCheckbox === true) || !isMobile) {
            if (typesHelpers.isParent(type, typesHelpers.getExtendedCollection())) {
                if (types !== undefined) {
                    types = types.concat(typesHelpers.getAllChildsForGroup(type, typesHelpers.getExtendedCollection()));
                } else {
                    types = typesHelpers.getAllChildsForGroup(type, typesHelpers.getExtendedCollection()) || [];
                }
            }
        } else if(isMobile && !easyPackConfig.mobileFiltersAsCheckbox) {
            clusterer.clearMarkers();
            currentTypes = [type]
        }
          if (types !== undefined) {
              types = types.concat(typesHelpers.getAllAdditionalTypes(typesHelpers.getExtendedCollection()));
          } else {
              types = typesHelpers.getAllAdditionalTypes(typesHelpers.getExtendedCollection());
          }
         if(isMobile && !easyPackConfig.mobileFiltersAsCheckbox) {
            clusterer.clearMarkers();
            currentTypes = [type]
          } else {
          if(currentTypes.indexOf(type) === -1) {
            currentTypes.push(type);
          }
          if(types !== undefined) {
            for(var i = 0; i < types.length; i++) {
              if(currentTypes.indexOf(types[i]) === -1) {
                currentTypes.push(types[i]);
              }
            }
          }
        }


        if(currentTypes !== undefined) {
          currentTypes = typesHelpers.sortByPriorities(currentTypes)
          clusterer.clearMarkers();
          for(var ic = 0; ic < currentTypes.length; ic++) {
            if(helpers.in(currentTypes[ic], currentTypes)) {
              if(allMarkers[currentTypes[ic].replace("_only", "")] !== undefined) {
                var markers = allMarkers[currentTypes[ic].replace("_only", "")].filter(function (value) {
                  return helpers.all(self.filtersObj.currentFilters, value.point.functions);
                })
                checkIcons(markers, currentTypes[ic]);
                clusterer.addMarkers(markers);
              }
            }
          }
          self.refreshPoints();
        }
        typesFilterObj.update(currentTypes);
        clearDetails();
        closeInfoBox();

        if(isMobile) {
          typesFilterObj.listWrapper.style.display = 'none';
        }
      } else {
        setTimeout(function(){ self.showType(type); }, 250);
      }
    };

    self.hideType = function(type) {
      if(mapIdle) {
        removeType(type);
      } else {
        setTimeout(function(){ self.hideType(type); }, 250);
      }
    };

    self.hideAllTypes = function() {
      currentTypes.length = 0;
      newPoints = [];

      clusterer.clearMarkers();
      listObj.list.innerHTML = '';
      typesFilterObj.update(currentTypes);
      clearDetails();
      closeInfoBox();
    };

    self.addType = function(type) {
      if(dynamicTypes[type.id] === undefined) {
        dynamicTypes[type.id] = [];
      }
      dynamicTypes[type.id] = type;
      easyPackConfig.points.types.push(type);
    };

    self.refreshPoints = function() {
      loadClosestPoints();
    };

    self.addPoint = function(point) {
      point.dynamic = true;
      if(dynamicTypes[point.type[0]] !== undefined) {
        point.icon = dynamicTypes[point.type[0]].icon;
      }
      if(helpers.in(point.type, currentTypes)) {
        addPoints([point], true, point.type);
      } else {
        for(var i = 0; point.type.length > i; i++) {
          if(dynamicPoints[point.type[i]] === undefined) {
            dynamicPoints[point.type[i]] = [];
          }
          dynamicPoints[point.type[i]].push(point);
        }
      }
    };

    self.searchPlace = function(place) {
      if(mapIdle) {

        function createNewEvent(eventName) {
          var event;
          if(typeof(Event) === 'function') {
            event = new Event(eventName);
          }else{
            event = document.createEvent('Event');
            event.initEvent(eventName, true, true);
          }

          return event
        }

        searchObj.searchInput.value = place;
        var event = createNewEvent('keyup');
        searchObj.searchInput.dispatchEvent(event);
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({
            'address': place
        }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
              var latLng = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
              mapObj.setCenter(latLng);
              searchObj.searchButton.click();
            }
        });
      } else {
        setTimeout(function(){ self.searchPlace(place); }, 250);
      }
    };

    self.searchLockerPoint = function(locker) {
      var self = this;
      if(mapIdle) {
        module.points.find(locker, function(response){
          var marker = createMarker(response),
              windowsPosition = isMobile ? new google.maps.Size(-145, -16) : new google.maps.Size(-170, -16),
              infoWindowObj = new infoWindow(marker, { clearDetails : clearDetails, setPointDetails : setPointDetails, setInfoBox : setInfoBox, closeInfoBox : closeInfoBox, style : style, infoBox : infoBoxObj, pointDetails : pointDetailsObj, placeholder : placeholderObj, placeholderId : placeholderId, initialLocation : initialLocation, map : mapObj, isMobile : isMobile, locationFromBrowser : locationFromBrowser }, { pixelOffset: windowsPosition }, response, pointCallback, self, isMobile);

          infoWindowObj.open();
          if (pointDetailsObj !== undefined && pointDetailsObj !== null) {
            var details = new pointDetails(marker, { setPointDetails : setPointDetails, pointDetails : pointDetailsObj, closeInfoBox : closeInfoBox, style : style, map : mapObj, placeholder : placeholderObj, initialLocation : initialLocation, isMobile : isMobile, widget: self.widget }, response);
            self.detailsObj = details;
            details.render();
          }
        });
      } else {
        setTimeout(function(){ self.searchLockerPoint(locker); }, 250);
      }
    };

    var checkIcons = function(markers, currentType) {
      for(var i = 0; markers.length  > i; i++) {
        updateMarkerIcon(markers[i], currentType);
      }
    };

    var checkTypes = function() {
      var subTypesEnabled = false;
      for(var t = 0; t < easyPackConfig.points.types.length; t++) {
        if(typeof easyPackConfig.points.types[t] === 'object') {
          if (easyPackConfig.points.types[t].name === 'pok') {
            easyPackConfig.points.types[t].name = 'pop';
          }
          subTypesEnabled = true;
          break;
        } else {
          if (easyPackConfig.points.types[t] === 'pok') {
            easyPackConfig.points.types[t] = 'pop';
          }
        }
      }
      if (helpers.in('pok', easyPackConfig.map.initialTypes)) {
        easyPackConfig.map.initialTypes = easyPackConfig.map.initialTypes.map(function (value) {
          if (value === 'pok') return 'pop';
          return value;
        })
      }
      currentTypes = helpers.intersection(easyPackConfig.map.initialTypes, easyPackConfig.points.types);
      var extendedTypes = typesHelpers.seachInArrayOfObjectsKeyWithCondition(typesHelpers.getExtendedCollection(), 'enabled', true, 'childs');
      extendedTypes = extendedTypes.concat(typesHelpers.getAllAdditionalTypes(typesHelpers.getExtendedCollection()) || []);
      if (extendedTypes.length > 0) {
        currentTypes = helpers.intersection(currentTypes, extendedTypes);
        if (currentTypes.length > 0) {
          currentTypes = currentTypes.concat(typesHelpers.getAllAdditionalTypes(typesHelpers.getExtendedCollection()));
          currentTypes.forEach( function (type) {
              if (typesHelpers.isParent(type, typesHelpers.getExtendedCollection())) {
                currentTypes = currentTypes.concat([typesHelpers.getNameForType(type)])
                currentTypes = currentTypes.concat(typesHelpers.getAllChildsForGroup(type, typesHelpers.getExtendedCollection()));
              }
          })
        }
      }
      if(currentTypes.length === 0) {
        currentTypes = [easyPackConfig.map.initialTypes[0]];
      }
    };

    var setInfoBox = function(obj) {
      infoBoxObj = obj;
    };

    var setPointDetails = function(obj) {
      pointDetailsObj = obj;
    };

    var closeInfoBox = function(){
      if(infoBoxObj !== undefined) {
        infoBoxObj.close();
      }
    };

    var getLocation = function() {
      if (easyPackConfig.map.useGeolocation && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position){
          location = [position.coords.latitude, position.coords.longitude];
          initialLocation = location;
          locationFromBrowser = true;
          renderMap();
          centerMap(location);
        }, function(){
          renderMap();
        });
      }
    };

    var sortCurrentPointsByDistance = function(points) {
      var self = this;
      if(points.length > 0) {
        sorted = points.sort(function (a, b) {
          var center = mapObj.getCenter();
          var aDistance = helpers.calculateDistance([center.lat(), center.lng()], [a.location.latitude, a.location.longitude]);
          var bDistance = helpers.calculateDistance([center.lat(), center.lng()], [b.location.latitude, b.location.longitude]);
          return aDistance - bDistance;
        });

        return sorted;
      }
    };

    self.sortCurrentPointsByDistance = sortCurrentPointsByDistance;

    var loadClosestPoints = function(currentType, list, filters) {
      var filters = self.filtersObj.currentFilters;
      if(filters === undefined) {
        filters = [];
      }
      if(currentTypes.length > 0 || filters.length > 0) {
        var distance = mapRendered ? calculateBoundsDistance() : easyPackConfig.map.defaultDistance;
        var dynArray = [];
        var params = self.isFilter ? {type: currentTypes, functions: filters} : {type: currentTypes};
        module.points.closest(location, distance, params, function(loadedPoints) {
          for(var i = 0; i < currentTypes.length; i++) {
            if(dynamicPoints[currentTypes[i]] !== undefined) {
              for(var k = 0; k < dynamicPoints[currentTypes[i]].length; k++) {
                var point = dynamicPoints[currentTypes[i]][k];
                    dist = helpers.calculateDistance([point.location.latitude, point.location.longitude], location);


                if(distance > dist * 10) {
                  dynArray.push(point);
                }
              }
            }
          }
          if(dynArray.length) {
            loadedPoints = loadedPoints.concat(dynArray);
            loadedPoints = sortCurrentPointsByDistance(loadedPoints);
          }
          var listUpdate = list === undefined ? true : list;
          if(currentType === undefined) {
            currentType = currentTypes[0];
          }

          addPoints(loadedPoints, true, currentType);
          module.api.pendingRequests = [];
        });
      } else {
        listObj.clear();
      }
    };

    self.loadClosestPoints = loadClosestPoints;

    var loadAllAsync = function(types, callback, abortCallback, filters, refresh) {
      if(currentTypes.length > 0 || types > 0) {
        var type = types.length > 0 ? types : currentTypes;
        if(types.length === 1 && dynamicPoints[types[0]] !== undefined) {
          addPoints(dynamicPoints[types[0]], true, types[0]);
        }

        var filtersData = filters !== undefined ? filters : null;
        var paramString = filtersData !== null ? { type: type, functions: filters } : { type: type };
        clusterer.clearMarkers();
        module.points.allAsync(location, 0, paramString, function(loadedPoints) {
          addPoints(loadedPoints.items, false);
          loaded += loadedPoints.items.length;
          statusBarObj.update(loaded, loadedPoints.count);
          if(loaded == loadedPoints.count) {
            if(callback !== undefined) {
              callback();
              module.api.pendingRequests = [];
            }
            statusBarObj.hide();
            allLoaded = true;
            loaded = 0;
          }
        }, abortCallback);
      } else {
        listObj.clear();
      }
    };

    var render = function() {
      var placeholder = document.getElementById(placeholderId);
      if (placeholder) {
        placeholderObj = placeholder;
        placeholderObj.className = 'easypack-widget';
        var documentObj = placeholderObj.ownerDocument;
        style = documentObj.createElement('style');
        style.appendChild(documentObj.createTextNode(""));
        documentObj.head.appendChild(style);
        monitorWidths();
        setInterval(function(){ monitorWidths(); }, 250);
        renderLoadingIcon();
      } else {
        setTimeout(function(){ render(); }, 250);
      }
    };

    var initialize = function(){
      if(easyPackConfig.map.useGeolocation) {
        var checkingForLocation = setInterval(function(){
          if(locationFromBrowser) {
            clearInterval(checkingForLocation);
            renderMap();
          }
        }, 100);
        setTimeout(function(){
          clearInterval(checkingForLocation);
          renderMap();
        }, 3000);
      } else {
        renderMap();
      }
    };

    var trackBounds = function() {
      google.maps.event.addListener(mapObj, 'bounds_changed', function(){
        clearTimeout(reloadProcess);
        var center = mapObj.getCenter();
        reloadProcess = setTimeout(function(){
          location = [center.lat(), center.lng()];
          if(self.isFilter) {
            loadClosestPoints([], true, self.filtersObj.currentFilters);
          } else {
            loadClosestPoints();
          }
        }, easyPackConfig.map.reloadDelay);
      });
    };

    var renderMap = function() {
      if ( easyPack.googleMapsApi.ready && !mapRendered ) {

        mapListRow = document.createElement('div');
        mapListRow.className = 'map-list-row';

        mapListFlex = document.createElement('div');
        mapListFlex.className = helpers.hasCustomMapAndListInRow()? 'map-list-in-row' : 'map-list-flex';

        mapElement = document.createElement('div');
        mapElement.className = 'map-widget';
        mapElement.id = 'map';

        mapListFlex.appendChild(mapElement);
        mapListRow.appendChild(mapListFlex);

        var options = {
          zoom: easyPackConfig.map.initialZoom,
          center: {
            lat: location[0],
            lng: location[1]
          },
          gestureHandling: 'greedy'
        };
          renderTypesFilter();

          addTypeClickEvent();
        mapObj = new google.maps.Map(mapElement, options);
        clusterer = new MarkerClusterer(mapObj, [], easyPackConfig.map.clusterer);
        self.clusterer = clusterer;

        window.addEventListener('orientationchange', function() {
          google.maps.event.trigger(mapObj, 'resize');
        });

        mapRendered = true;
        placeholderObj.removeChild(loadingIconWrapper);
        placeholderObj.appendChild(mapListRow);

        renderFilters();
        renderSearch();
        renderList();
        if (!helpers.hasCustomMapAndListInRow()) {
          renderViewChooser();
        }
        renderStatusBar();
        renderLanguageBar(module, placeholderId);
        allTypes = typesHelpers.seachInArrayOfObjectsKeyWithCondition(typesHelpers.getExtendedCollection(), 'enabled', true, 'childs');
        allTypes = allTypes.concat(typesHelpers.getAllAdditionalTypes(typesHelpers.getExtendedCollection()))
        loadAllAsync(currentTypes.concat(easyPackConfig.points.types).concat(allTypes).filter(helpers.uniqueElementInArray));
        trackBounds();
        google.maps.event.addListener( mapObj, 'idle', function() {
          mapIdle = true;
        });

        google.maps.event.addListener(mapObj, 'zoom_changed', function(){
          clearDetails();
          closeInfoBox();
        });

        google.maps.event.trigger(mapObj, 'resize');

        if ( callback ) { callback(self); }
      } else {
        setTimeout(function(){ renderMap(); }, 250);
      }
    };

    var addPoints = function(points, list, currentType) {
        if( mapRendered ) {
            if(list) {
                listObj.clear();
            }
            processNewPoints(points, list, currentType);
        } else {
            setTimeout(function(){ addPoints(points, list, currentType); }, 250);
        }
    };
    self.addPoints = addPoints;

    var centerMap = function(location) {
      if(mapRendered) {
        var latLng = new google.maps.LatLng(location[0], location[1]);
        mapObj.setCenter(latLng);
      } else {
        setTimeout(function(){ centerMap(location); }, 250);
      }
    };

    var addListener = function(marker) {
      return function() {
        markerClick(marker);
      };
    };
    self.addListener = addListener;

    var createMarker = function(point, map) {
      var latLng = new google.maps.LatLng(point.location.latitude, point.location.longitude),
          icon = dynamicTypes[point.type] !== undefined ? dynamicTypes[point.type].marker : module.points.markerIcon(point, currentTypes),
          marker = new google.maps.Marker({
            position: latLng,
            point: point,
            icon: icon,
            map: map !== undefined ? map : mapObj
          });

      google.maps.event.addListener(marker, 'click', addListener(marker));

            return marker;
    };

    this.createMarker = createMarker;

    this.onlyUniqueMarkers = function (value, index, self) {
        return self.indexOf(self.find(function(e) { return e.point.name === value.point.name; })) === index;
    };

    var processNewPoints = function(points, list, currentType) {
      var markers = [],
        anyNewPoints = false;

      points.filter(function (value) { return idMarkers[value.name] === undefined })
        .forEach(function (point) {
          anyNewPoints = true;

        if (point.location && point.location.latitude !== 0 && point.location.longitude !== 0) {
          var existingMarkerIndex = newPoints.indexOf(point.name);
          if (existingMarkerIndex > -1 && list === true) {
            var existingMarker = idMarkers[point.name];

            if (module.pointsToSearch.indexOf({name: point.name, types: point.type, action: addListener(existingMarker)}) === -1) {
              module.pointsToSearch.push({name: point.name, types: point.type, action: addListener(existingMarker)});
            }
            if (mapObj.getBounds().contains(idMarkers[point.name].getPosition())) {
              listObj.addPoint(point, addListener(idMarkers[point.name]), currentType);
            }
          } else {
            var marker = createMarker(point, null);

            if (module.pointsToSearch.indexOf({
                    name: point.name,
                    types: point.type,
                    action: addListener(marker)
                }) === -1) {
                module.pointsToSearch.push({name: point.name, types: point.type, action: addListener(marker)});
            }

            newPoints.push(point.name);

            point.type.filter(function (item) {
              return item !== 'pok'
            }).forEach(function (type) {

              if (allMarkers[type] === undefined || allMarkers[type].length === 0) {
                allMarkers[type] = [];
              }

              if (allMarkers[type].indexOf(marker) === -1) {
                allMarkers[type].push(marker);
              }

              if (typesHelpers.in(type, currentTypes) && idMarkers[point.name] === undefined) {
                markers.push(marker);
              }

              idMarkers[point.name] = marker;
            })

            if (list !== undefined && list === true) {
              if (mapObj.getBounds().contains(idMarkers[point.name].getPosition())) {
                listObj.addPoint(point, addListener(idMarkers[point.name]), currentType);
              }
            }
          }
        }

      })

      if (anyNewPoints === false && list === true) {
        points.forEach(function (point) {
          if (mapObj.getBounds().contains(idMarkers[point.name].getPosition())) {
            listObj.addPoint(point, addListener(idMarkers[point.name]), currentType);
          }
        })

      }


      if(markers.length > 0) {
          clusterer.addMarkers(markers);
          markers = [];
      }


      if (helpers.hasCustomMapAndListInRow()) {
        listObj.paginate(1, helpers.getPaginationPerPage());
      }
    };

    this.processNewPoints = processNewPoints;

    var updateMarkerIcon = function(marker, currentType) {
      var icon = dynamicTypes[marker.point.type] === undefined ? module.points.markerIcon(marker.point, currentTypes) : dynamicTypes[marker.point.type].marker;
      marker.setIcon(icon);
    };

    var markerClick = function(marker) {
      if(marker === undefined) {
        setTimeout(function(){ markerClick(marker); }, 250);
      } else {
        var timeout = 0, offset = -50;

        if (document.getElementById("easypack-map").getBoundingClientRect().top < 0) {
          timeout = isMobile? 600: 300;
          offset = isMobile ? 100 : -100;
        } else if (document.getElementById("easypack-map").getBoundingClientRect().top > 0) {
          timeout = isMobile? 400 : 0;
          offset = 0;
        }

        document.getElementById("easypack-map").scrollIntoView({behavior: "smooth"})


        if(isMobile && !helpers.hasCustomMapAndListInRow()) {
          viewChooserObj.listWrapper.setAttribute('data-active', 'false');
          viewChooserObj.mapWrapper.setAttribute('data-active', 'true');
          mapElement.style.display = 'block';
          listObj.listElement.style.display = 'none';
        }



        function offsetCenter(latlng, offsetx, offsety, windowObject) {
          var minZoom = easyPackConfig.map.detailsMinZoom;

          if(mapObj.getZoom() < minZoom) {
            mapObj.setZoom(minZoom);
          }

          windowObject.open();

          var scale = Math.pow(2, mapObj.getZoom());

          var worldCoordinateCenter = mapObj.getProjection().fromLatLngToPoint(latlng);
          var pixelOffset = new google.maps.Point((offsetx/scale) || 0,(offsety/scale) ||0);

          var worldCoordinateNewCenter = new google.maps.Point(
            worldCoordinateCenter.x - pixelOffset.x,
            worldCoordinateCenter.y + pixelOffset.y
          );

          var newCenter = mapObj.getProjection().fromPointToLatLng(worldCoordinateNewCenter);

          mapObj.setCenter(newCenter);
        }

        var windowsPosition = isMobile ? new google.maps.Size(-145, -16) : new google.maps.Size(-170, -16);

        if(marker.point.dynamic) {
          var infoWindowObj = new infoWindow(marker, { clearDetails : clearDetails, setPointDetails : setPointDetails, setInfoBox : setInfoBox, closeInfoBox : closeInfoBox, style : style, infoBox : infoBoxObj, pointDetails : pointDetailsObj, placeholder : placeholderObj, placeholderId : placeholderId, initialLocation : initialLocation, map : mapObj, isMobile : isMobile, locationFromBrowser : locationFromBrowser }, { pixelOffset: windowsPosition }, marker.point, pointCallback, self, isMobile);

          setTimeout(function () {
            offsetCenter(marker.getPosition(), 0, offset, infoWindowObj);
          }, timeout);

          if (pointDetailsObj !== undefined && pointDetailsObj !== null) {
            var details = new pointDetails(marker, { setPointDetails : setPointDetails, pointDetails : pointDetailsObj, closeInfoBox : closeInfoBox, style : style, map : mapObj, placeholder : placeholderObj, initialLocation : initialLocation, isMobile : isMobile, widget: self.widget }, marker.point);
            details.render();
            self.detailsObj = details;
          }
        } else {
          var mapFin = mapObj.getStreetView().getVisible() ? mapObj.getStreetView() : mapObj;

          if(self.pointsStorage[marker.point.name] === undefined) {
            module.points.find(marker.point.name, function(response) {
              self.pointsStorage[marker.point.name] = response;
              infoWindowObj = new infoWindow(marker, { clearDetails : clearDetails, setPointDetails : setPointDetails, setInfoBox : setInfoBox, closeInfoBox : closeInfoBox, style : style, infoBox : infoBoxObj, pointDetails : pointDetailsObj, placeholder : placeholderObj, placeholderId : placeholderId, initialLocation : initialLocation, map : mapFin, isMobile : isMobile, locationFromBrowser : locationFromBrowser }, { pixelOffset: windowsPosition }, response, pointCallback, self, isMobile);

              setTimeout(function () {
                offsetCenter(marker.getPosition(), 0, offset, infoWindowObj);
              }, timeout);

              if (pointDetailsObj !== undefined && pointDetailsObj !== null) {
                var details = new pointDetails(marker, { setPointDetails : setPointDetails, pointDetails : pointDetailsObj, closeInfoBox : closeInfoBox, style : style, map : mapObj, placeholder : placeholderObj, initialLocation : initialLocation, isMobile : isMobile, widget : self }, response);
                details.render();
                self.detailsObj = details;
              }
            });
          } else {
            var response = self.pointsStorage[marker.point.name];
            infoWindowObj = new infoWindow(marker, { clearDetails : clearDetails, setPointDetails : setPointDetails, setInfoBox : setInfoBox, closeInfoBox : closeInfoBox, style : style, infoBox : infoBoxObj, pointDetails : pointDetailsObj, placeholder : placeholderObj, placeholderId : placeholderId, initialLocation : initialLocation, map : mapFin, isMobile : isMobile, locationFromBrowser : locationFromBrowser }, { pixelOffset: windowsPosition }, response, pointCallback, self, isMobile);

            setTimeout(function () {
              offsetCenter(marker.getPosition(), 0, offset, infoWindowObj);
            }, timeout);

            if (pointDetailsObj !== undefined && pointDetailsObj !== null) {
              var details = new pointDetails(marker, { setPointDetails : setPointDetails, pointDetails : pointDetailsObj, closeInfoBox : closeInfoBox, style : style, map : mapObj, placeholder : placeholderObj, initialLocation : initialLocation, isMobile : isMobile, widget : self }, response);
              details.render();
              self.detailsObj = details;
            }
          }
        }
      }
    };

    var calculateBoundsDistance = function() {
      var corner = mapObj.getBounds().getNorthEast();
      var distanceMultiplier = easyPackConfig.map.distanceMultiplier;

      return helpers.calculateDistance([location[0], location[1]], [corner.lat(), corner.lng()]) * distanceMultiplier;
    };

    var renderLoadingIcon = function() {
      loadingIconWrapper = document.createElement('div');
      loadingIconWrapper.className = 'loading-icon-wrapper';

      var loadingIcon = document.createElement('img');
      loadingIcon.src = easyPackConfig.loadingIcon;

      loadingIconWrapper.appendChild(loadingIcon);
      placeholderObj.appendChild(loadingIconWrapper);
    };

    var renderTypesFilter = function() {
      var filterKind;
      if (easyPackConfig.mobileFiltersAsCheckbox) {
        filterKind = 'checkbox';
      } else {
        filterKind = isMobile ? 'radio' : 'checkbox';
      }
      typesFilterObj = new typesFilter(currentTypes, { currentTypes : currentTypes, style : style }, filterKind);
      self.typesFilterObj = typesFilterObj;
      typesFilterObj.render(placeholderObj);
    };

    var addTypeClickEvent = function() {
      var typeItems = typesFilterObj.items;
      var i;

      if(!isMobile) {
        document.addEventListener('click', function() {
          var dropdowns = document.getElementsByClassName('has-subtypes');
          for(var s = 0; s < dropdowns.length; s++) {
            dropdowns[s].dataset.dropdown = 'closed';
          }
        });
      }

      var handler = function(elem) {
        var type = elem.parentNode.getAttribute('data-type');
        if (isMobile && !easyPackConfig.mobileFiltersAsCheckbox) {
          self.showType(type);
        } else {
            if(!helpers.in(type, currentTypes)) {
                self.showType(type);
            } else {
                self.hideType(type);
            }
        }
      };

      for(i = 0; i < typeItems.length; i++) {
        var typeItem = typeItems[i];

        typeItem.addEventListener('click', function(event) {
          event.stopPropagation();
          handler(this);
        });

        typeItem.nextSibling.addEventListener('click', function(event) {
          event.stopPropagation();
          handler(this);
        });
      }
    };

    var renderList = function() {
      if (helpers.hasCustomMapAndListInRow()) {
        listObj = new paginatedListWidget({ currentTypes : currentTypes });
      } else {
        listObj = new listWidget({ currentTypes : currentTypes });
      }

      self.listObj = listObj;
      listObj.render(mapListFlex);
    };

    var renderViewChooser = function(){
      viewChooserObj = new viewChooser({ style : style, mapElement : mapElement, list : listObj });
      self.viewChooserObj = viewChooserObj;
      viewChooserObj.render(placeholderObj);
    };

    var renderStatusBar = function() {
      statusBarObj = new statusBar(self);
      self.statusBarObj = statusBarObj;
      statusBarObj.render(mapElement);
    };

    var renderLanguageBar = function(module, placeholder) {
      if(module.config.langSelection) {
        languageBarObj = new languageBar(self, module, placeholder);
        languageBarObj.render(mapElement);
      }
    };

    var renderSearch = function() {
      searchObj = new searchWidget(self);
      self.searchObj = searchObj;
      placeholderObj.insertBefore(searchObj.render(), placeholderObj.firstChild);
      createAutocomplete();
    };

    var renderFilters = function() {
      filtersObj = new filtersWidget(self);
      self.filtersObj = filtersObj;
      placeholderObj.insertBefore(filtersObj.render(), placeholderObj.firstChild);
    };

    var createAutocomplete = function(){
      return autocompleteService.service(searchObj, mapObj, {placeholderObj: placeholderObj, clearDetails: clearDetails, closeInfoBox: closeInfoBox, currentTypes: currentTypes});
    };

    var clearDetails = function(){
      if(typeof pointDetailsObj != 'undefined' && pointDetailsObj !== null) {
        placeholderObj.removeChild(pointDetailsObj.element);
        pointDetailsObj = null;
      }
    };

    var monitorWidths = function() {
      if(placeholderObj.offsetWidth < easyPackConfig.mobileSize) {
        if(!isMobile && !self.isModal) {
          closeInfoBox();
          clearDetails();
          isMobile = true;
          self.isMobile = true;
          placeholderObj.className = 'easypack-widget mobile';
          if(typesFilterObj) {
            if (!easyPackConfig.mobileFiltersAsCheckbox) {
              typesFilterObj.setKind('radio');
            };
            typesFilterObj.listWrapper.style.display = 'none';
          }
          if(currentTypes.length > 1) {
            if (!easyPackConfig.mobileFiltersAsCheckbox) {
                currentTypes = [currentTypes[0]];
            }
            if(typesFilterObj) { typesFilterObj.update(currentTypes); }
          }
        }
      } else {
        if(isMobile) {
          closeInfoBox();
          clearDetails();
          placeholderObj.className = 'easypack-widget';
          isMobile = false;
          self.isMobile = false;
          typesFilterObj.listWrapper.style.display = 'block';
          if(typesFilterObj) { typesFilterObj.setKind('checkbox'); }
        }
      }
    };

    var removeType = function(type) {

      var index = currentTypes.indexOf(type);
      if(index > -1) {
        if(module.api.pendingRequests.length > 0) {
          for(var i = 0; module.api.pendingRequests.length > i; i++) {
          }
        } else {
          allLoaded = true;
        }

        newPoints = [];

        if (typesHelpers.isParent(type, typesHelpers.getExtendedCollection()) && typesHelpers.isAllChildSelected(type, currentTypes, helpers.findObjectByPropertyName(typesHelpers.getExtendedCollection(), type) || {})) {
          typesHelpers.getAllChildsForGroup(type, typesHelpers.getExtendedCollection()).forEach( function (value) {
            removeType(value)
          })
        }
        currentTypes.splice(index, 1);
        var parent = typesHelpers.getParentIfAvailable(type, typesHelpers.getExtendedCollection());
        if (parent !== null && typesHelpers.isNoOneChildSelected(parent, currentTypes, typesHelpers.getObjectForType(parent, typesHelpers.getExtendedCollection()))) {
            removeType(parent)
        }

        type = type.replace('_only', '');
        if(allMarkers[type] !== undefined) {
          checkIcons(allMarkers[type]);
          clusterer.removeMarkers(allMarkers[type]);
        }
        if (typesHelpers.isOnlyAdditionTypes(currentTypes.filter(function (e) {return e;}), typesHelpers.getExtendedCollection())) {
          typesHelpers.getAllAdditionalTypes(typesHelpers.getExtendedCollection()).forEach( function (additionalType) {
            removeType(additionalType)
          });
        }
          clusterer.clearMarkers();

        if (currentTypes.length > 0) {
          clusterer.clearMarkers();
          currentTypes.forEach(function (curTypes) {
            if (allMarkers[curTypes.replace('_only', '')] !== undefined) {
              var markers = allMarkers[curTypes.replace("_only", "")].filter(function (value) {
                return helpers.all(self.filtersObj.currentFilters, value.point.functions);
              })
              checkIcons(markers);
              clusterer.addMarkers(markers);
            }
          })
        }
        loadClosestPoints();
        typesFilterObj.update(currentTypes);
        clearDetails();
        closeInfoBox();
      }
    };

    checkTypes();
    getLocation();
    loadGoogleMapsApi();
    render();
    initialize();
    this.currentTypes = currentTypes;

    return this;
  };
;

  var dropdownWidget = function(container, callback, module) {
  this.build(container, callback);
  this.callback = callback;
  module.dropdownWidgetObj = this;

  if ( !easyPack.googleMapsApi.initialized ) {
    easyPack.googleMapsApi.initialized = true;
    helpers.asyncLoad('https://maps.googleapis.com/maps/api/js?v=3.exp&callback=easyPack.googleMapsApi.initializeDropdown&libraries=places&key=' + easyPackConfig.map.googleKey);
  }
}

dropdownWidget.prototype.build = function(container, callback) {
  var element = document.getElementById(container);
  element.className = 'easypack-widget';

  this.dropdownContainer = document.createElement('div');
  this.dropdownContainer.className = 'easypack-dropdown';
  this.dropdownContainer.dataset.open = 'false';

  this.dropdownSelect = document.createElement('div');
  this.dropdownSelect.className = 'easypack-dropdown__select';

  this.dropdownLabel = document.createElement('span');
  this.dropdownLabel.innerHTML = t('select_point');

  this.dropdownSelect.appendChild(this.dropdownLabel);

  this.dropdownArrow = document.createElement('span');
  this.dropdownArrow.className = 'easypack-dropdown__arrow';
  this.dropdownArrowImg = document.createElement('img');
  this.dropdownArrowImg.src = easyPackConfig.assetsServer + '/' + easyPackConfig.map.filtersIcon;

  this.dropdownArrow.appendChild(this.dropdownArrowImg);

  this.dropdownSelect.appendChild(this.dropdownArrow);

  this.dropdownContent = document.createElement('div');
  this.dropdownContent.className = 'easypack-dropdown__content';


  var dropdownSearch = document.createElement('input');
  dropdownSearch.className = 'easypack-dropdown__search';
  dropdownSearch.placeholder = t('search_by_city_or_address');

  this.dropdownList = document.createElement('ul');
  this.dropdownList.className = 'easypack-dropdown__list';

  this.loadingIcon = document.createElement('div');
  this.loadingIcon.className = 'easypack-loading hidden';

  var loadingIcon = document.createElement('img');
  loadingIcon.src = easyPackConfig.loadingIcon;

  this.loadingIcon.appendChild(loadingIcon);

  var self = this, timeout;
  dropdownSearch.addEventListener('keyup', function(e) {
    if(timeout) {
      clearTimeout(timeout);
      timeout = null;
    }

    timeout = setTimeout(function() {
      var input = this.value.replace(/ul\.\s?/i, '');
      if (input.length !== 0) {
        self.loadingIcon.className = 'easypack-loading';
        self.searchPoints(input, self.callback);
      }
    }.bind(this), 250);
  }, false);

  this.dropdownContent.appendChild(dropdownSearch);
  this.dropdownContent.appendChild(this.dropdownList);
  this.dropdownContent.appendChild(this.loadingIcon);
  this.dropdownContainer.appendChild(this.dropdownSelect);
  this.dropdownContainer.appendChild(this.dropdownContent);

  element.appendChild(this.dropdownContainer);
  var self = this;

  this.dropdownSelect.addEventListener('click', function() {
    var state = self.dropdownContainer.dataset.open;
    self.dropdownContainer.dataset.open = state === 'false' ? 'true' : 'false';
  });
}

dropdownWidget.prototype.afterLoad = function() {
  var self = this;
  self.loadingIcon.className = 'easypack-loading';
  this.searchFn(easyPackConfig.map.defaultLocation, this.callback);
}

dropdownWidget.prototype.searchPoints = function(address, callback) {
  var self = this;
  self.loadedPoints = [];
  this.autocompleteService = new google.maps.places.AutocompleteService();
  this.geocoder = new google.maps.Geocoder();

  this.autocompleteService.getPlacePredictions({input: address, types: ['geocode']}, function(results, status) {
    if(results.length > 0) {
      self.geocoder.geocode( { 'placeId' : results[0].place_id }, function( results, status ) {
        if(results.length > 0) {
          var lat = results[0].geometry.location.lat();
          var lng = results[0].geometry.location.lng();
          self.dropdownList.innerHTML = '';
          self.searchFn([lat, lng], callback);
        }
      });
    }
  });
}

dropdownWidget.prototype.searchFn = function(cords, callback) {
  var self = this;
  module.points.closest(cords, easyPackConfig.map.defaultDistance, { types: easyPackConfig.points.types, fields: ['name', 'type', 'location', 'address', 'address_details', 'is_next', 'location_description', 'opening_hours', 'payment_point_descr'] }, function(loadedPoints) {
    self.loadedPoints = loadedPoints;
    for(var i = 0; i < loadedPoints.length; i++) {
      var elementLi = document.createElement('li');
      elementLi.dataset.placeid = i;
      elementLi.innerHTML = loadedPoints[i].address.line1 + ', ' + loadedPoints[i].address.line2 + ', ' + loadedPoints[i].name;
      (function(elementLi, callback) {
        elementLi.addEventListener('click', function() {
          callback(self.loadedPoints[this.dataset.placeid]);
          self.dropdownLabel.innerHTML = this.innerHTML;
          self.dropdownContainer.dataset.open = 'false';
        });
      })(elementLi, callback);
      self.dropdownList.appendChild(elementLi);
    }
    self.loadingIcon.className = 'hidden';
  });
};

  var pointDetails = function(marker, params, response) {
    this.params = params;
    this.marker = marker;
    this.map = params.map;
    this.params.style.sheet.insertRule('.easypack-widget .details-actions .action a { background: url(' + easyPackConfig.map.pointIconDark + ') no-repeat; }', 0);
    this.params.style.sheet.insertRule('.easypack-widget.mobile .details-actions .action a { background: url(' + easyPackConfig.map.mapIcon + ') no-repeat; }', 0);
    this.response = response;

    return this;
  };

  pointDetails.prototype = {
    render: function() {
      this.pointData = this.response;

      if (easyPackConfig.customDetailsCallback) {
        easyPackConfig.customDetailsCallback(this.pointData);
        return;
      }

      var self = this;
      this.element = document.createElement('div');
      this.element.className = 'point-details';

      this.wrapper = document.createElement('div');
      this.wrapper.className = 'details-wrapper';
      this.element.appendChild(this.wrapper);

      this.content = document.createElement('div');
      this.content.className = 'details-content';
      this.wrapper.appendChild(this.content);

      this.closeButton = document.createElement('div');
      this.closeButton.className = 'close-button';
      this.closeButton.innerHTML = '&#10005';
      this.content.appendChild(this.closeButton);

      this.closeButton.onclick = function() {
        if(typeof self.params.pointDetails != 'undefined' && self.params.pointDetails !== null) {
          self.params.placeholder.removeChild(self.params.pointDetails.element);
          self.params.pointDetails = null;
          self.params.setPointDetails(null);
          if(easyPackConfig.closeTooltip) {
            self.params.closeInfoBox();
          }
        }
      };

      this.actions = document.createElement('div');
      this.actions.className = 'details-actions';
      if(this.params.isMobile){ this.wrapper.appendChild(this.actions); }

      this.planRoute = document.createElement('div');
      this.planRoute.className = 'action plan-route';

      this.routeLink = document.createElement('a');
      this.routeLink.className = 'route-link';
      this.routeLink.href = helpers.routeLink(this.params.initialLocation, this.marker.point.location);
      this.routeLink.target = '_new';
      this.routeLink.innerHTML = t('plan_route');

      this.planRoute.appendChild(this.routeLink);
      this.actions.appendChild(this.planRoute);

      this.pointBox = document.createElement('div');
      this.pointBox.className = 'point-box';

      this.title = document.createElement('h1');
      this.title.innerHTML = helpers.pointName(this.marker.point, this.params.widget.currentTypes);

      this.pointBox.appendChild(this.title);

      this.address = document.createElement('p');
      this.address.className = 'address';

      var addressParts = easyPackConfig.addressFormat.replace(/{(.*?)}/g, function(match, content) {
        var elem = match.replace('{', '').replace('}', '');
        var data = self.response.address_details[elem] === null ? '' : self.response.address_details[elem];
        if(data === undefined) {
          data = self.marker.point[elem];
        }

        return data;
      });


      if(easyPackConfig.descriptionInWindow) {
        this.address.innerHTML += self.response.location_description + '<br>';
      }

      this.address.innerHTML += addressParts;
      this.pointBox.appendChild(this.address);

      if(self.response.name !== undefined && self.response.name !== null && helpers.in('pok', self.response.type)) {
        this.name = document.createElement('p');
        this.name.className = 'name';
        this.name.innerHTML = self.response.name;
        this.pointBox.appendChild(this.name);
      }

      if(!this.params.isMobile){ this.pointBox.appendChild(this.actions); }
      this.content.appendChild(this.pointBox);

      this.description = document.createElement('div');
      this.description.className = 'description';

      this.content.appendChild(this.description);

      easyPackConfig.map.photosUrl = easyPackConfig.map.photosUrl.replace('{locale}', easyPackConfig.defaultLocale);
      this.photoUrl = easyPackConfig.assetsServer + easyPackConfig.map.photosUrl + this.marker.point.name + '.jpg';
      this.photo = document.createElement('img');
      this.photo.setAttribute('src', this.photoUrl);
      this.photo.onload = function() {
        self.photoElement = document.createElement('div');
        self.photoElement.className = 'description-photo';
        self.photoElement.appendChild(self.photo);

        self.content.insertBefore(self.photoElement, self.description);
      };

      if(this.params.placeholder.getElementsByClassName('point-details').length === 0 || (typeof this.params.pointDetails == 'undefined' || this.params.pointDetails === null)) {
        this.params.placeholder.appendChild(this.element);
      } else {
        this.params.placeholder.replaceChild(this.element, this.params.pointDetails.element);
      }
      this.params.pointDetails = this;
      this.params.setPointDetails(this);
      this.fetchDetails();
    },
    fetchDetails: function(){
      var self = this;

      if(this.marker.point.dynamic) {
        self.pointData = this.marker.point;
        self.renderDetails();
      } else {
        if(self.pointData === undefined) {
          module.points.find(this.marker.point.name, function(response){
            self.pointData = response;
            self.renderDetails();
          });
        } else {
          self.renderDetails();
        }
      }
    },
    renderDetails: function() {
      var self = this;
      if(self.description !== null) {
        var locationDescription = self.pointData.location_description;

        this.locationDescription = document.createElement('div');

        if(locationDescription !== undefined && locationDescription !== null) {
          this.locationDescription.className = 'item';
          this.locationDescriptionTerm = document.createElement('div');
          this.locationDescriptionTerm.className = 'term';
          this.locationDescriptionTerm.innerHTML = t('locationDescription');
          this.locationDescriptionDefinition = document.createElement('div');
          this.locationDescriptionDefinition.className = 'definition';
          this.locationDescriptionDefinition.innerHTML =  locationDescription;

          this.locationDescription.appendChild(this.locationDescriptionTerm);
          this.locationDescription.appendChild(this.locationDescriptionDefinition);
        }

        var isNextHandler = self.pointData.is_next === null ? false : self.pointData.is_next;
        if(!(isNextHandler && easyPackConfig.region === 'fr')) {
          this.description.appendChild(this.locationDescription);
        }

        var openingHours = self.pointData.opening_hours;
        if(openingHours !== undefined && openingHours !==null) {
                    this.openingHours = document.createElement('div');
          this.openingHours.className = 'item';
          this.openingHoursTerm = document.createElement('div');
          this.openingHoursTerm.className = 'term';
          this.openingHoursTerm.innerHTML = t('openingHours');
          this.openingHoursDefinition = document.createElement('div');
          this.openingHoursDefinition.className = 'definition';
          this.openingHoursDefinition.innerHTML = null
          if(easyPackConfig.formatOpenHours) {
            var days = [], hours = [];
            var uniqueHours = openingHours.match(/(\|.*?\;)/g);
            uniqueHours.filter(function(item, pos, self) {
              return self.indexOf(item) === pos;
            })
            .forEach(function (t, index) {
              var result = t.replace(';', '').replace('|', '');
              hours.push(result);
            });
            openingHours.match(/(;|[a-z]|[A-Z])(.*?)(\|)/g)
            .forEach( function (value, index) {
              var result = t(value.replace('|', '').replace(';', ''));
              if (index === 0) days.push(result);
              else if (uniqueHours[index].match(/(\|)(.*?)(\;)/g)[0] !== uniqueHours[index-1].match(/(\|)(.*?)(\;)/g)[0]) days.push(result);
              else if (uniqueHours[index].match(/(\|)(.*?)(\;)/g)[0] !== uniqueHours[index+1].match(/(\|)(.*?)(\;)/g)[0]) days.push(result);
            });
            var hoursForDays = [];
            days.forEach( function (value, index) {
              if (index === 0) {
                hoursForDays.push(value);
                return;
              }
              if (index % 2 === 1) typeof hoursForDays[index-1] !== 'undefined' ? hoursForDays[index-1] += '-' + value : hoursForDays[index-1] = value;
              else hoursForDays.push(value)
            });
            openingHours = '';
            hoursForDays.forEach( function (value, index){
              openingHours += value + ': ' + hours[index].replace('-|-', '-') + '<br />';
            })
          }
          this.openingHoursDefinition.innerHTML = helpers.openingHours(openingHours);
          this.openingHours.appendChild(this.openingHoursTerm);
          this.openingHours.appendChild(this.openingHoursDefinition);
          this.description.appendChild(this.openingHours);
        }

        var payByLink = self.pointData.payment_point_descr;
        if(easyPack.config.languages === undefined) {
          easyPack.config.languages = ['pl'];
        }
        if(easyPack.config.languages.length !== 2) {
          if(payByLink !== undefined && payByLink !== null) {
            this.payByLink = document.createElement('div');
            this.payByLink.className = 'item';
            this.payByLinkTerm = document.createElement('div');
            this.payByLinkTerm.className = 'term';
            this.payByLinkTerm.innerHTML = t('pay_by_link');
            this.payByLinkDefinition = document.createElement('div');
            this.payByLinkDefinition.className = 'definition';
            this.payByLinkDefinition.innerHTML = payByLink;

            this.payByLink.appendChild(this.payByLinkTerm);
            this.payByLink.appendChild(this.payByLinkDefinition);
            this.description.appendChild(this.payByLink);
          }
        }

        var isNext = self.pointData.is_next;

        if(isNext !== undefined && isNext !== null && isNext !== false) {
          if(easyPackConfig.region !== 'fr') {
            this.isNext = document.createElement('div');
            this.isNext.className = 'item';
            this.isNextTerm = document.createElement('div');
            this.isNextTerm.className = 'term';
            this.isNextTerm.innerHTML = t('is_next');
            this.isNextDefinition = document.createElement('div');
            this.isNextDefinition.className = 'definition';

            this.isNext.appendChild(this.isNextTerm);
            this.isNext.appendChild(this.isNextDefinition);
            this.description.appendChild(this.isNext);
          }
        }
      } else {
        setTimeout(function(){ self.renderDetails(); }, 100);
      }
    },
    rerender: function() {
      this.routeLink.innerHTML = t('plan_route');
      this.title.innerHTML = helpers.pointName(this.marker.point, this.params.widget.currentTypes);
      if(this.locationDescriptionTerm !== undefined) {
        this.locationDescriptionDefinition.innerHTML = this.pointData.location_description;
        if(this.locationDescriptionDefinition.innerHTML.length > 0) {
          this.locationDescriptionTerm.innerHTML = t('locationDescription');
        }
      }
      if(this.pointData.opening_hours !== undefined && this.pointData.opening_hours !==null) {
        this.openingHoursTerm.innerHTML = t('openingHours');
      }
      if(this.pointData.payment_point_descr !== undefined && this.pointData.payment_point_descr !== null) {
        if(this.payByLinkTerm !== undefined) {
          this.payByLinkTerm.innerHTML = t('pay_by_link');
        }
      }
      if(easyPackConfig.region !== 'fr' && this.pointData.is_next !== undefined && this.pointData.is_next !== null && this.pointData.is_next !== false) {
        if(this.isNextTerm !== undefined) {
          this.isNextTerm.innerHTML = t('is_next');
        }
      }
    }
  };
;

  var searchWidget = function(widget) {
    this.widget = widget;
    this.build();

    return this;
  };

  searchWidget.prototype = {
    build: function() {
      var self = this;

      this.searchElement = document.createElement('div');
      this.searchElement.className = 'search-widget';

      this.searchGroup = document.createElement('div');
      this.searchGroup.className = 'input-group';

      this.searchInput = document.createElement('input');
      this.searchInput.type = 'text';
      this.searchInput.className = 'form-control';
      this.searchInput.id = 'easypack-search';
      this.searchInput.name = 'easypack-search';
      this.searchInput.placeholder = t('search_by_city_or_address');

      this.searchButtonSpan = document.createElement('span');
      this.searchButtonSpan.className = 'input-group-btn';

      this.searchButton = document.createElement('button');
      this.searchButton.className = 'btn btn-search';
      this.searchButton.type = 'button';
      this.searchButton.style.backgroundImage = 'url(' + easyPackConfig.iconsUrl + 'search.png)';

      this.filtersButtonSpan = document.createElement('span');
      this.filtersButtonSpan.className = 'input-group-btn';

      this.filtersButton = document.createElement('button');
      this.filtersButton.className = 'btn btn-filters';
      this.filtersButton.type = 'button';

      if (this.filtersButton.dataset) {
          this.filtersButton.dataset.open = 'false';
      } else {
          this.filtersButton.setAttribute("data-open", 'false')
      }

      this.filtersButton.innerHTML = t('show_filters');

      var functionsClick =  function() {
        var state = false
        if (this.dataset) {
          state = this.dataset.open
        } else {
          state = this.getAttribute("data-open")
        }
        if(state === 'true') {
          if (this.dataset) {
            this.dataset.open = 'false';
          } else {
            this.setAttribute("data-open",'false')
          }
          if (self.widget.filtersObj.filtersElement.dataset) {
            self.widget.filtersObj.filtersElement.dataset.open = 'false';
          } else {
            self.widget.filtersObj.filtersElement.setAttribute("data-open", 'false');
          }
        } else {
          if (this.dataset) {
            this.dataset.open = 'true';
          } else {
            this.setAttribute("data-open",'true')
          }
          if (self.widget.filtersObj.filtersElement.dataset) {
            self.widget.filtersObj.filtersElement.dataset.open = 'true';
          } else {
            self.widget.filtersObj.filtersElement.setAttribute("data-open", 'true');
          }
        }
      }

      this.filtersButton.addEventListener('click', functionsClick);

      this.filtersButtonImg = document.createElement('span');
      this.filtersButtonImg.className = 'btn-filters__arrow';
      this.filtersButtonImg.style.backgroundImage = 'url(' + easyPackConfig.iconsUrl + 'filters.png)';

      this.filtersButton.appendChild(this.filtersButtonImg);


      if(easyPackConfig.filters) {
          var mobileFilters = this.filtersButton.cloneNode(true)

          mobileFilters.className = mobileFilters.className + ' visible-xs';
          mobileFilters.addEventListener('click', functionsClick);
          this.searchElement.appendChild(mobileFilters);

          this.filtersButton.className = this.filtersButton.className + ' hidden-xs';
          this.searchButtonSpan.appendChild(this.filtersButton);
      }

            this.searchButtonSpan.appendChild(this.searchButton);
      this.searchGroup.appendChild(this.searchInput);
      this.searchGroup.appendChild(this.searchButtonSpan);
      this.searchElement.appendChild(this.searchGroup);

      return this.searchElement;
    },
    render: function() {
      return this.searchElement;
    },
    rerender: function() {
      this.searchInput.placeholder = t('search_by_city_or_address');
      return this.searchElement;
    }
  };;

  var filtersWidget = function(widget) {
    this.widget = widget;
    if (easyPackConfig.points.functions.length > 0) {
      this.widget.isFilter = true
    }
    this.currentFilters = easyPackConfig.points.functions || [];
    this.build();

    return this;
  };

  filtersWidget.prototype = {
    build: function() {
      var self = this;

      this.filtersElement = document.createElement('div');
      this.filtersElement.className = 'filters-widget';

      this.filtersLoadingElement = document.createElement('div');
      this.filtersLoadingElement.className = 'filters-widget__loading';

      if (this.filtersElement.dataset) {
        this.filtersElement.dataset.open = 'false';
      } else {
        this.filtersElement.setAttribute("data-open", 'false')
      }

      this.filtersList = document.createElement('ul');
      this.filtersList.className = 'filters-widget__list';

      if (easyPackConfig.filters) {
        module.api.filters({'source': 'geov4_pl'}, function (result) {
          for (var i = 0; i < result.length; i++) {
            var listElem = document.createElement('li');
            listElem.className = 'filters-widget__elem';

            var checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.id = result[i].name;

            if (checkbox.dataset) {
                checkbox.dataset.filter = result[i].name;
            } else {
                checkbox.setAttribute("data-filter", result[i].name)
            }

            if (helpers.in(result[i].name, easyPackConfig.points.functions)) {
              checkbox.checked = 'checked'
            }
            (function (checkbox, self) {
              checkbox.addEventListener('click', function () {
                self.widget.isFilter = true;
                if (this.checked) {
                  if (this.dataset) {
                    self.currentFilters.push(this.dataset.filter);
                  } else {
                    self.currentFilters.push(this.getAttribute('data-filter'));
                  }
                } else {
                  var index;

                  if (this.dataset) {
                    index = self.currentFilters.indexOf(this.dataset.filter)
                  } else {
                      index = self.currentFilters.indexOf(this.getAttribute('data-filter'));
                  }
                  self.currentFilters.splice(index, 1);
                  if (self.currentFilters.length === 0) {
                    self.widget.isFilter = false;
                    if ((self.currentTypes === undefined || self.currentTypes.length > 0) && !typesHelpers.isOnlyAdditionTypes(self.widget.currentTypes.filter(function (e) {return e;}), typesHelpers.getExtendedCollection())) {
                      self.refreshAllTypes();
                    }
                  }
                }
                self.widget.loadClosestPoints([], true, self.currentFilters);
                if (self.currentFilters.length !== 0) {
                  self.widget.clusterer.clearMarkers();
                }

                self.getPointsByFilter();
              });
            })(checkbox, self);

            var label = document.createElement('label');
            label.htmlFor = result[i].name;
            var title = result[i][easyPackConfig.defaultLocale] === undefined ? result[i].name : result[i][easyPackConfig.defaultLocale];
            label.innerHTML = title;

            listElem.appendChild(checkbox);
            listElem.appendChild(label);
            self.filtersList.appendChild(listElem);
          }
        });
      }

      this.filtersElement.appendChild(this.filtersLoadingElement);
      this.filtersElement.appendChild(this.filtersList);

      return this.filtersElement;
    },
    refreshAllTypes: function() {
      this.widget.clusterer.clearMarkers();
      this.widget.showType(this.widget.currentTypes[0], this.widget.currentTypes.splice(1,this.widget.currentTypes.length));
    },
    getPointsByFilter: function() {
      var self = this;

      if(this.currentFilters.length > 0 && this.widget.currentTypes.length > 0) {
        self.filtersElement.className = 'filters-widget loading';
        this.widget.clusterer.clearMarkers();
        self.widget.listObj.clear();
        for(var i = 0; i < self.widget.currentTypes.length; i++) {
          var type = self.widget.currentTypes[i];
          if (!typesHelpers.isOnlyAdditionTypes(self.widget.currentTypes.filter(function (e) {return e;}), typesHelpers.getExtendedCollection())) {
            var markers = (self.widget.allMarkers[type] || []).filter(function (value) {
              return helpers.all(self.currentFilters, value.point.functions);
            })
            markers.forEach(function (marker) {
                self.widget.listObj.addPoint(marker.point, self.widget.addListener(marker), type);
              }
            )

            self.widget.clusterer.addMarkers(markers);
          }

        }
        self.filtersElement.className = 'filters-widget';
        self.widget.statusBarObj.hide();
      } else {
        this.widget.clusterer.clearMarkers();
        self.filtersElement.className = 'filters-widget';
        self.widget.listObj.clear();
        for(var i = 0; i < self.widget.currentTypes.length; i++) {
          if (!typesHelpers.isOnlyAdditionTypes(self.widget.currentTypes.filter(function (e) {return e;}), typesHelpers.getExtendedCollection())) {
            this.widget.showType(self.widget.currentTypes[i]);
          }
        }
      }
    },
    addPoints: function(points) {
      var self = this,
          markers = [],
          pointsData = [];

      for(var i = 0; i < points.length; i++) {
        var point = points[i],
            marker = self.widget.createMarker(point, null);

        pointsData.push(point);

      }

      self.widget.isFilter = true;
    },
    render: function() {
      return this.filtersElement;
    },
    rerender: function() {
      return this.filtersElement;
    }
  };;

  var typesFilter = function(selectedTypes, params, kind) {
    this.params = params;
    this.kind = kind || 'checkbox';
    this.selectedTypes = selectedTypes;
    this.build(selectedTypes);
  };

  typesFilter.prototype = {
    build: function(selectedTypes) {
      this.selectedTypes = selectedTypes;
      this.wrapper = document.createElement('div');
      this.wrapper.className = 'type-filter';

      this.renderCurrentType();

      this.listWrapper = document.createElement('div');
      this.listWrapper.className = 'list-wrapper';

      this.list = document.createElement('ul');
      this.list.className = 'types-list';
      this.listWrapper.appendChild(this.list);

      this.params.style.sheet.insertRule('.easypack-widget .type-filter .type-checkbox { background: url(' + easyPackConfig.map.typeSelectedIcon + ') no-repeat center; }', 0);
      this.params.style.sheet.insertRule('.easypack-widget .type-filter .type-radio { background: url(' + easyPackConfig.map.typeSelectedRadio + ') no-repeat 0 -27px; }', 0);

    this.addTypes();
  },
  getJoinedCurrentTypes: function () {
      return this.selectedTypes.map(function (value) {
          if (typesHelpers.isParent(value, typesHelpers.getExtendedCollection())) {
              var extendedObject =  typesHelpers.getObjectForType(value, typesHelpers.getExtendedCollection());
              if (extendedObject !== null && extendedObject.name) {
                  return t(extendedObject.name);
              }
              return t(value);
          }
          if (typesHelpers.getAllAdditionalTypes(typesHelpers.getExtendedCollection()).indexOf(value) === -1) return t(value);
      }).filter(function (value) { return value }).join(', ');
  },
  renderCurrentType: function () {
    var self = this;

    this.currentTypeWrapper = document.createElement('div');
    this.currentTypeWrapper.className = 'current-type-wrapper';
    this.wrapper.appendChild(this.currentTypeWrapper);

    this.typeSelectButton = document.createElement('button');
    this.typeSelectButton.className = 'btn btn-select-type';
    this.typeSelectButton.innerHTML = '&#9660;';

    this.typeSelectButton.onclick = function () {
      if (self.listWrapper.offsetParent === null) {
        self.listWrapper.style.display = 'block';
      } else {
        self.listWrapper.style.display = 'none';
      }
    };

    this.currentTypeWrapper.appendChild(this.typeSelectButton);

    this.currentType = document.createElement('div');
    this.currentType.className = 'current-type';
    if (easyPackConfig.mobileFiltersAsCheckbox) {
        this.currentType.innerHTML = this.getJoinedCurrentTypes();
    } else {
        this.currentType.innerHTML = t(this.selectedTypes[0]);
    }

    if (this.selectedTypes[0] !== undefined) {
      this.currentType.style.backgroundImage = 'url(' + easyPackConfig.iconsUrl + this.selectedTypes[0].replace('_only', '') + '.png)';
    }
    this.currentTypeWrapper.appendChild(this.currentType);
  },
  debounce: function(func, wait, immediate) {
    var timeout;
    return function() {
      var context = this, args = arguments;
      var later = function() {
        timeout = null;
        if (!immediate) func.apply(context, args);
      };
      var callNow = immediate && !timeout;
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
      if (callNow) func.apply(context, args);
    };
  },
  updateDataClass: function (type, dataButton, extendedObject, currentTypes) {
    dataButton.classList.add('some');
    dataButton.setAttribute('data-checked', 'true');
    dataButton.parentNode.setAttribute('data-checked', 'true');

    if (typesHelpers.isAllChildSelected(type, currentTypes, extendedObject)) {
      dataButton.classList.remove('some');
      dataButton.classList.remove('none');
      dataButton.setAttribute('data-checked', 'true');
      dataButton.parentNode.setAttribute('data-checked', 'true');
      dataButton.classList.add('all');
    }

    if (typesHelpers.isNoOneChildSelected(type, currentTypes, extendedObject)) {
      dataButton.classList.remove('some');
      dataButton.classList.remove('all');
      dataButton.setAttribute('data-checked', "false");
      dataButton.parentNode.setAttribute('data-checked', "false");
      dataButton.classList.add('none');
    }
  },
  addTypes: function () {
    var types = easyPackConfig.points.types,
        extendedTypes = typesHelpers.getExtendedCollection(),
        self = this;
    self.items = [];
    self.checked = 0;
    types.forEach(function (type) {
      var extendedObject = helpers.findObjectByPropertyName(extendedTypes, type) || {};
      type = type === 'pok' ? 'pop' : type;
      var iconUrl = 'url(' + easyPackConfig.iconsUrl + type.replace('_only', '') + '.png)',
          dataType = type,
          enabled = extendedObject.enabled || true,
          pointerIcon = 'url("' + easyPackConfig.map.tooltipPointerIcon + '") no-repeat left bottom',
          markerUrl = easyPackConfig.markersUrl + type.replace('_only', '') + '.png',
          typeTitle = t(type),
          description = t(type + '_description');

      self.checkedParent = false;
      var typeItem = document.createElement('li');
      if (extendedObject.childs === undefined) {
        typeItem.style.backgroundImage = iconUrl;

      }

      if (extendedObject.childs !== undefined) {
        typeItem.classList.add('has-subtypes');
        typeItem.classList.add('group');
      }
      typeItem.setAttribute('data-type', dataType);

      if (helpers.in(type, self.selectedTypes)) {
        typeItem.setAttribute('data-checked', 'true');
        self.checked++;
      }

      if (typeof extendedObject === 'object' && helpers.in(type, self.selectedTypes)) {
        typeItem.setAttribute('data-checked', 'true');
        self.checked++;
      }

      var tooltipWrapper = document.createElement('div');
      tooltipWrapper.className = 'tooltip-wrapper';
      tooltipWrapper.style.background = pointerIcon;

      if (extendedObject.childs !== undefined) {
        var dropdownWrapper = document.createElement('div');
        dropdownWrapper.className = 'dropdown-wrapper';

        var subTypesList = document.createElement('ul');
        subTypesList.className = 'dropdown-subtypes';

        extendedObject.childs.unshift(JSON.parse('{'+ '"' + type + '"' + ': { "enabled": "true"}}'));

        extendedObject.childs.forEach(function (child) {
          Object.keys(child).forEach(function (childType) {
            if (child[childType].enabled === true) {
              var subType = typesHelpers.getNameForType(childType);
              var liElem = document.createElement('li');
              liElem.style.backgroundImage = 'url(' + easyPackConfig.iconsUrl + subType.replace('_only', '') + '.png)';
              liElem.setAttribute('data-type', subType);

              var button = document.createElement('button');
              button.type = 'button';
              button.className = 'btn btn-' + self.kind + ' type-' + self.kind;

              var label = document.createElement('span');
              label.className = 'label';

              var labelText = document.createTextNode(t(subType.replace('_only', '')));
              label.appendChild(labelText);

              liElem.appendChild(button);
              liElem.appendChild(label);
              subTypesList.appendChild(liElem);
              self.items.push(button);

              if (helpers.in(subType, self.selectedTypes)) {

                liElem.setAttribute('data-checked', 'true');
                self.checked++;
              }
            }
          });


        });
        dropdownWrapper.appendChild(subTypesList);
      }

      var tooltip = document.createElement('div');
      tooltip.className = 'type-tooltip';
      tooltipWrapper.appendChild(tooltip);

      var tooltipIconWrapper = document.createElement('div');
      tooltipIconWrapper.className = 'icon-wrapper';
      tooltip.appendChild(tooltipIconWrapper);

      var tooltipIcon = document.createElement('img');
      tooltipIcon.src = markerUrl.replace('_only', '');
      tooltipIconWrapper.appendChild(tooltipIcon);

      var tooltipDescription = document.createElement('div');
      tooltipDescription.className = 'description';
      tooltipDescription.innerHTML = description;
      tooltip.appendChild(tooltipDescription);

      var button = document.createElement('button');
      button.type = 'button';

      var tooltipClass = extendedObject.childs === undefined ? 'has-tooltip ' : '';

      button.className = tooltipClass + 'btn btn-' + self.kind + ' type-' + self.kind;

      var label = document.createElement('span');
      label.className = tooltipClass + 'label';

      if (extendedObject.childs !== undefined) {
        button.classList.add('main-type');
        var arrow = document.createElement('span');
        arrow.className = 'arrow';
        arrow.style.background = 'url("' + easyPackConfig.map.pointerIcon + '") no-repeat center bottom / 15px';
        arrow.addEventListener('click', function (event) {
          event.stopPropagation();
            if (this.filtersElement.dataset) {
                this.filtersElement.dataset.dropdown = 'false';
            } else {
                this.filtersElement.setAttribute("data-dropdown", 'false')
            }
          var state = this.parentNode.dataset.dropdown;
          if (state === undefined || state === 'closed') {
            this.parentNode.dataset.dropdown = 'open';
          } else {
            this.parentNode.dataset.dropdown = 'closed';
          }
        });
      }
      if (extendedObject.name) {
        typeTitle = t(extendedObject.name)
      }
      var labelText = document.createTextNode(typeTitle);
      label.appendChild(labelText);
      if (enabled === false) {
        button.setAttribute('readonly', true);
        button.style.setProperty('cursor', 'not-allowed');
        label.style.setProperty('cursor', 'not-allowed');
      }
      self.items.push(button);
      typeItem.appendChild(button);
      typeItem.appendChild(label);

      if (extendedObject.childs !== undefined) {
        typeItem.appendChild(arrow);
        typeItem.appendChild(dropdownWrapper);
      }
        if (helpers.in(type, easyPackConfig.points.allowedToolTips)) {
          typeItem.appendChild(tooltipWrapper);
        }
      if (extendedObject.enabled !== undefined) {
        if (extendedObject.enabled === false){
        } else self.list.appendChild(typeItem);
      } else {
        self.list.appendChild(typeItem);
      }
    });

    self.wrapper.appendChild(self.listWrapper);
  },
  setKind: function (kind) {
    this.kind = kind;

    var listElements = this.list.getElementsByClassName('btn');
    var i;

    for (i = 0; i < listElements.length; i++) {
      var button = listElements[i];
      button.className = 'btn btn-' + this.kind + ' type-' + this.kind;
    }
  },
  update: function (currentTypes) {
    var buttons = this.list.getElementsByTagName('li');
    var extendedTypes = typesHelpers.getExtendedCollection()
    var i;
    for (i = 0; i < buttons.length; i++) {
      var button = buttons[i];
      var type = button.getAttribute('data-type');
      if (helpers.in(type, currentTypes)) {
        button.setAttribute('data-checked', 'true');
      } else {
        button.setAttribute('data-checked', 'false');
      }

      var extendedObject = helpers.findObjectByPropertyName(extendedTypes, type) || {};
      if (button.querySelector('button.main-type')) {
        this.updateDataClass(type, button.querySelector('button.main-type'),  extendedObject, currentTypes)
      }

    }
    this.currentType.innerHTML = t(currentTypes[0]);
      if (easyPackConfig.mobileFiltersAsCheckbox) {
          this.currentType.innerHTML = this.getJoinedCurrentTypes();
      } else {
          this.currentType.innerHTML = t(this.selectedTypes[0]);
      }
    if (currentTypes.length === 0) {
      this.currentType.innerHTML = t('select');
    }
    if (currentTypes[0] !== undefined) {
      this.currentType.style.backgroundImage = 'url(' + easyPackConfig.iconsUrl + currentTypes[0].replace('_only', '') + '.png)';
      this.currentType.style.paddingLeft = '42px';
    } else {
      this.currentType.style.backgroundImage = 'none';
      this.currentType.style.paddingLeft = '10px';
    }

  },
  render: function (placeholder) {
    if (this.items.length > 1) {
      placeholder.appendChild(this.wrapper);
    }
    this.placeholder = placeholder;
  },
  rerender: function () {
    var items = this.list.getElementsByTagName("li");
    for (var i = 0; i < items.length; ++i) {
      var liElement = items[i];
      if (liElement.getElementsByClassName('description').length > 0) {
        liElement.getElementsByClassName('description')[0].innerHTML = t(liElement.dataset.type + '_description');
      }
      liElement.getElementsByClassName('label')[0].innerHTML = t(liElement.dataset.type);
    }
  }
};
;

  var viewChooser = function(params) {
    this.params = params;

    this.params.style.sheet.insertRule('.easypack-widget .view-chooser .map-btn { background: url(' + easyPackConfig.map.mapIcon + ') no-repeat left; }', 0);
    this.params.style.sheet.insertRule('.easypack-widget .view-chooser .list-btn { background: url(' + easyPackConfig.map.listIcon + ') no-repeat left; }', 0);

    this.build();
  };

  viewChooser.prototype = {
    build: function() {
      this.wrapper = document.createElement('div');
      this.wrapper.className = 'view-chooser';

      this.mapWrapper = document.createElement('div');
      this.mapWrapper.className = 'map-wrapper';
      this.mapWrapper.setAttribute('data-active', 'true');
      this.mapWrapper.onclick = function(){
        self.listWrapper.setAttribute('data-active', 'false');
        this.setAttribute('data-active', 'true');
        self.params.mapElement.style.display = 'block';
        self.params.list.listElement.style.display = 'none';
      };
      this.wrapper.appendChild(this.mapWrapper);
      var self = this;

      this.mapButton = document.createElement('div');
      this.mapButton.className = 'btn map-btn';
      this.mapButton.innerHTML = t('map');

      this.mapWrapper.appendChild(this.mapButton);

      this.listWrapper = document.createElement('div');
      this.listWrapper.className = 'list-wrapper';
      this.listWrapper.onclick = function(){
        self.mapWrapper.setAttribute('data-active', 'false');
        this.setAttribute('data-active', 'true');
        self.params.mapElement.style.display = 'none';
        self.params.list.listElement.style.display = 'block';
      };
      this.wrapper.appendChild(this.listWrapper);

      this.listButton = document.createElement('div');
      this.listButton.className = 'btn list-btn';
      this.listButton.innerHTML = t('list');
      this.listWrapper.appendChild(this.listButton);
    },
    resetState: function() {
      this.listWrapper.setAttribute('data-active', 'false');
      this.mapWrapper.setAttribute('data-active', 'true');
      this.params.mapElement.style.display = 'block';
      this.params.list.listElement.style.display = 'none';
    },
    render: function(placeholder) {
      placeholder.appendChild(this.wrapper);
    },
    rerender: function() {
      this.mapButton.innerHTML = t('map');
      this.listButton.innerHTML = t('list');
    }
  };;

  return module;

}());

easyPack.asyncInit();

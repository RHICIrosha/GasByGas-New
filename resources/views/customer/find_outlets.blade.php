@extends('layouts.customer')

@section('content')
<!-- Main Content -->
<div class="main-content" id="mainContent">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold">Find Outlets</h2>
            <p class="lead">Locate the nearest GasByGas outlet in your area</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('orders.create') }}" class="btn btn-warning">
                <i class="fas fa-plus-circle me-2"></i>Place New Order
            </a>
        </div>
    </div>

    <!-- Map and Outlets Section -->
    <div class="row">
        <!-- Outlets List Sidebar -->
        <div class="col-md-4 mb-4">
            <div class="dashboard-card p-0 h-100 d-flex flex-column">
                <!-- Search and Filter -->
                <div class="p-3 border-bottom">
                    <div class="input-group">
                        <input type="text" id="outlet-search" class="form-control" placeholder="Search outlets...">
                        <button class="btn btn-warning" type="button" id="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="show-available-only" checked>
                            <label class="form-check-label" for="show-available-only">
                                Available outlets only
                            </label>
                        </div>
                        <select class="form-select form-select-sm w-auto" id="sort-outlets">
                            <option value="name">Sort by Name</option>
                            <option value="distance" selected>Sort by Distance</option>
                        </select>
                    </div>
                </div>

                <!-- Outlets List -->
                <div class="outlet-list flex-grow-1 overflow-auto" style="max-height: 500px;">
                    <div id="outlets-container">
                        @foreach($outlets as $outlet)
                        <div class="outlet-item p-3 border-bottom" data-outlet-id="{{ $outlet->id }}" data-lat="{{ $outlet->latitude ?? 6.9271 }}" data-lng="{{ $outlet->longitude ?? 79.8612 }}">
                            <h5 class="mb-1">{{ $outlet->name }}</h5>
                            <p class="mb-1 text-muted small">{{ $outlet->address }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge {{ $outlet->has_stock && $outlet->is_accepting_orders ? 'bg-success' : 'bg-danger' }}">
                                    {{ $outlet->has_stock && $outlet->is_accepting_orders ? 'Available' : 'Unavailable' }}
                                </span>
                                <button class="btn btn-sm btn-outline-primary view-outlet-btn" data-outlet-id="{{ $outlet->id }}">
                                    <i class="fas fa-info-circle me-1"></i> Details
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Area -->
        <div class="col-md-8 mb-4">
            <div class="dashboard-card h-100">
                <div id="outlets-map" style="width: 100%; height: 500px; border-radius: 10px;"></div>
            </div>
        </div>
    </div>

    <!-- Currently Selected Outlet -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="dashboard-card" id="outlet-details-card" style="display: none;">
                <div class="row">
                    <div class="col-md-8">
                        <h4 id="selected-outlet-name">Outlet Name</h4>
                        <div class="d-flex mb-3">
                            <span class="badge bg-success me-2" id="selected-outlet-status">Available</span>
                            <span class="text-muted" id="selected-outlet-code">OUT001</span>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Address</h6>
                            <p id="selected-outlet-address">123 Main Street, Colombo</p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Contact Information</h6>
                            <p class="mb-1">
                                <i class="fas fa-phone-alt me-2"></i>
                                <span id="selected-outlet-phone">0112345678</span>
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Operating Hours</h6>
                            <p>Monday - Saturday: 8:00 AM - 6:00 PM</p>
                            <p>Sunday: Closed</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="#" id="selected-outlet-directions" class="btn btn-outline-secondary mb-3" target="_blank">
                            <i class="fas fa-directions me-2"></i>Get Directions
                        </a>
                        <a href="#" id="selected-outlet-order" class="btn btn-warning d-block">
                            <i class="fas fa-shopping-cart me-2"></i>Order from this Outlet
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap" defer></script>
<script>
    let map;
    let markers = [];
    let outlets = @json($outlets);
    let currentPosition = null;

    function initMap() {
        // Center map on Sri Lanka
        const sriLankaCenter = { lat: 7.8731, lng: 80.7718 };

        map = new google.maps.Map(document.getElementById("outlets-map"), {
            center: sriLankaCenter,
            zoom: 8,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
            zoomControl: true,
        });

        // Try to get user's location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    currentPosition = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    // Add marker for user location
                    new google.maps.Marker({
                        position: currentPosition,
                        map: map,
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 10,
                            fillColor: "#4285F4",
                            fillOpacity: 1,
                            strokeColor: "#ffffff",
                            strokeWeight: 2,
                        },
                        title: "Your Location"
                    });

                    // Center map on user location
                    map.setCenter(currentPosition);
                    map.setZoom(10);

                    // Update outlet distances and sort
                    if (document.getElementById('sort-outlets').value === 'distance') {
                        updateDistances();
                        sortOutletsByDistance();
                    }
                },
                () => {
                    // Handle geolocation error
                    console.log("Error: The Geolocation service failed.");
                }
            );
        }

        // Add markers for all outlets
        addOutletMarkers();

        // Set up event listeners
        setupEventListeners();
    }

    function addOutletMarkers() {
        // Clear existing markers
        markers.forEach(marker => marker.setMap(null));
        markers = [];

        // Add a marker for each outlet
        outlets.forEach(outlet => {
            // Generate a random position if lat/lng not available
            // In a real app, you would have actual coordinates for each outlet
            const position = {
                lat: outlet.latitude ?? (6.9271 + (Math.random() - 0.5) * 2),
                lng: outlet.longitude ?? (79.8612 + (Math.random() - 0.5) * 2)
            };

            // Store position with outlet for distance calculation
            outlet.position = position;

            const isAvailable = outlet.has_stock && outlet.is_accepting_orders;

            const marker = new google.maps.Marker({
                position: position,
                map: map,
                title: outlet.name,
                icon: {
                    url: isAvailable ? '{{ asset("img/map-marker-available.png") }}' : '{{ asset("img/map-marker-unavailable.png") }}',
                    scaledSize: new google.maps.Size(30, 40)
                }
            });

            // Store the outlet id with the marker
            marker.outletId = outlet.id;

            // Add click event to marker
            marker.addListener("click", () => {
                displayOutletDetails(outlet);
                highlightOutletInList(outlet.id);
            });

            markers.push(marker);
        });
    }

    function setupEventListeners() {
        // Outlet search
        document.getElementById('outlet-search').addEventListener('input', filterOutlets);
        document.getElementById('search-btn').addEventListener('click', filterOutlets);

        // Show available only checkbox
        document.getElementById('show-available-only').addEventListener('change', filterOutlets);

        // Sort dropdown
        document.getElementById('sort-outlets').addEventListener('change', (e) => {
            if (e.target.value === 'distance') {
                sortOutletsByDistance();
            } else {
                sortOutletsByName();
            }
        });

        // Outlet item click
        document.querySelectorAll('.outlet-item').forEach(item => {
            item.addEventListener('click', function() {
                const outletId = this.getAttribute('data-outlet-id');
                const outlet = outlets.find(o => o.id == outletId);

                // Pan to marker
                map.panTo(outlet.position);
                map.setZoom(14);

                // Display outlet details
                displayOutletDetails(outlet);

                // Highlight in list
                highlightOutletInList(outletId);
            });
        });

        // View outlet button
        document.querySelectorAll('.view-outlet-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent triggering the parent's click event
                const outletId = this.getAttribute('data-outlet-id');
                const outlet = outlets.find(o => o.id == outletId);
                displayOutletDetails(outlet);
            });
        });
    }

    function displayOutletDetails(outlet) {
        // Show the details card
        const detailsCard = document.getElementById('outlet-details-card');
        detailsCard.style.display = 'block';

        // Update the details
        document.getElementById('selected-outlet-name').textContent = outlet.name;
        document.getElementById('selected-outlet-code').textContent = outlet.code;
        document.getElementById('selected-outlet-address').textContent = outlet.address;
        document.getElementById('selected-outlet-phone').textContent = outlet.contact_number;

        // Update status badge
        const statusBadge = document.getElementById('selected-outlet-status');
        if (outlet.has_stock && outlet.is_accepting_orders) {
            statusBadge.textContent = 'Available';
            statusBadge.className = 'badge bg-success me-2';
        } else {
            statusBadge.textContent = 'Unavailable';
            statusBadge.className = 'badge bg-danger me-2';
        }

        // Update buttons
        const orderBtn = document.getElementById('selected-outlet-order');
        orderBtn.href = "{{ route('orders.create') }}?outlet_id=" + outlet.id;

        if (!outlet.has_stock || !outlet.is_accepting_orders) {
            orderBtn.classList.add('disabled');
        } else {
            orderBtn.classList.remove('disabled');
        }

        // Set up directions link
        const directionsLink = document.getElementById('selected-outlet-directions');
        directionsLink.href = `https://www.google.com/maps/dir/?api=1&destination=${outlet.position.lat},${outlet.position.lng}`;

        // Scroll to details
        detailsCard.scrollIntoView({ behavior: 'smooth' });
    }

    function highlightOutletInList(outletId) {
        // Remove highlight from all items
        document.querySelectorAll('.outlet-item').forEach(item => {
            item.classList.remove('bg-light');
        });

        // Add highlight to selected item
        const selectedItem = document.querySelector(`.outlet-item[data-outlet-id="${outletId}"]`);
        if (selectedItem) {
            selectedItem.classList.add('bg-light');

            // Scroll the item into view in the list
            const container = document.querySelector('.outlet-list');
            container.scrollTop = selectedItem.offsetTop - container.offsetTop;
        }
    }

    function filterOutlets() {
        const searchTerm = document.getElementById('outlet-search').value.toLowerCase();
        const showAvailableOnly = document.getElementById('show-available-only').checked;

        let filteredOutlets = outlets;

        // Apply search filter
        if (searchTerm) {
            filteredOutlets = filteredOutlets.filter(outlet =>
                outlet.name.toLowerCase().includes(searchTerm) ||
                outlet.address.toLowerCase().includes(searchTerm) ||
                outlet.code.toLowerCase().includes(searchTerm)
            );
        }

        // Apply availability filter
        if (showAvailableOnly) {
            filteredOutlets = filteredOutlets.filter(outlet =>
                outlet.has_stock && outlet.is_accepting_orders
            );
        }

        // Update the list
        updateOutletsList(filteredOutlets);

        // Update markers
        updateMarkers(filteredOutlets);
    }

    function updateOutletsList(filteredOutlets) {
        const container = document.getElementById('outlets-container');
        container.innerHTML = '';

        if (filteredOutlets.length === 0) {
            container.innerHTML = `
                <div class="p-4 text-center">
                    <p class="mb-0">No outlets found matching your criteria.</p>
                </div>
            `;
            return;
        }

        filteredOutlets.forEach(outlet => {
            const distanceDisplay = outlet.distance ?
                `<span class="text-muted small">${outlet.distance.toFixed(1)} km away</span>` : '';

            const outletHtml = `
                <div class="outlet-item p-3 border-bottom" data-outlet-id="${outlet.id}" data-lat="${outlet.position.lat}" data-lng="${outlet.position.lng}">
                    <h5 class="mb-1">${outlet.name}</h5>
                    <p class="mb-1 text-muted small">${outlet.address}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge ${outlet.has_stock && outlet.is_accepting_orders ? 'bg-success' : 'bg-danger'}">
                                ${outlet.has_stock && outlet.is_accepting_orders ? 'Available' : 'Unavailable'}
                            </span>
                            ${distanceDisplay}
                        </div>
                        <button class="btn btn-sm btn-outline-primary view-outlet-btn" data-outlet-id="${outlet.id}">
                            <i class="fas fa-info-circle me-1"></i> Details
                        </button>
                    </div>
                </div>
            `;

            container.innerHTML += outletHtml;
        });

        // Reattach event listeners
        document.querySelectorAll('.outlet-item').forEach(item => {
            item.addEventListener('click', function() {
                const outletId = this.getAttribute('data-outlet-id');
                const outlet = outlets.find(o => o.id == outletId);

                // Pan to marker
                map.panTo(outlet.position);
                map.setZoom(14);

                // Display outlet details
                displayOutletDetails(outlet);

                // Highlight in list
                highlightOutletInList(outletId);
            });
        });

        document.querySelectorAll('.view-outlet-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const outletId = this.getAttribute('data-outlet-id');
                const outlet = outlets.find(o => o.id == outletId);
                displayOutletDetails(outlet);
            });
        });
    }

    function updateMarkers(filteredOutlets) {
        // Hide all markers
        markers.forEach(marker => {
            marker.setMap(null);
        });

        // Only show markers for filtered outlets
        markers.forEach(marker => {
            const outlet = filteredOutlets.find(o => o.id == marker.outletId);
            if (outlet) {
                marker.setMap(map);
            }
        });
    }

    function updateDistances() {
        if (!currentPosition) return;

        outlets.forEach(outlet => {
            if (outlet.position) {
                outlet.distance = calculateDistance(
                    currentPosition.lat, currentPosition.lng,
                    outlet.position.lat, outlet.position.lng
                );
            }
        });
    }

    function sortOutletsByDistance() {
        if (!currentPosition) return;

        // First update distances
        updateDistances();

        // Sort outlets by distance
        const sortedOutlets = [...outlets].sort((a, b) => {
            if (!a.distance) return 1;
            if (!b.distance) return -1;
            return a.distance - b.distance;
        });

        updateOutletsList(sortedOutlets);
    }

    function sortOutletsByName() {
        const sortedOutlets = [...outlets].sort((a, b) => {
            return a.name.localeCompare(b.name);
        });

        updateOutletsList(sortedOutlets);
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        // Haversine formula to calculate distance between two points
        const R = 6371; // Radius of the earth in km
        const dLat = deg2rad(lat2 - lat1);
        const dLon = deg2rad(lon2 - lon1);
        const a =
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
            Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const distance = R * c; // Distance in km
        return distance;
    }

    function deg2rad(deg) {
        return deg * (Math.PI/180);
    }

    // Initialize map when the page loads
    window.initMap = initMap;
</script>
@endsection

@section('styles')
<style>
    .outlet-item {
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .outlet-item:hover {
        background-color: #f8f9fa;
    }

    .outlet-list {
        scrollbar-width: thin;
    }

    .outlet-list::-webkit-scrollbar {
        width: 6px;
    }

    .outlet-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .outlet-list::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 3px;
    }

    .outlet-list::-webkit-scrollbar-thumb:hover {
        background: #ccc;
    }
</style>
@endsection

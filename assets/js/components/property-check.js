document.addEventListener('DOMContentLoaded', function () {

    var step = 0;
    var owner = '';
    var timing = '';
    var propertyLat = null;
    var propertyLng = null;

    // ===== ACTUAL LOT SIZE (COMMENTED OUT) =====
    // var actualLotSize = null;

    var OWNER_OPTIONS = [
        'I own it',
        'Buying it',
        'Family owns it',
        'I rent'
    ];

    var TIMING_OPTIONS = [
        'ASAP',
        '1-3 months',
        '3-6 months',
        'Researching'
    ];

    function updateRunButton() {
        var runButton = document.getElementById('run');

        if (!runButton) {
            return;
        }

        if (owner && timing) {
            runButton.disabled = false;
            runButton.style.opacity = '1';
        } else {
            runButton.disabled = true;
            runButton.style.opacity = '0.4';
        }
    }

    function showStep(n) {

        var bars = document.querySelectorAll('.gfx-bar');
        var labels = document.querySelectorAll('.gfx-label');

        step = n;

        for (var i = 0; i < 5; i++) {

            var el = document.getElementById('s' + i);
            if (el) {
                el.classList.toggle('active', i === n);
            }
        }

        for (var k = 0; k < labels.length; k++) {
            labels[k].classList.toggle('on', k <= Math.min(n, 3)
            );
        }

        for (var j = 0; j < bars.length; j++) {
            var shouldBeOn = j < n;
            bars[j].classList.toggle('on', shouldBeOn);
        }
    }

    function buildChips(containerId, options, onPick) {
        var box = document.getElementById(containerId);

        if (!box) {
            return;
        }

        box.innerHTML = '';

        options.forEach(function (text) {
            var btnSteps = document.createElement('button');

            btnSteps.type = 'button';
            btnSteps.className = 'gfx-chip';
            btnSteps.textContent = text;
            btnSteps.setAttribute('aria-pressed', 'false');


            btnSteps.addEventListener('click', function () {
                var error = document.getElementById('e1');

                box.querySelectorAll('.gfx-chip').forEach(function (c) {
                    c.setAttribute('aria-pressed', 'false');
                });

                btnSteps.setAttribute('aria-pressed', 'true');

                onPick(text);

                if (error) {
                    error.classList.remove('show');
                }
            });

            box.appendChild(btnSteps);
        });
    }

    buildChips('own', OWNER_OPTIONS, function (v) {
        owner = v;
        updateRunButton();
    });

    buildChips('tim', TIMING_OPTIONS, function (v) {
        timing = v;
        updateRunButton();
    });

    updateRunButton();

    var suburbInput = document.getElementById('suburbInput');

    if (suburbInput) {
        suburbInput.addEventListener('input', function () {
            var error = document.getElementById('e0');

            if (error) {
                error.classList.remove('show');
            }
        });
    }

    /*
     * ======= STEP 0: VALIDATE ADDRESS =======
     */

    /*
     * ======= GOOGLE PLACES AUTOCOMPLETE ========
     */

    var suburbInput = document.getElementById('suburbInput');
    var selectedGooglePlace = null;

    var SYDNEY_BOUNDS = {
        north: -33.40,
        south: -34.20,
        east: 151.60,
        west: 150.50
    };

    if (suburbInput) {
        function initGoogleAutocomplete() {
            if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
                console.error('Google Maps Places API has not loaded.');
                return;
            }

            var autocomplete = new google.maps.places.Autocomplete(suburbInput, {
                bounds: SYDNEY_BOUNDS,
                componentRestrictions: {
                    country: 'au'
                },

                /*
                 * false = Sydney is preferred,
                 * but users can still select other
                 * Australian addresses.
                 */
                strictBounds: false,

                fields: [
                    'formatted_address',
                    'geometry',
                    'address_components',
                    'place_id',
                    'types'
                ]
            });

            autocomplete.addListener('place_changed', function () {
                var place = autocomplete.getPlace();

                if (!place || !place.geometry || !place.geometry.location) {
                    selectedGooglePlace = null;
                    propertyLat = null;
                    propertyLng = null;

                    console.error('Google did not return coordinates.');
                    return;
                }

                var isAustralia = false;

                if (Array.isArray(place.address_components)) {
                    place.address_components.forEach(function (component) {
                        if (component.types && component.types.includes('country') && component.short_name === 'AU') {
                            isAustralia = true;
                        }
                    });
                }

                if (!isAustralia) {
                    selectedGooglePlace = null;
                    propertyLat = null;
                    propertyLng = null;

                    console.error('Selected address is not in Australia.');
                    return;
                }

                /* SAVE GOOGLE PLACE */
                selectedGooglePlace = place;

                /* SAVE GOOGLE COORDINATES */
                propertyLat = place.geometry.location.lat();
                propertyLng = place.geometry.location.lng();

                /* Use Google's formatted address. */
                if (place.formatted_address) {
                    suburbInput.value = place.formatted_address;
                }
            });
        }

        /*
         * Google Maps may load after this JS.
         */
        if (typeof google !== 'undefined' && google.maps && google.maps.places) {
            initGoogleAutocomplete();
        } else {
            /* Wait for Google Maps script. */
            var googleCheckAttempts = 0;

            var googleCheck = setInterval(function () {
                googleCheckAttempts++;

                if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                    clearInterval(googleCheck);
                    initGoogleAutocomplete();
                }

                if (googleCheckAttempts >= 100) {
                    clearInterval(googleCheck);
                    console.error('Google Maps Places API did not load.');
                }
            }, 100);
        }

        /*
         * If the user manually changes the
         * address after selecting Google,
         * invalidate the saved coordinates.
         */
        suburbInput.addEventListener('input', function () {
            selectedGooglePlace = null;
            propertyLat = null;
            propertyLng = null;

            var error = document.getElementById('e0');

            if (error) {
                error.classList.remove('show');
            }
        });
    }

    /* ======= STEP 0: VALIDATE GOOGLE ADDRESS ======= */

    var goButton = document.getElementById('go1');


    if (suburbInput && goButton) {
        function updateGoButton() {
            if (suburbInput.value.trim() !== '') {
                goButton.style.opacity = '1';
                goButton.disabled = false;
            } else {
                goButton.style.opacity = '0.4';
                goButton.disabled = true;
            }
        }
        suburbInput.addEventListener('input', updateGoButton);
        updateGoButton();
    }

    if (goButton) {
        goButton.addEventListener('click', async function () {
            var input = document.getElementById('suburbInput');
            var error = document.getElementById('e0');
            var address = input ? input.value.trim() : '';

            if (error) {
                error.classList.remove('show');
            }

            if (!address) {
                if (error) {
                    error.textContent = 'Please enter a property address.';
                    error.classList.add('show');
                }

                return;
            }

            /* GOOGLE PLACE WAS NOT SELECTED */
            if (!selectedGooglePlace || !selectedGooglePlace.geometry || !selectedGooglePlace.geometry.location) {
                if (error) {
                    error.textContent = 'Please select your property from the address suggestions.';
                    error.classList.add('show');
                }

                return;
            }

            /* MAKE SURE COORDINATES EXIST */
            if (!Number.isFinite(propertyLat) || !Number.isFinite(propertyLng)) {
                if (error) {
                    error.textContent = 'We could not get the location of that property. Please select the address again.';
                    error.classList.add('show');
                }

                return;
            }

            goButton.disabled = true;
            goButton.textContent = 'Checking address...';

            try {
                showStep(1);
            } catch (err) {
                console.error('Address validation failed:', err);

                if (error) {
                    error.textContent = 'We could not verify the address. Please try again.';
                    error.classList.add('show');
                }
            } finally {
                goButton.disabled = false;
                goButton.textContent = 'Check my property';
            }
        });
    }

    /* ====== OPTIONS ====== */

    var backButton = document.getElementById('back0');

    if (backButton) {
        backButton.addEventListener('click', function () {
            showStep(0);
        });
    }

    var backButton = document.getElementById('back2');

    if (backButton) {
        backButton.addEventListener('click', function () {
            showStep(1);
        });
    }

    var backStartButton = document.getElementById('backToStart');

    if (backStartButton) {
        backStartButton.addEventListener('click', function () {
            showStep(0);
        });
    }

    var runButton = document.getElementById('run');

    if (runButton) {
        runButton.addEventListener('click', function () {

            if (!owner || !timing) {
                var error = document.getElementById('e1');

                if (error) {
                    error.classList.add('show');
                }

                return;
            }

            showStep(2);
            handlePropertyCheck();
        });
    }

    /* ===== STEP 2: PROPERTY API CHECK ==== */

    async function handlePropertyCheck() {
        var address = document.getElementById('suburbInput')?.value.trim() || '';
        var output = document.getElementById('suburbOutput');
        var jsonOutput = document.getElementById('jsonOutput');
        var status = document.getElementById('propertyCheckStatus');
        var retryRow = document.getElementById('retryRow');

        if (retryRow) {
            retryRow.style.display = 'none';
        }

        if (output) {
            output.textContent = '';
            output.classList.remove('show');
        }

        if (jsonOutput) {
            jsonOutput.style.display = 'none';
        }

        if (status) {
            status.innerHTML = '';
        }

        // Reset actual lot size for a new check (Commented out)
        // actualLotSize = null;

        var planningBase = 'https://mapprod3.environment.nsw.gov.au/arcgis/rest/services/ePlanning/Planning_Portal_Principal_Planning/MapServer';
        var hazardBase = 'https://mapprod3.environment.nsw.gov.au/arcgis/rest/services/ePlanning/Planning_Portal_Hazard/MapServer';
        var parcelBase = 'https://portal.spatial.nsw.gov.au/server/rest/services/NSW_Land_Parcel_Property_Theme_multiCRS/FeatureServer';

        function addStep(id, text) {
            var stepElement = document.createElement('div');

            stepElement.id = id;
            stepElement.className = 'check-step';

            stepElement.innerHTML = `
                <div class="check-step-content">
                    <span>${text}</span>
                </div>
                <div class="check-step-status">
                    <span class="check-loader"></span>
                </div>
            `;

            if (status) {
                status.appendChild(stepElement);
            }

            return stepElement;
        }

        function completeStep(stepElement, text) {
            if (!stepElement) {
                return;
            }

            var content = stepElement.querySelector('.check-step-content span');
            var stepStatus = stepElement.querySelector('.check-step-status');

            if (content) {
                content.textContent = text;
            }

            if (stepStatus) {
                stepStatus.innerHTML = `
                    <svg
                        class="check-success"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 448 512"
                        aria-hidden="true">

                        <path d="M438.6 105.4C451.1 117.9 451.1 138.1 438.6 150.6L182.6 406.6C170.1 419.1 149.9 419.1 137.4 406.6L9.372 278.6C-3.124 266.1-3.124 245.9 9.372 233.4C21.87 220.9 42.13 220.9 54.63 233.4L159.1 338.7L393.4 105.4C405.9 92.88 426.1 92.88 438.6 105.4H438.6z"/>
                    </svg>
                `;
            }
        }

        try {

            var lat = propertyLat;
            var lng = propertyLng;

            if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                throw new Error('Property location could not be verified.');
            }

            /*===== 1. FIND NSW PARCEL ========*/

            var pointParams = {
                geometry: `${lng},${lat}`,
                geometryType: 'esriGeometryPoint',
                inSR: '4326',
                spatialRel: 'esriSpatialRelIntersects',
                outFields: '*',
                returnGeometry: 'true',
                outSR: '7844',
                f: 'json'
            };

            var lotResponse = await fetch(
                `${parcelBase}/8/query?` +
                new URLSearchParams({
                    ...pointParams,
                    returnGeometry: 'true',
                    outSR: '7844',
                    inSR: '7844'
                })
            );

            if (!lotResponse.ok) {
                throw new Error('NSW parcel request failed.');
            }

            var lotData = await lotResponse.json();

            if (lotData.error) {
                throw new Error(`Lot API: ${lotData.error.message}`);
            }

            if (!lotData.features?.length) {
                throw new Error('NSW property lot could not be found.');
            }

            var lotGeometry = lotData.features[0].geometry;

            if (!lotGeometry) {
                throw new Error('NSW property lot geometry could not be found.');
            }

            /* ====== 2. PLANNING CHECKS ======= */

            var zoningStep = addStep('step-zoning', 'Zoning');
            var lotSizeStep = addStep('step-lot-size', 'Lot size');
            var heritageStep = addStep('step-heritage', 'Heritage');
            // var actualLotSizeStep = addStep('check-actual-lot-size', 'Actual Lot size'); // Commented out

            // completeStep(actualLotSizeStep, 'Actual Lot size');

            var zoningPromise = fetch(
                `${planningBase}/19/query?` +
                new URLSearchParams(pointParams)
            )
                .then(function (res) {
                    return res.json();
                })
                .then(function (data) {
                    if (data.error) {
                        throw new Error(`Zoning API: ${data.error.message}`);
                    }

                    completeStep(zoningStep, 'Zoning');

                    return data;
                });

            var lotSizePromise = fetch(
                `${planningBase}/22/query?` +
                new URLSearchParams(pointParams)
            )
                .then(function (res) {
                    return res.json();
                })
                .then(function (data) {
                    if (data.error) {
                        throw new Error(`Lot Size API: ${data.error.message}`);
                    }
                    completeStep(lotSizeStep, 'Lot size');
                    return data;
                });

            var epiHeritagePromise = fetch(
                `${planningBase}/16/query?` +
                new URLSearchParams(pointParams)
            )
                .then(function (r) {
                    return r.json();
                });

            var stateHeritagePromise = fetch(
                `${planningBase}/221/query?` +
                new URLSearchParams(pointParams)
            )
                .then(function (r) {
                    return r.json();
                });

            var results = await Promise.all([
                zoningPromise,
                lotSizePromise,
                epiHeritagePromise,
                stateHeritagePromise
            ]);

            var zoningData = results[0];
            var lotSizeData = results[1];
            var epiHeritageData = results[2];
            var stateHeritageData = results[3];

            completeStep(heritageStep, 'Heritage');

            /* ===== 3. HAZARD CHECK ======= */

            var hazardStep = addStep('step-hazard', 'Hazard overlays');

            /* Note: matching inSR to 7844 (GDA2020) to match the parcel geometry SR */
            var polygonParams = {
                geometry: JSON.stringify(lotGeometry),
                geometryType: 'esriGeometryPolygon',
                inSR: '7844',
                spatialRel: 'esriSpatialRelIntersects',
                outFields: '*',
                returnGeometry: 'false',
                f: 'json'
            };

            var bushfirePromise = fetch(
                `${hazardBase}/229/query?` +
                new URLSearchParams(polygonParams)
            )
                .then(function (r) {
                    return r.json();
                });

            var floodPromise = fetch(
                `${hazardBase}/230/query?` +
                new URLSearchParams(polygonParams)
            )
                .then(function (r) {
                    return r.json();
                });

            var hazardResults = await Promise.all([
                bushfirePromise,
                floodPromise
            ]);

            var bushfireData = hazardResults[0];
            var floodData = hazardResults[1];

            /* DEBUG LOGS FOR HAZARD RESPONSES */
            if (bushfireData.error) {
                console.error('🔥 [HAZARD API] Bushfire Error:', bushfireData.error);
                throw new Error(`Bushfire API: ${bushfireData.error.message}`);
            }

            if (floodData.error) {
                console.error('🌊 [HAZARD API] Flood Error:', floodData.error);
                throw new Error(`Flood API: ${floodData.error.message}`);
            }

            completeStep(hazardStep, 'Hazard overlays');

            /* ========= 4. EXTRACT RESULTS ========= */

            var zoning = zoningData.features?.[0]?.attributes || {};
            var lotSize = lotSizeData.features?.[0]?.attributes || {};

            var minimumLotValue = lotSize.LOT_SIZE ?? lotSize.lot_size ?? null;
            var minimumLotUnit = lotSize.UNIT ?? lotSize.unit ?? 'm²';

            var result = {
                address: address,
                latitude: lat,
                longitude: lng,

                // actual_lot_size: actualLotSize,

                minimum_lot_size: {
                    value: minimumLotValue,
                    unit: minimumLotUnit
                },

                zoning: {
                    code: zoning.LABEL?.trim() || null,
                    name: zoning.LAY_CLASS || null
                },

                lga: zoning.LGA_NAME || lotSize.LGA_NAME || null,

                hazard: {
                    bushfire: bushfireData.features?.length > 0 ? 'Flagged' : 'Not Flagged',
                    flood: floodData.features?.length > 0 ? 'Flagged' : 'Not Flagged'
                },

                heritage: {
                    state: stateHeritageData.features?.length > 0,
                    epi: epiHeritageData.features?.length > 0
                }
            };

            // console.log('[Final Results]:', result);

            /*===== 5. VISIBLE RESULTS =====*/
            var zoningResult = document.getElementById('res-zoning');
            var lotSizeResult = document.getElementById('res-lotsize');
            var minLotSizeResult = document.getElementById('res-min-lotsize');
            // var actualLotSizeResult = document.getElementById('res-actual-lotsize'); 
            var bushfireResult = document.getElementById('res-bushfire');

            if (zoningResult) {
                zoningResult.textContent = result.zoning.code || 'N/A';
            }

            if (lotSizeResult) {
                lotSizeResult.textContent = result.minimum_lot_size?.value
                    ? result.minimum_lot_size.value + ' ' + result.minimum_lot_size.unit
                    : 'N/A';
            }

            /*
            if (actualLotSizeResult) {
                actualLotSizeResult.textContent = result.actual_lot_size && result.actual_lot_size.value
                    ? result.actual_lot_size.value + ' ' + result.actual_lot_size.unit
                    : 'N/A';
            }
            */

            if (minLotSizeResult) {
                minLotSizeResult.textContent = result.minimum_lot_size?.value
                    ? result.minimum_lot_size.value + ' ' + result.minimum_lot_size.unit
                    : 'N/A';
            }

            if (bushfireResult) {
                bushfireResult.textContent = result.hazard.bushfire || 'Not Flagged';
            }

            /*====== 6. HIDDEN FORM FIELDS =====*/
            var fields = {
                'property-address': result.address || '',
                'zoning-code': result.zoning.code || '',
                'zoning-name': result.zoning.name || '',
                'min-lot-size': result.minimum_lot_size && result.minimum_lot_size.value
                    ? result.minimum_lot_size.value + ' ' + result.minimum_lot_size.unit
                    : '',
                // 'actual-lot-size': result.actual_lot_size && result.actual_lot_size.value ? ... : '', // Commented out
                'property-lga': result.lga || '',
                'property-latitude': result.latitude || '',
                'property-longitude': result.longitude || '',
                'property-bushfire': result.hazard.bushfire || '',
                'property-flood': result.hazard.flood || '',
                'property-state-heritage': result.heritage.state ? 'Yes' : 'No',
                'property-epi-heritage': result.heritage.epi ? 'Yes' : 'No'
            };

            Object.keys(fields).forEach(function (id) {
                var field = document.getElementById(id);

                if (field) {
                    field.value = fields[id];
                }
            });

            /*====== 7. JSON RESULT ========*/
            if (jsonOutput) {
                jsonOutput.textContent = JSON.stringify(result, null, 2);
            }

            /*======= SHOW RESULTS ========*/

            setTimeout(function () {
                showStep(3);
            }, 500);

        } catch (error) {

            console.error('❌ Property check failed:', error);

            if (output) {
                output.textContent = error.message || 'Something went wrong.';
                output.classList.add('show');
            }

            if (retryRow) {
                retryRow.style.display = 'flex';
            }
        }
    }

    /* ====== STEP 3: SEND REPORT ======= */
    var form = document.getElementById('property-check-form');

    if (!form) {
        return;
    }

    var sendButton = document.getElementById('send');

    var nameInput = document.getElementById('nm');
    var phoneInput = document.getElementById('ph');
    var emailInput = document.getElementById('em');

    function updateSendButton() {
        if (!sendButton) {
            return;
        }

        if (
            nameInput.value.trim() !== '' &&
            phoneInput.value.trim() !== '' &&
            emailInput.value.trim() !== ''
        ) {
            sendButton.style.opacity = '1';
            sendButton.disabled = false;
        } else {
            sendButton.style.opacity = '0.4';
            sendButton.disabled = true;
        }
    }

    nameInput.addEventListener('input', updateSendButton);
    phoneInput.addEventListener('input', updateSendButton);
    emailInput.addEventListener('input', updateSendButton);

    updateSendButton();

    var errorElement = document.getElementById('e3');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        var name = document.getElementById('nm')?.value.trim() || '';
        var phone = document.getElementById('ph')?.value.trim() || '';
        var email = document.getElementById('em')?.value.trim() || '';

        if (!name || !phone || !email) {
            if (errorElement) {
                errorElement.textContent = 'Please fill in your name, phone and email.';
                errorElement.classList.add('show');
            }
            return;
        }

        if (errorElement) {
            errorElement.classList.remove('show');
        }

        /* WordPress AJAX URL */
        if (!window.propertyCheckAjaxUrl) {
            console.error('propertyCheckAjaxUrl is missing.');
            if (errorElement) {
                errorElement.textContent = 'Unable to connect to the server. Please try again.';
                errorElement.classList.add('show');
            }
            return;
        }

        var originalText = sendButton.textContent;

        sendButton.disabled = true;
        sendButton.textContent = 'Sending report...';

        var formData = new FormData(form);
        formData.set('action', 'property_check_send');

        formData.set('property_address', document.getElementById('suburbInput')?.value.trim() || '');
        formData.set('zoning_code', document.getElementById('zoning-code')?.value || '');
        formData.set('zoning_name', document.getElementById('zoning-name')?.value || '');
        formData.set('min_lot_size', document.getElementById('min-lot-size')?.value || '');
        // formData.set('actual_lot_size', document.getElementById('actual-lot-size')?.value || ''); // Commented out
        formData.set('lga', document.getElementById('property-lga')?.value || '');
        formData.set('latitude', document.getElementById('property-latitude')?.value || '');
        formData.set('longitude', document.getElementById('property-longitude')?.value || '');
        formData.set('bushfire', document.getElementById('property-bushfire')?.value || '');
        formData.set('flood', document.getElementById('property-flood')?.value || '');
        formData.set('state_heritage', document.getElementById('property-state-heritage')?.value || '');
        formData.set('epi_heritage', document.getElementById('property-epi-heritage')?.value || '');

        formData.set('name', name);
        formData.set('phone', phone);
        formData.set('email', email);

        formData.set('owner', owner);
        formData.set('timing', timing);

        try {

            var response = await fetch(
                window.propertyCheckAjaxUrl,
                {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                }
            );

            var responseText = await response.text();
            var result;

            try {
                result = JSON.parse(responseText);
            } catch (jsonError) {

                console.error('Invalid JSON response:', responseText);

                throw new Error('The server returned an invalid response.');
            }

            /* SUCCESS */
            if (result.success) {
                var thankYouUrl = '/thank-you-page-form/';
                if (window.location.search) {
                    thankYouUrl += window.location.search;
                }
                window.location.href = thankYouUrl;
                return;
            }

            /* WORDPRESS ERROR */
            var message = result?.data?.message || result?.data || 'Could not send the report. Please try again.';
            console.error('WordPress AJAX error:', result);

            if (errorElement) {
                errorElement.textContent = message;
                errorElement.classList.add('show');
            }

        } catch (error) {
            console.error('AJAX request failed:', error);
            document.getElementById('request-status-title').innerHTML = 'Request <em>Failed</em>';
            document.getElementById('request-status-subtitle').textContent = error.message || 'Unable to send the report.';

            if (errorElement) {
                errorElement.textContent = error.message || 'A network error occurred. Please try again.';
                errorElement.classList.add('show');
            }

        } finally {
            sendButton.disabled = false;
            updateSendButton();
        }
    });

});


if (!window.propertyCheckFormScriptLoaded) {
    window.propertyCheckFormScriptLoaded = true;

    function initPropertyCheckForm() {
        var form = document.getElementById('property-check-form');
        if (!form || form.dataset.initialized === 'true') return;
        form.dataset.initialized = 'true';

        var step = 0, owner = '', timing = '';
        var propertyLat = null, propertyLng = null;
        var selectedGooglePlace = null;
        var suburbInput = document.getElementById('suburbInput');
        var goButton = document.getElementById('go1');
        var runButton = document.getElementById('run');
        var sendButton = document.getElementById('send');
        var nameInput = document.getElementById('nm');
        var phoneInput = document.getElementById('ph');
        var emailInput = document.getElementById('em');
        var errorElement = document.getElementById('e3');

        var OWNER_OPTIONS = ['I own it', 'Buying it', 'Family owns it', 'I rent'];
        var TIMING_OPTIONS = ['ASAP', '1-3 months', '3-6 months', 'Researching'];

        function showStep(n) {
            step = n;

            for (var i = 0; i < 5; i++) {
                var el = document.getElementById('s' + i);
                if (el) el.classList.toggle('active', i === n);
            }

            document.querySelectorAll('.gfx-label').forEach(function (el, i) {
                el.classList.toggle('on', i <= Math.min(n, 3));
            });

            document.querySelectorAll('.gfx-bar').forEach(function (el, i) {
                el.classList.toggle('on', i < n);
            });
        }

        function buildChips(id, options, onPick) {
            var box = document.getElementById(id);
            if (!box) return;

            box.innerHTML = '';

            options.forEach(function (value) {
                var button = document.createElement('button');
                button.type = 'button';
                button.className = 'gfx-chip';
                button.textContent = value;
                button.setAttribute('aria-pressed', 'false');

                button.addEventListener('click', function () {
                    box.querySelectorAll('.gfx-chip').forEach(function (chip) {
                        chip.setAttribute('aria-pressed', 'false');
                    });

                    button.setAttribute('aria-pressed', 'true');
                    onPick(value);

                    var error = document.getElementById('e1');
                    if (error) error.classList.remove('show');
                });

                box.appendChild(button);
            });
        }

        function updateRunButton() {
            if (!runButton) return;
            runButton.disabled = !(owner && timing);
            runButton.style.opacity = owner && timing ? '1' : '0.4';
        }

        function updateGoButton() {
            if (!goButton || !suburbInput) return;
            goButton.disabled = !suburbInput.value.trim();
            goButton.style.opacity = suburbInput.value.trim() ? '1' : '0.4';
        }

        function updateSendButton() {
            if (!sendButton || !nameInput || !phoneInput || !emailInput) return;

            if (form.dataset.submitting === 'true') {
                sendButton.disabled = true;
                return;
            }

            var valid = nameInput.value.trim() &&
                phoneInput.value.trim() &&
                emailInput.value.trim();

            sendButton.disabled = !valid;
            sendButton.style.opacity = valid ? '1' : '0.4';
        }

        buildChips('own', OWNER_OPTIONS, function (value) {
            owner = value;
            updateRunButton();
        });

        buildChips('tim', TIMING_OPTIONS, function (value) {
            timing = value;
            updateRunButton();
        });

        updateRunButton();
        updateGoButton();
        updateSendButton();

        if (suburbInput) {
            suburbInput.addEventListener('input', function () {
                selectedGooglePlace = null;
                propertyLat = null;
                propertyLng = null;

                var error = document.getElementById('e0');
                if (error) error.classList.remove('show');

                updateGoButton();
            });
        }

        function initGoogleAutocomplete() {
            if (!suburbInput || suburbInput.dataset.autocompleteInitialized === 'true') return;
            if (typeof google === 'undefined' || !google.maps || !google.maps.places) return;

            suburbInput.dataset.autocompleteInitialized = 'true';

            var autocomplete = new google.maps.places.Autocomplete(suburbInput, {
                bounds: {
                    north: -33.40,
                    south: -34.20,
                    east: 151.60,
                    west: 150.50
                },
                componentRestrictions: { country: 'au' },
                strictBounds: false,
                fields: ['formatted_address', 'geometry', 'address_components', 'place_id', 'types']
            });

            autocomplete.addListener('place_changed', function () {
                var place = autocomplete.getPlace();
                var isAustralia = (place.address_components || []).some(function (component) {
                    return component.types.includes('country') && component.short_name === 'AU';
                });

                if (!place.geometry || !place.geometry.location || !isAustralia) {
                    selectedGooglePlace = null;
                    propertyLat = null;
                    propertyLng = null;

                    var error = document.getElementById('e0');
                    if (error) {
                        error.textContent = 'Please select a valid Australian address from the suggestions.';
                        error.classList.add('show');
                    }
                    return;
                }

                selectedGooglePlace = place;
                propertyLat = place.geometry.location.lat();
                propertyLng = place.geometry.location.lng();

                if (place.formatted_address) suburbInput.value = place.formatted_address;
                updateGoButton();

                var error = document.getElementById('e0');
                if (error) error.classList.remove('show');
            });
        }

        if (suburbInput) {
            initGoogleAutocomplete();

            if (!(typeof google !== 'undefined' && google.maps && google.maps.places)) {
                var attempts = 0;
                var googleCheck = setInterval(function () {
                    attempts++;

                    if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                        clearInterval(googleCheck);
                        initGoogleAutocomplete();
                    } else if (attempts >= 100) {
                        clearInterval(googleCheck);
                        console.error('Google Maps Places API did not load.');
                    }
                }, 100);
            }
        }

        if (goButton) {
            goButton.addEventListener('click', function () {
                var error = document.getElementById('e0');

                if (error) error.classList.remove('show');

                if (!suburbInput || !suburbInput.value.trim()) {
                    if (error) {
                        error.textContent = 'Please enter a property address.';
                        error.classList.add('show');
                    }
                    return;
                }

                if (!selectedGooglePlace || !Number.isFinite(propertyLat) || !Number.isFinite(propertyLng)) {
                    if (error) {
                        error.textContent = 'Please select your property from the address suggestions.';
                        error.classList.add('show');
                    }
                    return;
                }

                goButton.disabled = true;
                goButton.textContent = 'Checking address...';

                showStep(1);

                goButton.disabled = false;
                goButton.textContent = 'Continue';
                updateGoButton();
            });
        }

        var back0 = document.getElementById('back0');
        if (back0) back0.addEventListener('click', function () { showStep(0); });

        var back2 = document.getElementById('back2');
        if (back2) back2.addEventListener('click', function () { showStep(1); });

        var backToStart = document.getElementById('backToStart');
        if (backToStart) backToStart.addEventListener('click', function () { showStep(0); });

        var retryRow = document.getElementById('retryRow');
        if (retryRow) {
            retryRow.addEventListener('click', function (event) {
                if (event.target.closest('button')) handlePropertyCheck();
            });
        }

        if (runButton) {
            runButton.addEventListener('click', function () {
                if (!owner || !timing) {
                    var error = document.getElementById('e1');
                    if (error) error.classList.add('show');
                    return;
                }

                showStep(2);
                handlePropertyCheck();
            });
        }

        async function handlePropertyCheck() {
            var address = suburbInput ? suburbInput.value.trim() : '';
            var output = document.getElementById('suburbOutput');
            var jsonOutput = document.getElementById('jsonOutput');
            var status = document.getElementById('propertyCheckStatus');

            if (retryRow) retryRow.style.display = 'none';
            if (output) {
                output.textContent = '';
                output.classList.remove('show');
            }
            if (jsonOutput) jsonOutput.style.display = 'none';
            if (status) status.innerHTML = '';

            var planningBase = 'https://mapprod3.environment.nsw.gov.au/arcgis/rest/services/ePlanning/Planning_Portal_Principal_Planning/MapServer';
            var hazardBase = 'https://mapprod3.environment.nsw.gov.au/arcgis/rest/services/ePlanning/Planning_Portal_Hazard/MapServer';
            var parcelBase = 'https://portal.spatial.nsw.gov.au/server/rest/services/NSW_Land_Parcel_Property_Theme_multiCRS/FeatureServer';

            function addStep(id, label) {
                var el = document.createElement('div');
                el.id = id;
                el.className = 'check-step';
                el.innerHTML = '<div class="check-step-content"><span></span></div><div class="check-step-status"><span class="check-loader"></span></div>';

                var text = el.querySelector('.check-step-content span');
                if (text) text.textContent = label;
                if (status) status.appendChild(el);
                return el;
            }

            function completeStep(el, label) {
                if (!el) return;

                var text = el.querySelector('.check-step-content span');
                var icon = el.querySelector('.check-step-status');

                if (text) text.textContent = label;
                if (icon) {
                    icon.innerHTML = '<svg class="check-success" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" aria-hidden="true"><path d="M438.6 105.4C451.1 117.9 451.1 138.1 438.6 150.6L182.6 406.6C170.1 419.1 149.9 419.1 137.4 406.6L9.372 278.6C-3.124 266.1-3.124 245.9 9.372 233.4C21.87 220.9 42.13 220.9 54.63 233.4L159.1 338.7L393.4 105.4C405.9 92.88 426.1 92.88 438.6 105.4z"/></svg>';
                }
            }

            try {
                if (!Number.isFinite(propertyLat) || !Number.isFinite(propertyLng)) {
                    throw new Error('Property location could not be verified. Please select an address suggestion.');
                }

                var pointParams = {
                    geometry: propertyLng + ',' + propertyLat,
                    geometryType: 'esriGeometryPoint',
                    inSR: '4326',
                    spatialRel: 'esriSpatialRelIntersects',
                    outFields: '*',
                    returnGeometry: 'true',
                    outSR: '7844',
                    f: 'json'
                };

                var parcelParams = new URLSearchParams({
                    ...pointParams,
                    inSR: '4326',
                    outSR: '7844'
                });

                var lotResponse = await fetch(parcelBase + '/8/query?' + parcelParams);
                if (!lotResponse.ok) throw new Error('NSW parcel request failed.');

                var lotData = await lotResponse.json();
                if (lotData.error) throw new Error('Lot API: ' + lotData.error.message);
                if (!lotData.features?.length) throw new Error('NSW property lot could not be found.');

                var lotGeometry = lotData.features[0].geometry;
                if (!lotGeometry) throw new Error('NSW property lot geometry could not be found.');

                var zoningStep = addStep('step-zoning', 'Zoning');
                var lotSizeStep = addStep('step-lot-size', 'Lot size');
                var heritageStep = addStep('step-heritage', 'Heritage');

                async function query(url) {
                    var response = await fetch(url);
                    if (!response.ok) throw new Error('A property data request failed.');

                    var data = await response.json();
                    if (data.error) throw new Error(data.error.message);
                    return data;
                }

                var pointQuery = new URLSearchParams(pointParams);

                var results = await Promise.all([
                    query(planningBase + '/19/query?' + pointQuery),
                    query(planningBase + '/22/query?' + pointQuery),
                    query(planningBase + '/16/query?' + pointQuery),
                    query(planningBase + '/221/query?' + pointQuery)
                ]);

                var zoningData = results[0];
                var lotSizeData = results[1];
                var epiHeritageData = results[2];
                var stateHeritageData = results[3];

                completeStep(zoningStep, 'Zoning');
                completeStep(lotSizeStep, 'Lot size');
                completeStep(heritageStep, 'Heritage');

                var hazardStep = addStep('step-hazard', 'Hazard overlays');
                var polygonParams = new URLSearchParams({
                    geometry: JSON.stringify(lotGeometry),
                    geometryType: 'esriGeometryPolygon',
                    inSR: '7844',
                    spatialRel: 'esriSpatialRelIntersects',
                    outFields: '*',
                    returnGeometry: 'false',
                    f: 'json'
                });

                var hazardResults = await Promise.all([
                    query(hazardBase + '/229/query?' + polygonParams),
                    query(hazardBase + '/230/query?' + polygonParams)
                ]);

                var bushfireData = hazardResults[0];
                var floodData = hazardResults[1];
                completeStep(hazardStep, 'Hazard overlays');

                var zoning = zoningData.features?.[0]?.attributes || {};
                var lotSize = lotSizeData.features?.[0]?.attributes || {};
                var minimumLotValue = lotSize.LOT_SIZE ?? lotSize.lot_size ?? null;
                var minimumLotUnit = lotSize.UNIT ?? lotSize.unit ?? 'm²';

                var result = {
                    address: address,
                    latitude: propertyLat,
                    longitude: propertyLng,
                    minimum_lot_size: { value: minimumLotValue, unit: minimumLotUnit },
                    zoning: {
                        code: zoning.LABEL?.trim() || null,
                        name: zoning.LAY_CLASS || null
                    },
                    lga: zoning.LGA_NAME || lotSize.LGA_NAME || null,
                    hazard: {
                        bushfire: bushfireData.features?.length ? 'Flagged' : 'Not Flagged',
                        flood: floodData.features?.length ? 'Flagged' : 'Not Flagged'
                    },
                    heritage: {
                        state: !!stateHeritageData.features?.length,
                        epi: !!epiHeritageData.features?.length
                    }
                };

                var displayFields = {
                    'res-zoning': result.zoning.code || 'N/A',
                    'res-lotsize': minimumLotValue != null ? minimumLotValue + ' ' + minimumLotUnit : 'N/A',
                    'res-min-lotsize': minimumLotValue != null ? minimumLotValue + ' ' + minimumLotUnit : 'N/A',
                    'res-bushfire': result.hazard.bushfire
                };

                Object.keys(displayFields).forEach(function (id) {
                    var el = document.getElementById(id);
                    if (el) el.textContent = displayFields[id];
                });

                var fields = {
                    'property-address': result.address,
                    'zoning-code': result.zoning.code || '',
                    'zoning-name': result.zoning.name || '',
                    'min-lot-size': minimumLotValue != null ? minimumLotValue + ' ' + minimumLotUnit : '',
                    'property-lga': result.lga || '',
                    'property-latitude': result.latitude,
                    'property-longitude': result.longitude,
                    'property-bushfire': result.hazard.bushfire,
                    'property-flood': result.hazard.flood,
                    'property-state-heritage': result.heritage.state ? 'Yes' : 'No',
                    'property-epi-heritage': result.heritage.epi ? 'Yes' : 'No'
                };

                Object.keys(fields).forEach(function (id) {
                    var el = document.getElementById(id);
                    if (el) el.value = fields[id];
                });

                if (jsonOutput) {
                    jsonOutput.textContent = JSON.stringify(result, null, 2);
                    jsonOutput.style.display = 'block';
                }

                setTimeout(function () { showStep(3); }, 500);

            } catch (error) {
                console.error('Property check failed:', error);

                if (output) {
                    output.textContent = error.message || 'Something went wrong.';
                    output.classList.add('show');
                }

                if (retryRow) retryRow.style.display = 'flex';
            }
        }

        [nameInput, phoneInput, emailInput].forEach(function (input) {
            if (input) input.addEventListener('input', updateSendButton);
        });

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            if (form.dataset.submitting === 'true') return;

            var name = nameInput?.value.trim() || '';
            var phone = phoneInput?.value.trim() || '';
            var email = emailInput?.value.trim() || '';

            if (!name || !phone || !email) {
                if (errorElement) {
                    errorElement.textContent = 'Please fill in your name, phone and email.';
                    errorElement.classList.add('show');
                }
                return;
            }

            var ajaxUrl = window.propertyCheckAjaxUrl;

            if (!ajaxUrl) {
                if (errorElement) {
                    errorElement.textContent = 'Unable to connect to the server. Please try again.';
                    errorElement.classList.add('show');
                }
                console.error('window.propertyCheckAjaxUrl is missing.');
                return;
            }

            form.dataset.submitting = 'true';
            if (errorElement) errorElement.classList.remove('show');

            var originalText = sendButton ? sendButton.textContent : 'Send report';
            if (sendButton) {
                sendButton.disabled = true;
                sendButton.textContent = 'Sending report...';
            }

            var formData = new FormData(form);
            formData.set('action', 'property_check_send');

            var fields = {
                property_address: suburbInput?.value.trim() || '',
                zoning_code: document.getElementById('zoning-code')?.value || '',
                zoning_name: document.getElementById('zoning-name')?.value || '',
                min_lot_size: document.getElementById('min-lot-size')?.value || '',
                lga: document.getElementById('property-lga')?.value || '',
                latitude: document.getElementById('property-latitude')?.value || '',
                longitude: document.getElementById('property-longitude')?.value || '',
                bushfire: document.getElementById('property-bushfire')?.value || '',
                flood: document.getElementById('property-flood')?.value || '',
                state_heritage: document.getElementById('property-state-heritage')?.value || '',
                epi_heritage: document.getElementById('property-epi-heritage')?.value || '',
                name: name,
                phone: phone,
                email: email,
                owner: owner,
                timing: timing
            };

            Object.entries(fields).forEach(function ([key, value]) {
                formData.set(key, value == null ? '' : value);
            });

            try {
                var response = await fetch(ajaxUrl, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                var responseText = await response.text();
                var result;

                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Invalid AJAX response:', responseText);
                    throw new Error('The server returned an invalid response.');
                }

                if (!response.ok || !result.success) {
                    throw new Error(
                        result?.data?.message ||
                        (typeof result?.data === 'string' ? result.data : '') ||
                        'Could not send the report. Please try again.'
                    );
                }

                var currentPath = window.location.pathname.replace(/\/+$/, '');

                if (currentPath.endsWith('/thank-you-page-form')) return;

                var thankYouUrl = new URL('thank-you-page-form/', window.location.href);
                thankYouUrl.search = window.location.search;
                window.location.href = thankYouUrl.href;

            } catch (error) {
                console.error('Property Check submission failed:', error);

                var statusTitle = document.getElementById('request-status-title');
                var statusSubtitle = document.getElementById('request-status-subtitle');

                if (statusTitle) statusTitle.innerHTML = 'Request <em>Failed</em>';
                if (statusSubtitle) statusSubtitle.textContent = error.message;

                if (errorElement) {
                    errorElement.textContent = error.message || 'Unable to send the report. Please try again.';
                    errorElement.classList.add('show');
                }

                form.dataset.submitting = 'false';
                if (sendButton) sendButton.textContent = originalText;
                updateSendButton();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPropertyCheckForm, { once: true });
    } else {
        initPropertyCheckForm();
    }
}

(function () {
    const phoneInput = document.querySelector('[data-phone-input]');
    const form = phoneInput?.closest('form');

    if (!phoneInput || !form || typeof window.intlTelInput !== 'function') {
        return;
    }

    const countryInput = document.getElementById('phone_country');
    const feedback = document.getElementById('phoneFeedback');

    // Read config directly from the HTML attribute — no timing issues, always available
    let countryConfig = {};
    try {
        const raw = phoneInput.getAttribute('data-phone-config');
        if (raw) countryConfig = JSON.parse(raw);
    } catch (e) {
        countryConfig = {};
    }

    const iti = window.intlTelInput(phoneInput, {
        initialCountry: countryInput?.value || 'co',
        preferredCountries: ['co', 'us', 'mx', 'es', 'ar', 'br'],
        separateDialCode: true,
        nationalMode: false,
        autoPlaceholder: 'off',
        utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input/build/js/utils.js'
    });

    if (phoneInput.value) {
        iti.setNumber(phoneInput.value);
        setTimeout(() => {
            const national = iti.getNumber(window.intlTelInputUtils?.numberFormat?.NATIONAL || 2);
            if (national) {
                phoneInput.value = national.replace(/\D/g, '');
            }
        }, 100);
    }

    const getSelectedRule = () => {
        const data = iti.getSelectedCountryData();
        const iso2 = (data && data.iso2) ? data.iso2.toLowerCase() : '';
        return countryConfig[iso2] || null;
    };

    const getDigits = () => phoneInput.value.replace(/\D/g, '');

    const getNationalDigits = () => {
        const digits = getDigits();
        const selectedCountry = iti.getSelectedCountryData();
        const rule = getSelectedRule();
        const maxLength = rule?.lengths?.length ? Math.max(...rule.lengths) : 15;
        const dialCode = selectedCountry?.dialCode || '';

        if (dialCode && digits.startsWith(dialCode) && digits.length > maxLength) {
            return digits.slice(dialCode.length);
        }

        return digits;
    };

    const setErrorMessage = (message = '', preserveServerMessage = false) => {
        if (!feedback) {
            phoneInput.classList.toggle('is-invalid', Boolean(message));
            phoneInput.setCustomValidity(message);
            return;
        }

        const serverMessage = preserveServerMessage ? (feedback.dataset.serverMessage || '') : '';
        const finalMessage = message || serverMessage;

        phoneInput.classList.toggle('is-invalid', Boolean(finalMessage));
        phoneInput.setCustomValidity(message);
        feedback.textContent = finalMessage;
        feedback.classList.toggle('d-block', Boolean(finalMessage));
    };

    const syncCountryDetails = () => {
        const selectedCountry = iti.getSelectedCountryData();
        const rule = getSelectedRule();
        const maxLength = rule?.lengths?.length ? Math.max(...rule.lengths) : 15;

        if (countryInput) {
            countryInput.value = selectedCountry?.iso2 || '';
        }

        phoneInput.maxLength = maxLength;

        // Set placeholder from config example or fallback to iti built-in placeholder
        let newPlaceholder = '';
        if (rule && rule.example) {
            newPlaceholder = rule.example;
        } else {
            // Fallback to library's placeholder if available (requires utilsScript to be loaded)
            const itiPlaceholder = (typeof iti.getPlaceholder === 'function') ? iti.getPlaceholder() : '';
            if (itiPlaceholder) {
                // Strip non-digits and leading zeros for national placeholder
                newPlaceholder = itiPlaceholder.replace(/\D/g, '').replace(/^0+/, '');
            } else {
                // If it is not ready yet, it will be updated by the utilsChecker loop
                newPlaceholder = '3000000000';
            }
        }
        
        phoneInput.placeholder = 'Ej: ' + newPlaceholder;
    };

    const enforceMaxLength = () => {
        const rule = getSelectedRule();
        const maxLength = rule?.lengths?.length ? Math.max(...rule.lengths) : 15;
        const nationalDigits = getNationalDigits();

        if (nationalDigits.length <= maxLength) {
            return;
        }

        phoneInput.value = nationalDigits.slice(0, maxLength);
    };

    const validatePhone = (normalizeOnSuccess = false) => {
        const nationalDigits = getNationalDigits();
        const selectedCountry = iti.getSelectedCountryData();
        const rule = getSelectedRule();

        if (!nationalDigits) {
            setErrorMessage('Ingresa tu número de teléfono.');
            return false;
        }

        if (rule?.lengths?.length && !rule.lengths.includes(nationalDigits.length)) {
            setErrorMessage('Para ' + (rule.name || selectedCountry?.name || 'este país') + ' debes ingresar ' + rule.lengths.join(' o ') + ' dígitos.');
            return false;
        }

        const startsWith = rule?.starts_with || [];
        if (startsWith.length > 0) {
            const validPrefix = startsWith.some(p => nationalDigits.startsWith(p));
            if (!validPrefix) {
                const prefixes = startsWith.join(' o ');
                setErrorMessage('Para ' + (rule.name || selectedCountry?.name || 'este país') + ' el número debe comenzar por ' + prefixes + '.');
                return false;
            }
        }

        const fullNumber = iti.getNumber();

        if (window.intlTelInputUtils && nationalDigits.length >= 8 && fullNumber && !iti.isValidNumber()) {
            setErrorMessage('Revisa el número ingresado. El formato no es válido para el país seleccionado.');
            return false;
        }

        setErrorMessage('');

        if (normalizeOnSuccess) {
            phoneInput.value = fullNumber || ('+' + (selectedCountry?.dialCode || '') + nationalDigits);
        }

        return true;
    };

    const sanitizeInput = () => {
        const value = phoneInput.value;
        const digits = value.replace(/\D/g, '');
        if (value !== digits) {
            phoneInput.value = digits;
        }
    };

    if (feedback && feedback.textContent.trim()) {
        feedback.dataset.serverMessage = feedback.textContent.trim();
        feedback.classList.add('d-block');
    }

    syncCountryDetails();
    setErrorMessage('', true);

    // Re-sync placeholders once the library's utility script is loaded (to get real examples for all countries)
    let utilsCheckCount = 0;
    const utilsChecker = setInterval(() => {
        if (window.intlTelInputUtils) {
            syncCountryDetails();
            clearInterval(utilsChecker);
        }
        if (++utilsCheckCount > 25) clearInterval(utilsChecker); // Stop after 5 seconds
    }, 200);

    phoneInput.addEventListener('input', () => {
        if (feedback) {
            feedback.dataset.serverMessage = '';
        }
        sanitizeInput();
        enforceMaxLength();
        if (phoneInput.classList.contains('is-invalid')) {
            validatePhone();
        }
    });

    phoneInput.addEventListener('paste', () => {
        setTimeout(() => {
            const val = phoneInput.value;
            if (val.startsWith('+')) {
                iti.setNumber(val);
                setTimeout(() => {
                    phoneInput.value = phoneInput.value.replace(/\D/g, '');
                }, 1);
            } else {
                sanitizeInput();
            }
        }, 0);
    });

    phoneInput.addEventListener('blur', () => {
        validatePhone();
    });

    phoneInput.addEventListener('countrychange', () => {
        if (feedback) {
            feedback.dataset.serverMessage = '';
        }
        syncCountryDetails();
        enforceMaxLength();
        if (getDigits()) {
            validatePhone();
        } else {
            setErrorMessage('');
        }
    });

    form.addEventListener('submit', (event) => {
        if (feedback) {
            feedback.dataset.serverMessage = '';
        }
        syncCountryDetails();
        enforceMaxLength();
        if (!validatePhone(true)) {
            event.preventDefault();
            phoneInput.focus();
        }
    });
})();

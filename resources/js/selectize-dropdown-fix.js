// Responsive selectize dropdown positioning
(function() {
    'use strict';

    // Function to reposition dropdown based on viewport
    function repositionDropdown(dropdown) {
        if (!dropdown) return;

        // We use requestAnimationFrame to ensure we measure after render
        requestAnimationFrame(function() {
            try {
                // Get the selectize input element
                // The dropdown is usually a sibling of the wrapper, or child of body if dropdownParent: 'body'
                // If appended to body, we need a way to find the input. Selectize stores the instance.

                // Try to find the control.
                // Using internal selectize data if available would be best, but let's rely on DOM if possible.
                // If dropdownParent is body, the dropdown doesn't have a direct parent relationship to the input validation-wise
                // except via the internal instance.

                // Assuming the dropdown has a reference or we can find it.
                // Actually, selectize sets 'top' and 'left' on the dropdown.
                // We can just rely on the rects if we assume it's currently positioned near the input.

                // BUT, to get the input rect accurately:
                // Note: The selectize dropdown usually has `data-selectize` or similar, or we can look for the active input.
                const input = document.querySelector('.selectize-input.dropdown-active');
                if (!input) return;

                const inputRect = input.getBoundingClientRect();
                const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
                const dropdownHeight = dropdown.scrollHeight;

                // Check if this dropdown belongs to the active input
                // Compare positions? Or assume only one Main dropdown is active at a time (Selectize default).
                // If there are multiple, this might be risky, but usually only one is open.

                // Calculate available space
                const spaceBelow = viewportHeight - inputRect.bottom;
                const spaceAbove = inputRect.top;

                // Define threshold and desired height
                // If dropdown fits below, keep it. If not, check above.
                // We prefer Down if it fits.
                const offset = 5; // buffer

                // If space below is less than dropdown height AND space above is larger than space below
                if (spaceBelow < dropdownHeight && spaceAbove > spaceBelow) {
                    // Position UP
                    dropdown.classList.add('dropdown-upward');
                    dropdown.style.top = 'auto';
                    dropdown.style.bottom = (viewportHeight - inputRect.top + offset) + 'px';
                    dropdown.style.left = inputRect.left + 'px';
                    dropdown.style.width = inputRect.width + 'px';
                    dropdown.style.position = 'fixed';

                    // Constrain height if needed to fit above
                    const maxH = Math.min(dropdownHeight, spaceAbove - offset * 2);
                    dropdown.style.maxHeight = maxH + 'px';

                } else {
                    // Position DOWN (Default behavior, but we might want to constrain max-height to avoid scroll)
                    dropdown.classList.remove('dropdown-upward');

                    // If we want to prevent adding scroll to body, we should use fixed position or constrain height.
                    // Selectize default is absolute. causing scroll.
                    // Let's force fixed for 'down' as well to avoid window scroll, only if needed?
                    // Actually, modifying 'maxHeight' is safer.

                    const maxH = Math.min(dropdownHeight, spaceBelow - offset);
                    dropdown.style.maxHeight = maxH + 'px';

                    // Let Selectize handle top/left for down usually,
                    // but if we are in 'body', we might want to enforce fixed to handle scroll properly?
                    // Let's stick to modifying max-height for Down, and full override for Up.

                    // If we use fixed, we must update on scroll.
                    dropdown.style.position = 'fixed';
                    dropdown.style.top = (inputRect.bottom + offset) + 'px';
                    dropdown.style.left = inputRect.left + 'px';
                    dropdown.style.width = inputRect.width + 'px';
                    dropdown.style.bottom = 'auto';
                }

                dropdown.style.overflowY = 'auto';

            } catch (e) {
                console.error('Error repositioning dropdown:', e);
            }
        });
    }

    // Main trigger function
    function checkAndReposition() {
        // Find visible selectize dropdowns
        const dropdowns = document.querySelectorAll('.selectize-dropdown');
        dropdowns.forEach(dropdown => {
            if (dropdown.style.display !== 'none') {
                 repositionDropdown(dropdown);
            }
        });
    }

    // Listeners

    // 1. On Click/Focus of inputs
    document.addEventListener('click', function(e) {
        if (e.target.closest('.selectize-input')) {
            // Wait for dropdown to technically 'open'
            setTimeout(checkAndReposition, 50);
            setTimeout(checkAndReposition, 200); // safety net
        }
    }, true);

    // 2. Window events
    window.addEventListener('resize', checkAndReposition, { passive: true });
    window.addEventListener('scroll', checkAndReposition, { passive: true, capture: true });

    // 3. Mutation Observer for new nodes (initial render)
    const observer = new MutationObserver(function(mutations) {
        let shouldCheck = false;
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                shouldCheck = true;
            }
            if (mutation.type === 'attributes' && (mutation.attributeName === 'style' || mutation.attributeName === 'class')) {
                 shouldCheck = true;
            }
        });
        if (shouldCheck) {
             checkAndReposition();
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['style', 'class', 'display'] // monitor style changes on dropdowns
    });

})();

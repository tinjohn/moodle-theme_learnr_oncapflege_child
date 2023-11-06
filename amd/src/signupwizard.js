import {eventTypes as formEventTypes} from 'core_form/events';

export const init = () => {

    /**
     * Function to add list entries based on event messages
     * @param {any} message
     * @returns {any}
     */
    function addListEntry(message) {
        const listItem = document.createElement('li');
        listItem.textContent = message;
        listItem.classList.add("nodecoration");
        errorSummaryUl.appendChild(listItem);
    }
    /**
     * Function to remove all list entries based on event messages
     * @returns {any}
     */
    function removeAllListEntries() {
        const ul = document.querySelector('#formerrorssummary ul'); // Get a reference to the <ul> element
        // Remove all <li> elements from the <ul>
        while (ul.firstChild) {
            ul.removeChild(ul.firstChild);
        }
    }

    /**
     * Description
     * @param {any} e
     * @returns {any}
     */
    function fieldValidationFailedListener(e) {
        const message = e.detail.message;
        if (message != "") {
            addListEntry(message);
            const errorSummary = document.querySelector('#formerrorssummary');
            errorSummary.classList.remove('hidden');
        }
    }

    /**
     * Description
     * @returns {any}
     */
    function refreshWizard() {
        tabTargets[currentStep].classList.remove('active');
        currentStep = 0;
        for(var i=steps[currentStep]+1; i< tabPanels.length; i++) {
            tabPanels[i].classList.add('hidden');
        }
        for(var i=0; i <= steps[currentStep]; i++) {
            tabPanels[i].classList.remove('hidden');
        }
        tabTargets[currentStep].classList.add('active');
        updateStatusDisplay();
        refreshErrorsummary();
    }

    /**
     * Description
     * @returns {any}
     */
    function refreshErrorsummary () {
        const errorSummary = document.querySelector('#formerrorssummary');
        errorSummary.classList.add('hidden');
        removeAllListEntries();
    }

    // Create the pagination div and its contents
    const paginationHTML = `
    <div class="pagination">
    <a class="btn hidden" id="prev"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i></a>
    <a class="btn " id="next"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i>    </a>
    <a class="btn hidden" id="refresh"><i class="fa fa-refresh" aria-hidden="true"></i></a>
    </div>
    `;

    // Get a reference to the first form element in the document
    const form = document.querySelector('form');
    const signup = document.querySelector('div.signupform');
    // Append the pagination HTML to the form
    signup.insertAdjacentHTML('beforeend', paginationHTML);
    const errorSummaryUl = document.querySelector('#formerrorssummary ul');

    const submitButton = document.querySelector('input[type="submit"]');
    submitButton.addEventListener('click', () => {
         // Remove the event listener
        form.removeEventListener(formEventTypes.formFieldValidationFailed, fieldValidationFailedListener);
        refreshErrorsummary();
        form.addEventListener(formEventTypes.formFieldValidationFailed, fieldValidationFailedListener);
    });


    // Add the event listener
//    form.addEventListener(formEventTypes.formFieldValidationFailed, fieldValidationFailedListener);

    // form.addEventListener(formEventTypes.formFieldValidationFailed, e => {
    //     const message = e.detail.message;
    //     if(message != "") {
    //         addListEntry(message);
    //     }
    //     const errorSummary = document.querySelector('#formerrorssummary');
    //     errorSummary.classList.remove('hidden');
    // });

    const refreshButton = document.querySelector('#refresh');
    const previousButton = document.querySelector('#prev');
    const nextButton = document.querySelector('#next');
    //const submitButton = document.querySelector('#submit');
    const tabPanels = form.querySelectorAll('form > *');
    const fieldsets = form.querySelectorAll('form > fieldset');

    //const tabPanels = Array.from(tabPanels1).concat(Array.from(tabPanels2));
    //const isEmpty = (str) => !str.trim().length;
    let currentStep = 0;
    for(var x=1; x<tabPanels.length; x++) {
        alert(tabPanels[x]);
    }
    const steps = [6, 10]; // Initialize the array with 6 and 10
    for (let x = 1; x < fieldsets.length; x++) {
        steps.push(10 + x); // Add 10 + x to the array for each x from 1 to the length of fieldsets
    }
    steps.push(tabPanels.length - 1); // Add tabPanels.length - 1 as the last index in the array

    //const steps = [6, 10, 11, tabPanels.length-1];
    // Create the tab status HTML
    var tabStatusHTML = `
    <div class="tab-status">
        <span class="tab active">1</span>
    `;
    for(var sc = 2; sc <= steps.length; sc++) {
    tabStatusHTML += `
        <span class="tab">` + sc + `</span>
    `;
    }
    // Create the tab status HTML
    tabStatusHTML += `
    <span class="helppix"></span>
    </div>
    `;
    // Append the pagination HTML to the form
    signup.insertAdjacentHTML('beforebegin', tabStatusHTML);

    const tabTargets = document.querySelectorAll('.tab');

    // currentStep is 0
    for(var i=steps[currentStep]+1; i< tabPanels.length; i++) {
        tabPanels[i].classList.add('hidden');
    }

    // Next: Change UI relative to the current step and account for button permissions
    nextButton.addEventListener('click', (event) => {
        // Prevent default on links
        event.preventDefault();
        // Hide current tab
        for(var i = 0 ; i <= steps[currentStep]; i++) {
            tabPanels[i].classList.add('hidden');
        }
        tabTargets[currentStep].classList.remove('active');
        // Show next tab
        for(i=steps[currentStep]+1; i <= steps[currentStep + 1]; i++) {
            tabPanels[i].classList.remove('hidden');
        }
        currentStep += 1;
        tabTargets[currentStep].classList.add('active');
        //validateEntry();
        updateStatusDisplay();
    });

    // Previous: Change UI relative to current step and account for button permissions
    previousButton.addEventListener('click', (event) => {
        event.preventDefault();
        // Hide current tab
        for(var i = 0; i < tabPanels.length; i++) {
            tabPanels[i].classList.add('hidden');
        }
        tabTargets[currentStep].classList.remove('active');
        // Show previous tab
        if (currentStep < 2) {
            var start = -1;
        }  else {
            var start = steps[currentStep - 2];
        }
        for(i = start + 1; i <= steps[currentStep -1]; i++) {
            tabPanels[i].classList.remove('hidden');
        }
        tabTargets[currentStep - 1].classList.add('active');
        currentStep -= 1;
        nextButton.removeAttribute('disabled');
        updateStatusDisplay();
    });

    // Refresh: Change UI relative to current step and account for button permissions
    refreshButton.addEventListener('click', () => {
        refreshWizard();
    });



    /**
     * updateStatusDisplay
     * @returns {any}
     */
    function updateStatusDisplay() {
        // If on the last step, hide the next button and show submit
        if (currentStep === tabTargets.length - 1) {
            nextButton.classList.add('hidden');
            previousButton.classList.remove('hidden');
            refreshButton.classList.remove('hidden');
            //submitButton.classList.remove('hidden');
            //validateEntry();
            // If it's the first step hide the previous button
        } else if (currentStep == 0) {
            nextButton.classList.remove('hidden');
            previousButton.classList.add('hidden');
            refreshButton.classList.add('hidden');
            //submitButton.classList.add('hidden');
            // In all other instances display both buttons
        } else {
            nextButton.classList.remove('hidden');
            previousButton.classList.remove('hidden');
            refreshButton.classList.add('hidden');
            //submitButton.classList.add('hidden');
        }
    }


    // /**
    //  * Description
    //  * @returns {any}
    //  */
    // function validateEntry() {
    //     let input = tabPanels[currentStep].querySelector('input');
    //     // Start but disabling continue button
    //     nextButton.setAttribute('disabled', true);
    //     //submitButton.setAttribute('disabled', true);
    //     // Validate on initial function fire
    //     setButtonPermissions(input);
    //     // Validate on input
    //     input.addEventListener('input', () => setButtonPermissions(input));
    //     // Validate if bluring from input
    //     input.addEventListener('blur', () => setButtonPermissions(input));
    // }

    // /**
    //  * Description
    //  * @param {any} input
    //  * @returns {any}
    //  */
    // function setButtonPermissions(input) {
    //     if (isEmpty(input.value)) {
    //         nextButton.setAttribute('disabled', true);
    //         //submitButton.setAttribute('disabled', true);
    //     } else {
    //         nextButton.removeAttribute('disabled');
    //         //submitButton.removeAttribute('disabled');
    //     }
    // }
};
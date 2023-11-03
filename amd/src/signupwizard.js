export const init = () => {
    // Create the pagination div and its contents
    const paginationHTML = `
    <div class="pagination">
    <a class="btn hidden" id="prev"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i></a>
    <a class="btn " id="next"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i>    </a>
    </div>
    `;

    // Get a reference to the first form element in the document
    const form = document.querySelector('form');
    const signup = document.querySelector('div.signupform');
    // Append the pagination HTML to the form
    signup.insertAdjacentHTML('beforeend', paginationHTML);
    // other option.
    // const targetElement = document.querySelector('#fgroup_id_buttonar');

    // if (targetElement) {
    //   // Create a new div element to contain the content
    //   const newDiv = document.createElement('div');
    //   newDiv.innerHTML = paginationHTML;
    //   // Insert the new div before the target element
    //   targetElement.parentNode.insertBefore(newDiv, targetElement);
    // }

    // // Create the tab status HTML
    // const tabStatusHTML = `
    // <div class="tab-status">
    // <span class="tab active">1</span>
    // <span class="tab">2</span>
    // <span class="tab">3</span>
    // </div>
    // `;




    const previousButton = document.querySelector('#prev');
    const nextButton = document.querySelector('#next');
//    const submitButton = document.querySelector('#submit');
    const tabPanels1 = form.querySelectorAll('form > div.form-group');
    const tabPanels2 = form.querySelectorAll('form > fieldset');
    const tabPanels = Array.from(tabPanels1).concat(Array.from(tabPanels2));

    const isEmpty = (str) => !str.trim().length;
    let currentStep = 0;
    const steps = [4, 8, 10, tabPanels.length - 1];

    // Create the tab status HTML
    var tabStatusHTML = `
    <div class="tab-status">
        <span class="tab active">1</span>
    `;
    for(var sc = 2; sc < steps.length; sc++) {
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

    for(var i=steps[currentStep]+1; i< tabPanels.length; i++) {
        tabPanels[i].classList.add('hidden');
    }
    // for(var j =0 ; j <= steps[currentStep + 1]; j++) {
    //     tabPanels[j].classList.remove('hidden');
    // }

    // Next: Change UI relative to the current step and account for button permissions
    nextButton.addEventListener('click', (event) => {
        // Prevent default on links
        event.preventDefault();
        // Hide current tab
        for(var i = 0 ; i <= steps[currentStep]; i++) {
            window.console.log('hide' + tabPanels[i] + 'step' + steps[currentStep] + 'div' +  i + '----');
            tabPanels[i].classList.add('hidden');
        }
        tabTargets[currentStep].classList.remove('active');
        // Show next tab
        for(i=steps[currentStep]+1; i <= steps[currentStep + 1]; i++) {
            window.console.log('show' + tabPanels[i] + 'step' + steps[currentStep + 1] + 'div' + i + '+++++');
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
            window.console.log('hide' + tabPanels[i] + 'div' + i + '----');
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
            window.console.log('show' + tabPanels[i] +  'step' + steps[currentStep - 1] + 'div' + i + '+++++');

        }
        tabTargets[currentStep - 1].classList.add('active');
        currentStep -= 1;
        nextButton.removeAttribute('disabled');
        updateStatusDisplay();
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
      //submitButton.classList.remove('hidden');
      validateEntry();
      // If it's the first step hide the previous button
    } else if (currentStep == 0) {
      nextButton.classList.remove('hidden');
      previousButton.classList.add('hidden');
      //submitButton.classList.add('hidden');
      // In all other instances display both buttons
    } else {
      nextButton.classList.remove('hidden');
      previousButton.classList.remove('hidden');
      //submitButton.classList.add('hidden');
    }
  }


/**
 * Description
 * @returns {any}
 */
  function validateEntry() {
    let input = tabPanels[currentStep].querySelector('input');
    // Start but disabling continue button
    nextButton.setAttribute('disabled', true);
    //submitButton.setAttribute('disabled', true);
    // Validate on initial function fire
    setButtonPermissions(input);
    // Validate on input
    input.addEventListener('input', () => setButtonPermissions(input));
    // Validate if bluring from input
    input.addEventListener('blur', () => setButtonPermissions(input));
  }

/**
 * Description
 * @param {any} input
 * @returns {any}
 */
  function setButtonPermissions(input) {
    if (isEmpty(input.value)) {
      nextButton.setAttribute('disabled', true);
      //submitButton.setAttribute('disabled', true);
    } else {
      nextButton.removeAttribute('disabled');
      //submitButton.removeAttribute('disabled');
    }
  }
};
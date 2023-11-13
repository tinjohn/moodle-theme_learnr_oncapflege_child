
export const init = (strs) => {

    const togglechb = `
    <div>
        <input type="checkbox" id="toggle-password">
        <label id="label-toggle-password" title="`+ strs +`" for="toggle-password"></label>
    </div>`;

    // Create a new element (e.g., a <div>) with the content
    const newElement = document.createElement('div');
    newElement.innerHTML = togglechb;
    // Insert the new element after the input field
    const pwdelem =  document.querySelector('input[type="password"]');
    pwdelem.insertAdjacentElement('afterend', newElement);


    // Select the checkbox element
    const checkbox = document.querySelector('#toggle-password');

    // Add an event listener for the "change" event
    checkbox.addEventListener('change', function() {
        // Check if the checkbox is checked
        const passwordField = document.getElementById("id_password");
        if (this.checked) {
            // The checkbox is checked, perform an action here
            passwordField.type = "text"; // Change the input type to "text" when typing
            // Add your code to execute when the checkbox is checked
        } else {
            // The checkbox is unchecked, perform an action here
            passwordField.type = "password";
            // Add your code to execute when the checkbox is unchecked
        }
    });
};

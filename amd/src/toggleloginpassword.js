
export const init = () => {
    // Select the checkbox element
    const checkbox = document.querySelector('#toggle-password');

    // Add an event listener for the "change" event
    checkbox.addEventListener('change', function() {
        // Check if the checkbox is checked
        const passwordField = document.getElementById("password");
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

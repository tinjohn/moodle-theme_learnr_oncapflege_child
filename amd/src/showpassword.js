
export const init = () => {
        const passwordField = document.getElementById("id_password");

        passwordField.addEventListener("input", function() {
            this.type = "text"; // Change the input type to "text" when typing
        });

        passwordField.addEventListener("blur", function() {
            this.type = "password"; // Change the input type back to "password" when not focused
        });
};


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/admin_login.css">
</head>
<body>
    <div class="container">

            <!-- Brand Section for login, always visible -->
            <div class="brand-section" id="login-brand-section">
            <div class="brand-content">
                <div class="image-container">
                    <img src="images/chicken.jpg" alt="Brand Illustration">
                </div>
                <h2>New Customer?</h2>
                <p>Register Here to Order</p>
                <button class="signup-btn" onclick="showRegistration()">Sign Up</button>
            </div>
        </div>
        
        <!-- Login Section, initially visible -->
        <div class="login-section" id="login-section">
            <h2>Log In</h2>
            <form class="login-form" action="adminpage.php" method="POST">
                <div class="input-container">
                    <input type="text" name="username" placeholder="Username" required>
                    <i class="fas fa-user icon"></i>
                </div>
                
                <!-- Password Field with Toggle -->
                <div class="input-container">
                    <input type="password" id="login_password" name="password" placeholder="Password" required>
                    <i class="fas fa-eye icon" onclick="togglePassword('login_password')"></i>
                </div>
                <button type="submit" class="login-btn">Log In</button>
                <?php
                if (isset($error_message)) {
                    echo '<p style="color:red;">' . $error_message . '</p>';
                }
                ?>
            </form>
        </div>



        <!-- Brand Section for registration, initially hidden -->
        <div class="registeration-section" id="registration-brand-section">
            <div class="brand-content">
                <div class="image-container">
                    <img src="images/chicken.jpg" alt="Registration Illustration">
                </div>
                <h2>Welcome to Maigis</h2>
                <p>Fill in your details to create an account.</p>
            </div>
        </div>

<div id="registration-form" class="register-section" style="display:none;">
    <h2>Sign Up</h2>
    <form id="register-form" class="register-form">
        <div class="input-container">
            <input type="text" name="firstname" placeholder="Enter Firstname" required>
            <i class="fas fa-user icon"></i>
        </div>
        <div class="input-container">
            <input type="text" name="lastname" placeholder="Enter Lastname" required>
            <i class="fas fa-user icon"></i>
        </div>
        <div class="input-container">
            <input type="text" id="contact_no" name="contact_no" placeholder="Enter Contact No." maxlength="11" pattern="\d{11}" required 
            oninput="validateContactNumber()">
            <i class="fas fa-phone icon"></i>
            <p id="contact-error">Please enter a valid 11-digit phone number.</p>
        </div>
        <div class="input-container">
            <input type="text" name="username" placeholder="Enter Username" required>
            <i class="fas fa-user icon"></i>
        </div>
        <div class="input-container">
            <input type="password" id="reg_password" name="password" placeholder="Enter Password" required 
            oninput="validatePassword()">
            <i class="fas fa-eye icon" onclick="togglePassword('reg_password')"></i>
            <p id="password-error">Password must be at least 6 characters.</p>
            <p id="password-specialchar-error">Must not contain special characters.</p>
            
        </div>
        <div class="input-container">
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required
            oninput="validateConfirmPassword()">
            <i class="fas fa-eye icon" onclick="togglePassword('confirm_password')"></i>
            <p id="confirm-password-error">Passwords do not match.</p>
        </div>
        <button type="submit" class="register-btn">Register</button>
    </form>
</div>

<!-- OTP Input Section, initially hidden -->
<div id="otp-section" class="otp-section" style="display:none;">
    <h3>OTP has been sent to your mobile number. Please enter it below</h3>
    <form id="otp-form" class="otp-form" onsubmit="return verifyOtp();">
        <div class="input-container">
            <input type="text" id="otp_input" name="otp" placeholder="Enter OTP" maxlength="6" pattern="\d{6}" required>
            <i class="fas fa-lock icon"></i>
            <p id="otp-error" style="color: red; display: none;">Please enter a valid 6-digit OTP.</p>
        </div>
        <button type="submit" class="verify-btn">Verify OTP</button>
    </form>
    <!-- Resend OTP Button -->
    <button id="resend-otp-btn" class="resend-btn">Resend OTP</button>
    <p id="resend-message" style="color: green; display: none;">A new OTP has been sent to your phone.</p>
</div>

</div>
</div>
</div>

<!-- Success Modal -->
<div id="success-modal" class="modal" style="display:none;">
    <div class="modal-content">
        <h2>Registration Successful!</h2>
        <p>Your account has been created successfully. You can now log in.</p>
        <button id="close-modal-btn">Close</button>
    </div>
</div>

<!-- Error Modal -->
<div id="error-modal" class="modal" style="display:none;">
    <div class="error-modal-content">
        <h2>Error</h2>
        <p id="error-message">Invalid OTP entered. Please try again.</p>
        <button id="close-error-modal-btn">Close</button>
    </div>
</div>

<script>

function verifyOtp() {
    const otp = document.getElementById('otp_input').value;

    fetch('API/verifyOtp.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ otp_code: otp })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // OTP is valid, and account is created
            document.getElementById('otp-section').style.display = 'none'; // Hide OTP section
            document.getElementById('success-modal').style.display = 'block'; // Show success modal
            
            // Clear sessionStorage to reset active section
            sessionStorage.removeItem('activeSection');

            // Redirect to index.php after showing the success modal
            setTimeout(() => {
                window.location.href = 'index.php'; // Redirect to index.php
            }, 3000); // Redirect after 3 seconds
        } else {
            // Show error modal if OTP is invalid
            document.getElementById('error-message').textContent = 'Invalid OTP entered. Please try again.';
            document.getElementById('error-modal').style.display = 'block'; // Show error modal
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });

    return false; // Prevent default form submission
}


// Close the modal when the "Close" button is clicked
document.getElementById('close-modal-btn').addEventListener('click', function() {
    document.getElementById('success-modal').style.display = 'none';
});

function validateContactNumber() {
    return new Promise((resolve) => {
        const contactNo = document.getElementById('contact_no').value;
        const contactError = document.getElementById('contact-error');

        // Check if the input contains any non-numeric characters
        if (/[^0-9]/.test(contactNo)) {
            contactError.textContent = 'Please enter numbers only.';
            contactError.style.display = 'block'; // Show error message
            resolve(false); // Indicate invalid input
        } 
        // Check if the contact number has exactly 11 digits and contains only numbers
        else if (contactNo.length === 11) {
            contactError.style.display = 'none'; // Hide error message

            // Check if the contact number already exists in the database
            fetch('API/check_contact.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'contact_no=' + encodeURIComponent(contactNo)
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    contactError.textContent = 'This contact number is already registered.';
                    contactError.style.display = 'block'; // Show error message
                    resolve(false); // Indicate invalid input
                } else {
                    contactError.style.display = 'none'; // Hide error message
                    resolve(true); // Indicate valid input
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resolve(false); // Handle fetch error
            });
        } 
        // If the contact number does not have 11 digits yet
        else {
            contactError.textContent = 'Please enter a valid 11-digit phone number.';
            contactError.style.display = 'block'; // Show error message
            resolve(false); // Indicate invalid input
        }
    });
}


function validatePassword() {
    const passwordInput = document.getElementById('reg_password').value;
    const passwordError = document.getElementById('password-error');
    const passwordSpecialCharError = document.getElementById('password-specialchar-error');

    // Regex to check for special characters not allowed
    const specialCharRegex = /[^a-zA-Z0-9()-]/;

    // Initialize error visibility
    passwordError.style.display = 'none'; // Assume no error initially
    passwordSpecialCharError.style.display = 'none'; // Assume no error initially

    // Check if password contains special characters
    if (specialCharRegex.test(passwordInput)) {
        passwordSpecialCharError.style.display = 'block'; // Show special character error if found
    } else {
        // If no special characters, check the length
        if (passwordInput.length < 6) {
            passwordError.style.display = 'block'; // Show length error if less than 6 characters
        }
    }

    // If the password is valid (at least 6 characters and no special characters), hide both errors
    if (passwordInput.length >= 6 && !specialCharRegex.test(passwordInput)) {
        passwordError.style.display = 'none'; // Ensure length error is hidden
        passwordSpecialCharError.style.display = 'none'; // Ensure special character error is hidden
    }
}

function validateConfirmPassword() {
    const passwordInput = document.getElementById('reg_password').value;
    const confirmPasswordInput = document.getElementById('confirm_password').value;
    const confirmPasswordError = document.getElementById('confirm-password-error');

    // Check if the confirm password matches the original password
    if (passwordInput === confirmPasswordInput) {
        confirmPasswordError.style.display = 'none'; // Hide error message if passwords match
    } else {
        confirmPasswordError.style.display = 'block'; // Show error message if passwords do not match
    }
}

function validateForm() {
    // Validate all fields
    return validateContactNumber().then(isContactValid => {
        validatePassword(); // Call this but don't await it
        validateConfirmPassword(); // Call this but don't await it

        // Check if any error messages are visible
        const contactErrorVisible = !isContactValid; // Contact error visible if not valid
        const passwordErrorVisible = document.getElementById('password-error').style.display === 'block';
        const passwordSpecialCharErrorVisible = document.getElementById('password-specialchar-error').style.display === 'block';
        const confirmPasswordErrorVisible = document.getElementById('confirm-password-error').style.display === 'block';

        // If any error messages are visible, prevent form submission
        if (contactErrorVisible || passwordErrorVisible || passwordSpecialCharErrorVisible || confirmPasswordErrorVisible) {
            return false; // Prevent submission
        }

        return true; // Allow submission
    });
}

document.addEventListener("DOMContentLoaded", function () {
    // Check if a section was stored in sessionStorage and show that section
    const lastSection = sessionStorage.getItem('activeSection');

    if (lastSection === 'otp') {
        showOtpSection();
    } else if (lastSection === 'registration') {
        showRegistration();
    } else {
        showLogin();
    }
});

// Clear sessionStorage on login page load
document.addEventListener('DOMContentLoaded', function () {
    // Check if we are on the admin login page
    if (window.location.pathname.includes('admin_login.php')) {
        // Clear active section
        sessionStorage.removeItem('activeSection');
        
        // Ensure the login section is displayed by default
        showLogin();
    }
});

// Function to show the login section and hide others
function showLogin() {
    const loginSection = document.getElementById('login-section');
    const registrationForm = document.getElementById('registration-form');
    const loginBrandSection = document.getElementById('login-brand-section');
    const registrationBrandSection = document.getElementById('registration-brand-section');
    const otpSection = document.getElementById('otp-section');

    // Show login section and its brand section
    loginSection.style.display = "block";
    loginBrandSection.style.display = "block";

    // Hide other sections
    registrationForm.style.display = "none";
    registrationBrandSection.style.display = "none";
    otpSection.style.display = "none";
}

function showRegistration() {
    const loginSection = document.getElementById('login-section');
    const registrationForm = document.getElementById('registration-form');
    const loginBrandSection = document.getElementById('login-brand-section');
    const registrationBrandSection = document.getElementById('registration-brand-section');
    const otpSection = document.getElementById('otp-section');

    // Hide other sections
    loginSection.style.display = "none";
    loginBrandSection.style.display = "none";
    otpSection.style.display = "none";

    // Show registration form and its brand section
    registrationForm.style.display = "block";
    registrationBrandSection.style.display = "block";

    // Store the active section in sessionStorage
    sessionStorage.setItem('activeSection', 'registration');
}

function showOtpSection() {
    const loginSection = document.getElementById('login-section');
    const registrationForm = document.getElementById('registration-form');
    const loginBrandSection = document.getElementById('login-brand-section');
    const registrationBrandSection = document.getElementById('registration-brand-section');
    const otpSection = document.getElementById('otp-section');

    // Hide other sections
    loginSection.style.display = "none";
    loginBrandSection.style.display = "none";
    registrationForm.style.display = "none";
    registrationBrandSection.style.display = "block";

    // Show OTP section
    otpSection.style.display = "block";

    // Store the active section as OTP
    sessionStorage.setItem('activeSection', 'otp');
}

function togglePassword(inputId) {
        const passwordInput = document.getElementById(inputId);
        const icon = passwordInput.nextElementSibling;
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    document.getElementById('close-error-modal-btn').addEventListener('click', function() {
        document.getElementById('error-modal').style.display = 'none'; // Hide the error modal
    });

document.getElementById('register-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    validateForm().then(isValid => {
        if (isValid) {
            const formData = new FormData(this); // Gather form data

            // Submit the form data to register_config.php
            fetch('functions/register_config.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('registration-form').style.display = 'none';
                    document.getElementById('otp-section').style.display = 'block';
        
                    // Update sessionStorage to keep OTP section active on refresh
                    sessionStorage.setItem('activeSection', 'otp');

                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });
});

document.getElementById('resend-otp-btn').addEventListener('click', function() {
    // Disable the button temporarily to avoid rapid resend attempts
    this.disabled = true;
    document.getElementById('resend-message').style.display = 'none';

    // Send an AJAX request to resend OTP
    fetch('API/resendOtp.php', {
        method: 'POST',
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('resend-message').innerText = data.message;
            document.getElementById('resend-message').style.display = 'block';
        } else {
            alert('Error: ' + data.message);
        }
        // Re-enable button after 30 seconds
        setTimeout(() => {
            document.getElementById('resend-otp-btn').disabled = false;
        }, 30000);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        document.getElementById('resend-otp-btn').disabled = false;
    });
});


</script>
</body>
</html>

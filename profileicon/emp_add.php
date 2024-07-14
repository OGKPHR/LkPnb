<?php
session_start();
require_once('connection.php');

require_once "permission_check.php";
checkEmployeePermissions($conn);

// Function to load options
function loadOptions($conn, $table, $textColumn, $valueColumn)
{
    $query = "SELECT $valueColumn AS id, $textColumn AS position FROM $table";
    $result = mysqli_query($conn, $query);

    if ($result) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        return [];
    }
}

// Insert into database if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $bdate = $_POST["bdate"];
    $gender = $_POST["gender"];
    $jobpositionId = $_POST["jobposition"];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $house_no = $_POST["house_no"];
    $village = $_POST["village"];
    $subdistrict = $_POST["subdistrict"];
    $district = $_POST["district"];
    $province = $_POST["province"];
    $postcode = $_POST["postcode"];

    $query_employee = "INSERT INTO employee (username, password, fname, lname, bdate, gender, Job_id) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_employee = mysqli_prepare($conn, $query_employee);
    mysqli_stmt_bind_param($stmt_employee, "ssssssi", $username, $hashedPassword, $fname, $lname, $bdate, $gender, $jobpositionId);
    $result_employee = mysqli_stmt_execute($stmt_employee);

    if ($result_employee) {
        $emp_id = mysqli_insert_id($conn);

        $query_address = "INSERT INTO emp_address (emp_id, house_no, village, subdistrict, district, province, postcode) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_address = mysqli_prepare($conn, $query_address);
        mysqli_stmt_bind_param($stmt_address, "issssss", $emp_id, $house_no, $village, $subdistrict, $district, $province, $postcode);
        $result_address = mysqli_stmt_execute($stmt_address);

        if ($result_address) {
            $_SESSION['success_message'] = "Employee added successfully!";
        } else {
            $_SESSION['error_message'] = "Error adding address: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error_message'] = "Error adding employee: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt_employee);
    mysqli_stmt_close($stmt_address);

    mysqli_close($conn);

    // Redirect back to the employee addition page
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="unnamed.png">
    <title>ADDEMPLOYEE</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Add your CSS styles or link to an external stylesheet -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            width: 50%;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
        }

        input, select {
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .success_message {
            color: #4caf50;
        }

        .error_message {
            color: #f44336;
        }

        /* Add more styles as needed */
    </style>
</head>
<?php


include('menuli.php'); ?>
<body>

    <div class="container">
        <?php
        // Display success or error messages
        if (isset($_SESSION['success_message'])) {
            echo "<p class='success_message'>{$_SESSION['success_message']}</p>";
            unset($_SESSION['success_message']);
        }

        if (isset($_SESSION['error_message'])) {
            echo "<p class='error_message'>{$_SESSION['error_message']}</p>";
            unset($_SESSION['error_message']);
        }
        ?>
<script>
window.setTimeout(function() {
   document.querySelectorAll('.error_message, .success_message').forEach(function(message) {
     message.style.display = 'none';
   });
}, 3000);
</script>

<h2>Add Employee</h2>
<form id="employeeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <!-- Employee Information -->
            <label for="username">Username:</label>
            <input type="text" name="username" id="usernameInput" required>
            <div class="feedback-message" id="usernameFeedback"></div>

            <label for="password">Password:</label>
            <input type="password" name="password" id="passwordInput" required>
            <div class="feedback-message" id="passwordFeedback"></div>

            <label for="fname">First Name:</label>
            <input type="text" name="fname" id="fnameInput" required>
            <div class="feedback-message" id="fnameFeedback"></div>

            <label for="lname">Last Name:</label>
            <input type="text" name="lname" id="lnameInput" required>
            <div class="feedback-message" id="lnameFeedback"></div>

            <label for="bdate">Birthdate:</label>
            <input type="date" name="bdate" required>

            <label for="gender">Gender:</label>
            <select name="gender" required>
                <option value="M">Male</option>
                <option value="F">Female</option>
            </select>

            <label for="jobpositionSelect">Job Position:</label>

            <select name="jobposition" id="jobpositionSelect">
    <option value="" style="color: #555;">Select Job Position</option>
    <!-- Options will be dynamically loaded using AJAX -->
</select>
  
<label for="house_no">House Number:</label>
<input type="text" name="house_no" required>

<label for="village">Village:</label>
<input type="text" name="village" required>
            <!-- Address Information -->
        <!-- Select for Province -->
<label for="provinceSelect">Province:</label>
<select name="provinceSelect" id="provinceSelect" onchange="loadDistricts()">
    <!-- Options will be dynamically loaded using AJAX -->
</select>

<!-- Select for District (Amphures) -->
<label for="districtSelect">District (Amphures):</label>
<select name="districtSelect" id="districtSelect" onchange="loadAmphures(); updatePostcode()">
    <!-- Options will be dynamically loaded using AJAX -->
</select>

<!-- Select for Subdistrict -->
<label for="amphureSelect">Subdistrict:</label>
<select name="amphureSelect" id="amphureSelect"  onchange="loadSubdistrict()">
    <!-- Options will be dynamically loaded using AJAX -->
</select>

<input type="hidden" name="subdistrict" id="subdistrictInput">
<input type="hidden" name="district" id="districtInput">
<input type="hidden" name="province" id="provinceInput">

<!-- Postcode -->
<label for="postcode">Postcode:</label>
<input type="text" name="postcode" id="postcode" required readonly>


            <input id="addEmployeeButton" type="submit" value="Add Employee">
        </form>
    </div>

</body>
</html>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(document).ready(function () {
    // Load options for Job Positions on page load
    loadOptions('jobpositionSelect', 'load_jobpositions.php');

    // Function to load options
    function loadOptions(elementId, endpoint) {
        // Fetch job positions dynamically and update the select element
        $.ajax({
            type: 'GET',
            url: endpoint,
            dataType: 'json',
            success: function (response) {
                const selectElement = $('#' + elementId);
                const addEmployeeButton = $('#addEmployeeButton');
                const selectedValue = selectElement.val(); // Store the selected value
               
                selectElement.empty(); // Clear existing options

                // Add a placeholder option
                selectElement.append('<option value="" style="color: #555;">Select Job Position</option>');

                // Add new options
                response.forEach(option => {
                    const optionElement = $('<option>', {
                        value: option.id,
                        text: option.position // Use the job position text directly
                    });

                    // Set text color to a visible color
                    optionElement.css('color', '#333');

                    selectElement.append(optionElement);
                });

                // Restore the selected value
                selectElement.val(selectedValue);

                // Disable the button if the job position is not selected
                if (!selectedValue) {
                    addEmployeeButton.prop('disabled', true);
                    addEmployeeButton.css('background-color', 'gray');
                } else {
                    addEmployeeButton.prop('disabled', false);
                    addEmployeeButton.css('background-color', 'green');
                }
            },
            error: function (xhr, status, error) {
                console.log('Error loading job positions:', error);
            }
        });

        // Event listener for change in the job position select
        selectElement.on('change', function () {
            const selectedValue = $(this).val();
            const addEmployeeButton = $('#addEmployeeButton');

            // Disable the button if the job position is not selected
            if (!selectedValue) {
                addEmployeeButton.prop('disabled', true);
                addEmployeeButton.css('background-color', 'gray');
            } else {
                addEmployeeButton.prop('disabled', false);
                addEmployeeButton.css('background-color', 'green');
            }
        });
    }
});
</script>

<script>
    $(document).ready(function () {
        const usernameInput = $('#usernameInput');
        const passwordInput = $('#passwordInput');
        const fnameInput = $('#fnameInput');
        const lnameInput = $('#lnameInput');
        const addEmployeeButton = $('#addEmployeeButton');

        const usernameFeedback = $('#usernameFeedback');
        const passwordFeedback = $('#passwordFeedback');
        const fnameFeedback = $('#fnameFeedback');
        const lnameFeedback = $('#lnameFeedback');

        function validateUsernameLength(username) {
            return username.length >= 5 && username.length <= 10;
        }

        function validatePasswordLength(password) {
            return password.length >= 8 && password.length <= 12;
        }

        function updateFeedback(element, isValid, message) {
            element.text(isValid ? '' : message);
            element.css('color', isValid ? 'green' : 'red');
        }

        function updateButtonState() {
            const isUsernameValid = validateUsernameLength(usernameInput.val().trim());
            const isPasswordValid = validatePasswordLength(passwordInput.val().trim());

            updateFeedback(usernameFeedback, isUsernameValid, 'Username must be 5-7 characters.');
            updateFeedback(passwordFeedback, isPasswordValid, 'Password must be 8-12 characters.');

            const isFnameValid = fnameInput.val().trim().length > 0; // Add more specific validation if needed
            const isLnameValid = lnameInput.val().trim().length > 0; // Add more specific validation if needed

            const isFormValid = isUsernameValid && isPasswordValid && isFnameValid && isLnameValid;

            addEmployeeButton.prop('disabled', !isFormValid);
        }

        // Attach event listeners for input validation
        usernameInput.on('input', function () {
            usernameFeedback.show(); // Show feedback when typing
            updateButtonState();
        });

        passwordInput.on('input', function () {
            passwordFeedback.show(); // Show feedback when typing
            updateButtonState();
        });

        fnameInput.on('input', function () {
            fnameFeedback.show(); // Show feedback when typing
            updateButtonState();
        });

        lnameInput.on('input', function () {
            lnameFeedback.show(); // Show feedback when typing
            updateButtonState();
        });
    });
</script>


<script>
    $(document).ready(function () {
        const usernameInput = $('#usernameInput');
        const feedbackMessage = $('#usernameFeedback');
        const addEmployeeButton = $('#addEmployeeButton');

        usernameInput.on('input', function () {
            const username = $(this).val().trim();

            if (username !== '') {
                // Send an asynchronous request to check if the username is already in use
                $.ajax({
                    type: 'GET',
                    url: `check_empname.php?username=${encodeURIComponent(username)}`,
                    dataType: 'json',
                    success: function (response) {
                        if (response.exists) {
                            feedbackMessage.text('Username is already in use.');
                            addEmployeeButton.css('background-color', 'gray');
                            feedbackMessage.css('color', 'red');
                            addEmployeeButton.prop('disabled', true);
                        } else {
                            feedbackMessage.text('');
                            addEmployeeButton.css('background-color', 'green');
                            addEmployeeButton.prop('disabled', false);
                        }
                    },
                    error: function () {
                        feedbackMessage.text('Error checking username.');
                        feedbackMessage.css('color', 'red');
                        addEmployeeButton.prop('disabled', true);
                        addEmployeeButton.css('background-color', 'gray');
                    }
                });
            } else {
                feedbackMessage.text('');
                addEmployeeButton.css('background-color', 'green');
               addEmployeeButton.prop('disabled', false);
            }
        });
    });
    
</script>
<script>
    $(document).ready(function () {
        // Load options for Provinces on page load
        loadOptions('provinceSelect', 'load_provinces.php');
    });

    function loadDistricts() {
        // Load options for Districts based on the selected Province
        const provinceId = $('#provinceSelect').val();
        loadOptions('districtSelect', `load_districts.php?province=${encodeURIComponent(provinceId)}`);

        // Set the value of the hidden province input
        $('#provinceInput').val($('#provinceSelect option:selected').text());
        
    }

    function loadAmphures() {
        // Load options for Amphures based on the selected District
        const districtId = $('#districtSelect').val();
        loadOptions('amphureSelect', `load_amphures.php?district=${encodeURIComponent(districtId)}`);

        // Set the value of the hidden district input
        $('#districtInput').val($('#districtSelect option:selected').text());
        


      
    }
    function loadSubdistrict() {
      // Set the value of the hidden subdistrict input
      $('#subdistrictInput').val($('#amphureSelect option:selected').text());
    }
    function loadOptions(elementId, endpoint) {
        // Fetch options dynamically and update the select element
        $.ajax({
            type: 'GET',
            url: endpoint,
            dataType: 'json',
            success: function (response) {
                const selectElement = $('#' + elementId);
                selectElement.empty(); // Clear existing options

                // Add new options
                response.forEach(option => {
                    const optionElement = $('<option>', {
                        value: option.id,
                        text: option.name_th // Change this based on your table structure
                    });
                    selectElement.append(optionElement);
                });
            },
            error: function () {
                console.log(`Error loading options for ${elementId}.`);
            }
        });
    }

    function updatePostcode() {
        // Fetch the zip code based on the selected Amphure (Subdistrict)
        const amphureId = $('#districtSelect').val();
        $.ajax({
            type: 'GET',
            url: `get_zip_code.php?district=${encodeURIComponent(amphureId)}`, // Use 'district' instead of 'amphure'
            dataType: 'json',
            success: function (response) {
                // Update the postcode input field
                $('#postcode').val(response.zip_code);

                
            },
            error: function () {
                console.log('Error fetching zip code.');
            }
        });
    }
</script>

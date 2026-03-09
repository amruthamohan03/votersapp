<div class="page-content">
    <div class="page-container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h4 class="mb-0">
                            <i class="bi bi-shield-lock"></i> Voter Authentication
                        </h4>
                        <small>Verify your identity to vote</small>
                    </div>

                    <div class="card-body p-5">
                        <!-- ALERT CONTAINER -->
                        <div id="alertContainer"></div>

                        <!-- INFO BOX -->
                        <div class="alert alert-info mb-4" role="alert">
                            <i class="bi bi-info-circle"></i>
                            <strong>Instructions:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Enter your Roll Number (exactly as registered)</li>
                                <li>Enter the last 4 digits of your Registration Number as verification code</li>
                                <li>Example: If Reg No is REG20251001, enter <strong>1001</strong></li>
                            </ul>
                        </div>

                        <form id="voterAuthForm" method="POST" action="<?= APP_URL ?>voting/authenticate">
                            <!-- ROLL NUMBER -->
                            <div class="mb-3">
                                <label class="form-label" for="rollNumber">
                                    <i class="bi bi-hash"></i> Roll Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="rollNumber"
                                       name="student_roll_no" 
                                       placeholder="e.g., CT001"
                                       autocomplete="off"
                                       required>
                                <small class="text-muted">Enter your college roll number</small>
                            </div>

                            <!-- VERIFICATION CODE -->
                            <div class="mb-4">
                                <label class="form-label" for="verificationCode">
                                    <i class="bi bi-key"></i> Verification Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="verificationCode"
                                       name="verification_code" 
                                       placeholder="Last 4 digits of Reg No"
                                       maxlength="4"
                                       inputmode="numeric"
                                       autocomplete="off"
                                       required>
                                <small class="text-muted">Last 4 digits of your Registration Number</small>
                            </div>

                            <!-- SUBMIT BUTTON -->
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="bi bi-box-arrow-in-right"></i> Verify & Access Voting
                            </button>

                            <button type="reset" class="btn btn-light btn-lg w-100">
                                <i class="bi bi-arrow-clockwise"></i> Clear
                            </button>
                        </form>

                        <!-- HELP SECTION -->
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="mb-3">Need Help?</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="d-block mb-2">
                                        <strong>Can't remember your details?</strong><br>
                                        Contact the Student Affairs Office
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <small class="d-block mb-2">
                                        <strong>Not registered yet?</strong><br>
                                        Register in the Voter Management Portal
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="card-footer bg-light text-center py-3">
                        <small class="text-muted">
                            Voting Portal v1.0 | Secure Authentication System
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
}

.page-content {
    flex: 1;
    padding: 2rem 0;
}

.card {
    border: none;
    border-radius: 10px;
}

.form-control-lg {
    border-radius: 5px;
    font-size: 1rem;
}

.form-control-lg:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-lg {
    border-radius: 5px;
    font-weight: 500;
}

.alert-info {
    background-color: #e7f3ff;
    border-color: #b3d9ff;
    color: #004085;
}

.alert-info ul {
    padding-left: 1.5rem;
}

.alert-info li {
    margin-bottom: 0.5rem;
}
</style>

<script>
const form = document.getElementById('voterAuthForm');

form.addEventListener('submit', function(e) {
    e.preventDefault();

    const rollNumber = document.getElementById('rollNumber').value.trim();
    const verificationCode = document.getElementById('verificationCode').value.trim();

    // Validate
    if (!rollNumber) {
        showAlert('⚠ Please enter your roll number.', 'warning');
        document.getElementById('rollNumber').focus();
        return;
    }

    if (!verificationCode || verificationCode.length !== 4) {
        showAlert('⚠ Verification code must be exactly 4 digits.', 'warning');
        document.getElementById('verificationCode').focus();
        return;
    }

    // Submit form via AJAX
    const formData = new FormData();
    formData.append('student_roll_no', rollNumber);
    formData.append('verification_code', verificationCode);

    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Verifying...';

    fetch('<?= APP_URL ?>voting/authenticate', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('✅ ' + data.message, 'success');
            setTimeout(() => {
                window.location.href = data.redirect || '<?= APP_URL ?>voting';
            }, 1500);
        } else {
            showAlert('❌ ' + data.message, 'danger');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Verify & Access Voting';
            document.getElementById('verificationCode').value = '';
            document.getElementById('verificationCode').focus();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('❌ Error during authentication. Please try again.', 'danger');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Verify & Access Voting';
    });
});

function showAlert(message, type = 'info') {
    const alertHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    
    const container = document.getElementById('alertContainer');
    container.innerHTML = alertHTML;
    
    // Auto-dismiss after 5 seconds (except for errors)
    if (type !== 'danger') {
        setTimeout(() => {
            container.innerHTML = '';
        }, 5000);
    }
}

// Auto-format verification code to uppercase and numbers only
document.getElementById('verificationCode').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4);
});
</script>
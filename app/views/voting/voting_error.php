<div class="page-content">
    <div class="page-container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-lg border-danger text-center">
                    <!-- ERROR ICON -->
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <div class="display-1 text-danger mb-3">
                                <i class="bi bi-x-circle-fill"></i>
                            </div>
                            <h2 class="text-danger mb-2">Access Denied</h2>
                            <p class="text-muted lead">You are not eligible to vote at this time.</p>
                        </div>

                        <!-- REASON -->
                        <div class="alert alert-danger mb-4" role="alert">
                            <i class="bi bi-exclamation-circle"></i>
                            <strong class="d-block mt-2"><?= $message ?? 'Voting Access Restricted' ?></strong>
                            
                            <?php if (!empty($voter)): ?>
                                <div class="mt-3 text-start text-dark">
                                    <small>
                                        <p class="mb-1"><strong>Student Name:</strong> <?= htmlspecialchars($voter['student_name']) ?></p>
                                        <p class="mb-1"><strong>Roll Number:</strong> <?= htmlspecialchars($voter['student_roll_no']) ?></p>
                                        <p class="mb-0"><strong>Status:</strong> 
                                            <?php 
                                                if ($voter['has_voted'] === 'Y') {
                                                    echo '<span class="badge bg-warning">Already Voted</span>';
                                                } elseif ($voter['voting_status'] !== 'ELIGIBLE') {
                                                    echo '<span class="badge bg-danger">' . htmlspecialchars($voter['voting_status']) . '</span>';
                                                }
                                            ?>
                                        </p>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- REASONS CARD -->
                        <div class="card bg-light mb-4">
                            <div class="card-body text-start">
                                <h6 class="card-title text-primary mb-3">
                                    <i class="bi bi-info-circle"></i> Possible Reasons:
                                </h6>
                                <ul class="small mb-0">
                                    <li class="mb-2">
                                        <strong>Already Voted:</strong> You have already participated in the voting process. Each voter can only vote once.
                                    </li>
                                    <li class="mb-2">
                                        <strong>Not Eligible:</strong> Your voting status has been marked as "NOT ELIGIBLE" by the election committee. This may be due to administrative reasons.
                                    </li>
                                    <li class="mb-2">
                                        <strong>Suspended:</strong> Your voting privilege has been suspended. This is usually due to disciplinary action or other administrative reasons.
                                    </li>
                                    <li class="mb-0">
                                        <strong>Not Registered:</strong> You are not registered as a voter. Please contact the Student Affairs Office.
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="row gap-2">
                            <div class="col-12">
                                <a href="/" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-house"></i> Return to Home
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="<?= APP_URL ?>voting/authenticate" class="btn btn-outline-primary btn-lg w-100">
                                    <i class="bi bi-arrow-counterclockwise"></i> Try Again
                                </a>
                            </div>
                        </div>

                        <!-- CONTACT SUPPORT -->
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="mb-3">Need Help?</h6>
                            <p class="text-muted mb-3">
                                If you believe this is an error, or if you have questions about your eligibility, please contact the Student Affairs Office.
                            </p>
                            <div class="alert alert-info" role="alert">
                                <strong>📧 Email:</strong> <a href="mailto:studentaffairs@college.edu">studentaffairs@college.edu</a><br>
                                <strong>📱 Phone:</strong> +91-XXXX-XXXXXX<br>
                                <strong>🏢 Office Hours:</strong> Monday - Friday, 9:00 AM - 5:00 PM<br>
                                <strong>📍 Location:</strong> Student Affairs Office, Main Building
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="card-footer bg-light text-center py-3">
                        <small class="text-muted">
                            <i class="bi bi-shield-check"></i> Voting Portal v1.0 | Security & Compliance System
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
body {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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

.btn-lg {
    border-radius: 5px;
    font-weight: 500;
}

.alert-info a {
    color: #004085;
    font-weight: 600;
}
</style>
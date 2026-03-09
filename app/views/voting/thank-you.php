<div class="page-content">
    <div class="page-container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-lg border-success text-center">
                    <!-- SUCCESS ICON -->
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <div class="display-1 text-success mb-3">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <h2 class="text-success mb-2">Thank You for Voting!</h2>
                            <p class="text-muted lead">Your votes have been successfully recorded and submitted.</p>
                        </div>

                        <!-- CONFIRMATION DETAILS -->
                        <div class="alert alert-success mb-4" role="alert">
                            <i class="bi bi-info-circle"></i>
                            <strong>Voting Confirmed</strong>
                            <div class="mt-2 small">
                                <p class="mb-1">Date & Time: <strong id="votingTime"></strong></p>
                                <p class="mb-0">Status: <strong class="badge bg-success">Completed</strong></p>
                            </div>
                        </div>

                        <!-- IMPORTANT MESSAGE -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-primary">
                                    <i class="bi bi-exclamation-triangle"></i> Important
                                </h6>
                                <p class="card-text text-dark mb-0">
                                    Each voter can only vote once per position. Your votes cannot be changed after submission. 
                                    A confirmation email has been sent to your registered email address.
                                </p>
                            </div>
                        </div>

                        <!-- WHAT HAPPENS NEXT -->
                        <div class="mb-4">
                            <h6 class="text-dark mb-3">What Happens Next?</h6>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item bg-transparent border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <div class="badge badge-lg bg-primary rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                1
                                            </div>
                                        </div>
                                        <div class="col">
                                            <p class="mb-0"><strong>Voting Period Closes</strong><br>
                                            <small class="text-muted">All votes will be counted after the voting period ends</small></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item bg-transparent border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <div class="badge badge-lg bg-info rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                2
                                            </div>
                                        </div>
                                        <div class="col">
                                            <p class="mb-0"><strong>Results Announced</strong><br>
                                            <small class="text-muted">Voting results will be announced within 24 hours</small></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <div class="badge badge-lg bg-success rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                3
                                            </div>
                                        </div>
                                        <div class="col">
                                            <p class="mb-0"><strong>Elected Representatives Announced</strong><br>
                                            <small class="text-muted">Your new student leaders will be announced</small></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="row gap-2">
                            <div class="col">
                                <a href="<?= APP_URL ?>dashboard" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-house"></i> Return to Dashboard
                                </a>
                            </div>
                            <div class="col">
                                <a href="<?= APP_URL ?>voting/results" class="btn btn-outline-primary btn-lg w-100">
                                    <i class="bi bi-bar-chart"></i> View Results
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="card-footer bg-light text-center py-3">
                        <small class="text-muted">
                            <i class="bi bi-shield-check"></i> Your vote is secure and confidential
                        </small>
                    </div>
                </div>

                <!-- FAQ SECTION -->
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Frequently Asked Questions</h6>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="faqAccordion">
                            <!-- FAQ ITEM 1 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                        Can I change my vote after submission?
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p class="mb-0">No, once you submit your votes, they cannot be changed. Each voter can only vote once per position. Please ensure you are satisfied with your selections before finalizing.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ ITEM 2 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                        How is my privacy protected?
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p class="mb-0">Your vote is completely confidential. While we record that you have voted, we do not store any information linking your identity to your specific vote choices. All voting is conducted through a secure, encrypted system.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ ITEM 3 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                        When will results be announced?
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p class="mb-0">Voting results will be officially announced within 24 hours after the voting period closes. The administration will announce the results through various campus channels including email, portal, and notice boards.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ ITEM 4 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                        Who can I contact for support?
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p class="mb-0">For any issues or queries regarding the voting system, please contact the Student Affairs Office at <strong>studentaffairs@college.edu</strong> or visit their office during working hours.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
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

.badge-lg {
    font-size: 1rem;
    font-weight: bold;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: #667eea;
}

.accordion-button:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
}

.list-group-item {
    padding: 1rem;
}

.btn-lg {
    border-radius: 5px;
    font-weight: 500;
}
</style>

<script>
// Display current date and time
function updateVotingTime() {
    const now = new Date();
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    };
    document.getElementById('votingTime').textContent = now.toLocaleString('en-US', options);
}

// Call on page load
updateVotingTime();

// Show confetti or animation (optional)
window.addEventListener('load', function() {
    console.log('Voting page loaded successfully');
});
</script>
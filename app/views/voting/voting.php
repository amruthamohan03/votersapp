<div class="page-content">
    <div class="page-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom border-dashed bg-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="header-title mb-0">🗳️ Online Voting Portal</h4>
                                <small class="text-muted">Cast your votes for student positions</small>
                            </div>
                            <div class="text-end">
                                <div class="badge bg-success badge-lg mb-2">
                                    Voter: <?= htmlspecialchars($voter['student_name']) ?>
                                </div>
                                <br>
                                <small class="text-muted">Roll: <?= htmlspecialchars($voter['student_roll_no']) ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- ALERT CONTAINER -->
                        <div id="alertContainer"></div>

                        <!-- VOTING STATS -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="alert alert-info text-center" role="alert">
                                    <div class="h5 mb-0">
                                        <span class="badge bg-primary"><?= count($positions) ?></span>
                                    </div>
                                    <small>Positions to Vote</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="alert alert-warning text-center" role="alert">
                                    <div class="h5 mb-0">
                                        <span class="badge bg-warning" id="votesCount">0</span>
                                    </div>
                                    <small>Votes Cast</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="alert alert-secondary text-center" role="alert">
                                    <div class="h5 mb-0">
                                        <span class="badge bg-secondary"><?= count($candidates) ?></span>
                                    </div>
                                    <small>Total Candidates</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="alert alert-light text-center border" role="alert">
                                    <div class="h5 mb-0">
                                        <span id="votingProgress">0%</span>
                                    </div>
                                    <small>Progress</small>
                                </div>
                            </div>
                        </div>

                        <!-- FORM START -->
                        <form id="votingForm">
                            <!-- POSITIONS WITH CANDIDATES -->
                            <?php if (!empty($positions)): ?>
                                <?php foreach ($positions as $position): ?>
                                    <div class="voting-position mb-5 p-3 border rounded bg-light">
                                        <div class="mb-4">
                                            <h5 class="text-primary mb-1">
                                                <i class="bi bi-briefcase"></i> <?= htmlspecialchars($position['position_name']) ?>
                                            </h5>
                                            <small class="text-muted"><?= htmlspecialchars($position['description'] ?? '') ?></small>
                                        </div>

                                        <!-- CANDIDATES FOR THIS POSITION -->
                                        <div class="row">
                                            <?php 
                                                $positionCandidates = array_filter($candidates, 
                                                    fn($c) => $c['position_id'] == $position['id']
                                                );
                                                
                                                if (!empty($positionCandidates)): 
                                                    foreach ($positionCandidates as $candidate): 
                                            ?>
                                                <div class="col-md-6 mb-3">
                                                    <div class="candidate-card card h-100 border candidate-option" 
                                                         data-position-id="<?= $position['id'] ?>" 
                                                         data-candidate-id="<?= $candidate['id'] ?>"
                                                         role="button" tabindex="0">
                                                        
                                                        <div class="card-body">
                                                            <!-- CANDIDATE PHOTO -->
                                                            <?php if (!empty($candidate['candidate_photo'])): ?>
                                                                <div class="text-center mb-3">
                                                                    <img src="<?= htmlspecialchars($candidate['candidate_photo']) ?>" 
                                                                         alt="<?= htmlspecialchars($candidate['candidate_name']) ?>"
                                                                         class="img-fluid rounded-circle" 
                                                                         style="width: 80px; height: 80px; object-fit: cover;">
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="text-center mb-3">
                                                                    <div class="avatar-lg bg-light rounded-circle mx-auto"
                                                                         style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                                                        <i class="bi bi-person-fill text-muted" style="font-size: 2rem;"></i>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>

                                                            <!-- CANDIDATE INFO -->
                                                            <h6 class="mb-1"><?= htmlspecialchars($candidate['candidate_name']) ?></h6>
                                                            <small class="text-muted d-block mb-2">
                                                                Roll: <?= htmlspecialchars($candidate['candidate_roll_no'] ?? 'N/A') ?>
                                                            </small>
                                                            
                                                            <?php if (!empty($candidate['department_name'])): ?>
                                                                <small class="text-muted d-block mb-2">
                                                                    Dept: <?= htmlspecialchars($candidate['department_name']) ?>
                                                                </small>
                                                            <?php endif; ?>

                                                            <!-- BIO -->
                                                            <?php if (!empty($candidate['bio'])): ?>
                                                                <p class="small text-secondary mb-2" style="font-size: 0.85rem;">
                                                                    <?= htmlspecialchars(substr($candidate['bio'], 0, 80)) ?>...
                                                                </p>
                                                            <?php endif; ?>

                                                            <!-- VOTE BUTTON / CHECKED STATE -->
                                                            <div class="mt-3">
                                                                <input type="radio" 
                                                                       class="vote-radio" 
                                                                       name="position_<?= $position['id'] ?>" 
                                                                       value="<?= $candidate['id'] ?>"
                                                                       data-position-id="<?= $position['id'] ?>"
                                                                       data-candidate-id="<?= $candidate['id'] ?>"
                                                                       style="display: none;">
                                                                
                                                                <button type="button" class="btn btn-outline-primary btn-sm w-100 vote-btn"
                                                                        data-position-id="<?= $position['id'] ?>"
                                                                        data-candidate-id="<?= $candidate['id'] ?>">
                                                                    <i class="bi bi-check-circle"></i> Vote
                                                                </button>
                                                            </div>

                                                            <!-- VOTE CONFIRMATION -->
                                                            <div class="alert alert-success mt-2 d-none vote-confirmed" role="alert">
                                                                <i class="bi bi-check-circle-fill"></i> Vote Recorded
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php 
                                                    endforeach; 
                                                else: 
                                            ?>
                                                <div class="col-12">
                                                    <div class="alert alert-warning" role="alert">
                                                        No candidates available for this position.
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-danger" role="alert">
                                    No positions available for voting at this time.
                                </div>
                            <?php endif; ?>
                        </form>

                        <!-- SUBMIT VOTES SECTION -->
                        <div class="mt-5 pt-3 border-top">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-secondary btn-lg w-100" 
                                            onclick="if(confirm('Cancel voting? All votes will be discarded.')) window.location='<?= APP_URL ?>voting/authenticate';">
                                        <i class="bi bi-x-circle"></i> Cancel & Exit
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-success btn-lg w-100" id="submitVotesBtn">
                                        <i class="bi bi-check-circle"></i> Submit & Finalize Votes
                                    </button>
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
.candidate-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.candidate-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 123, 255, 0.3);
}

.candidate-card.selected {
    border: 3px solid #28a745;
    background-color: #f0f8f5;
}

.candidate-card.selected .vote-confirmed {
    display: block !important;
}

.vote-btn {
    transition: all 0.3s ease;
}

.vote-btn:hover {
    transform: translateY(-2px);
}

.voting-position {
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
let votedPositions = {};
const positionCount = <?= count($positions) ?>;

// Handle vote button clicks
document.querySelectorAll('.vote-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const positionId = this.dataset.positionId;
        const candidateId = this.dataset.candidateId;
        const candidateName = this.closest('.candidate-card').querySelector('h6').textContent;

        castVote(candidateId, positionId, candidateName);
    });
});

// Handle candidate card clicks (alternative voting method)
document.querySelectorAll('.candidate-card').forEach(card => {
    card.addEventListener('click', function(e) {
        if (e.target.closest('.vote-btn') || e.target.closest('.vote-radio')) return;
        
        const positionId = this.dataset.positionId;
        const candidateId = this.dataset.candidateId;
        const candidateName = this.querySelector('h6').textContent;

        castVote(candidateId, positionId, candidateName);
    });
});

function castVote(candidateId, positionId, candidateName) {
    const formData = new FormData();
    formData.append('candidate_id', candidateId);
    formData.append('position_id', positionId);

    fetch('<?= APP_URL ?>voting/castVote', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Always try to get text first
        return response.text().then(text => ({
            text: text,
            ok: response.ok,
            status: response.status
        }));
    })
    .then(data => {
        // Try to parse JSON
        try {
            const jsonData = JSON.parse(data.text);
            
            if (jsonData.success) {
                votedPositions[positionId] = {
                    candidateId: candidateId,
                    candidateName: candidateName
                };
                updateVotingUI(positionId, candidateId);
                showAlert('✅ ' + jsonData.message, 'success');
                updateProgress();
            } else {
                showAlert('❌ ' + (jsonData.message || 'Error casting vote'), 'danger');
            }
        } catch (parseError) {
            console.error('JSON Parse Error:', parseError);
            console.error('Raw Response:', data.text);
            showAlert('❌ Server error. Response: ' + data.text.substring(0, 100), 'danger');
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        showAlert('❌ Network error: ' + error.message, 'danger');
    });
}

function updateVotingUI(positionId, candidateId) {
    // Remove previous selection for this position
    document.querySelectorAll(`[data-position-id="${positionId}"]`).forEach(card => {
        card.classList.remove('selected');
        card.querySelector('.vote-confirmed')?.classList.add('d-none');
    });

    // Add selection to current candidate
    document.querySelector(`[data-position-id="${positionId}"][data-candidate-id="${candidateId}"]`).classList.add('selected');
    document.querySelector(`[data-position-id="${positionId}"][data-candidate-id="${candidateId}"]`).querySelector('.vote-confirmed').classList.remove('d-none');
    
    // Update vote count
    document.getElementById('votesCount').textContent = Object.keys(votedPositions).length;
}

function updateProgress() {
    const progress = Math.round((Object.keys(votedPositions).length / positionCount) * 100);
    document.getElementById('votingProgress').textContent = progress + '%';
}

function showAlert(message, type = 'info') {
    const alertHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    
    const container = document.getElementById('alertContainer');
    container.innerHTML = alertHTML;
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        container.innerHTML = '';
    }, 5000);
}

// Submit votes
document.getElementById('submitVotesBtn').addEventListener('click', function() {
    if (Object.keys(votedPositions).length === 0) {
        showAlert('⚠ Please cast at least one vote before submitting.', 'warning');
        return;
    }

    if (!confirm('Are you sure you want to finalize your votes?\n\nPositions voted: ' + Object.keys(votedPositions).length)) {
        return;
    }

    this.disabled = true;
    this.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting...';

    const formData = new FormData();
    fetch('<?= APP_URL ?>voting/submitVotes', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        return response.text().then(text => ({
            text: text,
            ok: response.ok
        }));
    })
    .then(data => {
        try {
            const jsonData = JSON.parse(data.text);
            
            if (jsonData.success) {
                showAlert('✅ ' + jsonData.message, 'success');
                setTimeout(() => {
                    window.location.href = jsonData.redirect || '<?= APP_URL ?>voting/thank-you';
                }, 2000);
            } else {
                showAlert('❌ ' + jsonData.message, 'danger');
                document.getElementById('submitVotesBtn').disabled = false;
                document.getElementById('submitVotesBtn').innerHTML = '<i class="bi bi-check-circle"></i> Submit & Finalize Votes';
            }
        } catch (parseError) {
            console.error('JSON Parse Error:', parseError);
            console.error('Raw Response:', data.text);
            showAlert('❌ Server error. Check console.', 'danger');
            document.getElementById('submitVotesBtn').disabled = false;
            document.getElementById('submitVotesBtn').innerHTML = '<i class="bi bi-check-circle"></i> Submit & Finalize Votes';
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        showAlert('❌ Network error: ' + error.message, 'danger');
        document.getElementById('submitVotesBtn').disabled = false;
        document.getElementById('submitVotesBtn').innerHTML = '<i class="bi bi-check-circle"></i> Submit & Finalize Votes';
    });
});
</script>
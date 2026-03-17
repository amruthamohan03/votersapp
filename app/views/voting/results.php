<div class="page-content">
    <div class="page-container">
        <!-- PAGE HEADER -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="header-title mb-0">📊 Voting Results & Statistics</h3>
                    <div>
                        <button class="btn btn-info btn-sm" id="refreshBtn" title="Refresh data">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                        <button class="btn btn-success btn-sm" id="exportCSVBtn" title="Download as Excel CSV">
                            <i class="bi bi-file-earmark-excel"></i> Export CSV
                        </button>
                        <button class="btn btn-danger btn-sm" id="exportPDFBtn" title="Download as PDF Report">
                            <i class="bi bi-file-earmark-pdf"></i> Export PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- OVERALL STATISTICS CARDS -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="d-block mb-2">Total Votes Cast</small>
                                <h3 class="mb-0" id="totalVotesCard"><?= $stats['total_votes_cast'] ?? 0 ?></h3>
                            </div>
                            <div style="font-size: 2.5rem; opacity: 0.3;">
                                <i class="bi bi-ballot-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-gradient-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="d-block mb-2">Voter Turnout</small>
                                <h3 class="mb-0" id="turnoutPercentageCard"><?= $turnoutPercentage ?>%</h3>
                                <small class="mt-2 d-block"><?= $votersTurnout ?> / <?= $totalEligible ?> voters</small>
                            </div>
                            <div style="font-size: 2.5rem; opacity: 0.3;">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-gradient-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="d-block mb-2">Positions</small>
                                <h3 class="mb-0" id="positionsCard"><?= count($positions) ?></h3>
                                <small class="mt-2 d-block"><?= $stats['total_candidates'] ?? 0 ?> candidates</small>
                            </div>
                            <div style="font-size: 2.5rem; opacity: 0.3;">
                                <i class="bi bi-briefcase"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-gradient-danger text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="d-block mb-2">Last Updated</small>
                                <small class="d-block" id="lastUpdatedCard">Just now</small>
                                <button class="btn btn-light btn-sm mt-2" onclick="toggleLiveUpdates()">
                                    <i class="bi bi-play-circle"></i> <span id="liveToggleText">Enable Live</span>
                                </button>
                            </div>
                            <div style="font-size: 2.5rem; opacity: 0.3;">
                                <i class="bi bi-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHARTS ROW -->
        <?php if (!empty($deptDistribution) || !empty($votingTrend)): ?>
        <div class="row mb-4">
            <?php if (!empty($deptDistribution)): ?>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header border-bottom border-dashed bg-light">
                        <h5 class="header-title mb-0">Department-wise Vote Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div id="deptChart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($votingTrend)): ?>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header border-bottom border-dashed bg-light">
                        <h5 class="header-title mb-0">Voting Trend Over Time</h5>
                    </div>
                    <div class="card-body">
                        <div id="trendChart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- POSITION-WISE RESULTS -->
        <?php if (!empty($positionResults)): ?>
            <?php foreach ($positionResults as $positionId => $result): ?>
            <div class="card mb-4">
                <div class="card-header border-bottom border-dashed bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="header-title mb-1">
                                <i class="bi bi-briefcase"></i> <?= htmlspecialchars($result['position']['position_name']) ?>
                            </h5>
                            <?php if (!empty($result['position']['description'])): ?>
                                <small class="text-muted"><?= htmlspecialchars($result['position']['description']) ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary">
                                Total Votes: <strong><?= $result['total_votes'] ?></strong>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- WINNER HIGHLIGHT -->
                    <?php if ($result['winner']): ?>
                    <div class="alert alert-success mb-4" role="alert">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-1">🏆 Leading Candidate</h6>
                                <strong><?= htmlspecialchars($result['winner']['candidate_name']) ?></strong>
                                <span class="badge bg-success ms-2"><?= $result['winner']['vote_count'] ?> votes</span>
                                <span class="badge bg-secondary ms-2"><?= $result['winner']['vote_percentage'] ?? 0 ?>%</span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- CANDIDATES TABLE / BARS -->
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Candidate Name</th>
                                    <th>Roll No</th>
                                    <th>Department</th>
                                    <th>Votes</th>
                                    <th>Percentage</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $rank = 1;
                                    foreach ($result['candidates'] as $candidate): 
                                        $votePercent = $candidate['vote_percentage'] ?? 0;
                                ?>
                                <tr>
                                    <td>
                                        <?php if ($rank === 1): ?>
                                            <span class="badge bg-gold">1st</span>
                                        <?php elseif ($rank === 2): ?>
                                            <span class="badge bg-secondary">2nd</span>
                                        <?php elseif ($rank === 3): ?>
                                            <span class="badge bg-warning">3rd</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark"><?= $rank ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($candidate['candidate_name']) ?></strong>
                                    </td>
                                    <td>
                                        <small><?= htmlspecialchars($candidate['candidate_roll_no'] ?? 'N/A') ?></small>
                                    </td>
                                    <td>
                                        <small><?= htmlspecialchars($candidate['department_name'] ?? 'N/A') ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= $candidate['vote_count'] ?></span>
                                    </td>
                                    <td>
                                        <strong><?= $votePercent ?>%</strong>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?= $votePercent ?>%; background: linear-gradient(90deg, #0d6efd, #0dcaf0);" 
                                                 aria-valuenow="<?= $votePercent ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                <?php if ($votePercent > 5): ?>
                                                    <small class="text-white" style="font-weight: 600;"><?= $votePercent ?>%</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    $rank++;
                                    endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- CHART FOR THIS POSITION -->
                    <div class="mt-4">
                        <canvas id="positionChart_<?= $positionId ?>" height="80"></canvas>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <h5 class="mt-3">No Results Available</h5>
                <p class="text-muted">Voting is in progress or no votes have been cast yet.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- STYLES -->
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .bg-gradient-warning {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .bg-gradient-danger {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .bg-gold {
        background-color: #ffc107;
        color: #000 !important;
    }

    .candidate-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .candidate-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }

    .progress {
        background-color: #e9ecef;
    }

    .header-title {
        color: #333;
        font-weight: 600;
        font-size: 1.25rem;
    }

    .card {
        border: 1px solid rgba(0,0,0,0.08);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .alert {
        border-radius: 0.5rem;
    }

    /* PDF Export Button Highlight */
    #exportPDFBtn {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }

    #exportPDFBtn:hover {
        animation: none;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }
</style>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
let liveUpdateInterval = null;
let isLiveUpdating = false;

// Initialize all position charts
function initializeCharts() {
    <?php foreach ($positionResults as $positionId => $result): ?>
        initPositionChart(<?= $positionId ?>, <?= json_encode($result['candidates']) ?>);
    <?php endforeach; ?>

    <?php if (!empty($deptDistribution)): ?>
        initDeptChart(<?= json_encode($deptDistribution) ?>);
    <?php endif; ?>

    <?php if (!empty($votingTrend)): ?>
        initTrendChart(<?= json_encode($votingTrend) ?>);
    <?php endif; ?>
}

// Initialize individual position chart
function initPositionChart(positionId, candidates) {
    const ctx = document.getElementById('positionChart_' + positionId);
    if (!ctx) return;

    const labels = candidates.map(c => c.candidate_name);
    const data = candidates.map(c => c.vote_count);
    const bgColors = generateColors(candidates.length);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Vote Count',
                data: data,
                backgroundColor: bgColors,
                borderColor: bgColors.map(c => c.replace('0.8', '1')),
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Initialize department distribution chart
function initDeptChart(deptData) {
    const ctx = document.getElementById('deptChart');
    if (!ctx) return;

    const labels = deptData.map(d => d.department_name || 'N/A');
    const data = deptData.map(d => d.vote_count);
    const bgColors = generateColors(deptData.length);

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: bgColors,
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Initialize voting trend chart
function initTrendChart(trendData) {
    const ctx = document.getElementById('trendChart');
    if (!ctx) return;

    const labels = trendData.map(t => t.vote_hour);
    const data = trendData.map(t => t.vote_count);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Votes Per Hour',
                data: data,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#667eea',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Generate random colors
function generateColors(count) {
    const colors = [
        'rgba(102, 126, 234, 0.8)',
        'rgba(245, 87, 108, 0.8)',
        'rgba(79, 172, 254, 0.8)',
        'rgba(0, 242, 254, 0.8)',
        'rgba(255, 193, 7, 0.8)',
        'rgba(76, 175, 80, 0.8)',
        'rgba(156, 39, 176, 0.8)',
        'rgba(33, 150, 243, 0.8)'
    ];
    return Array(count).fill(0).map((_, i) => colors[i % colors.length]);
}

// Refresh button
document.getElementById('refreshBtn').addEventListener('click', function() {
    location.reload();
});

// Export to CSV
document.getElementById('exportCSVBtn').addEventListener('click', function() {
    window.location.href = '<?= APP_URL ?>results/exportCSV';
});

// Export to PDF
document.getElementById('exportPDFBtn').addEventListener('click', function() {
    this.disabled = true;
    this.innerHTML = '<i class="bi bi-hourglass-split"></i> Generating...';
    
    setTimeout(() => {
        window.location.href = '<?= APP_URL ?>results/exportPDF';
        
        setTimeout(() => {
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Export PDF';
        }, 2000);
    }, 500);
});

// Toggle live updates
function toggleLiveUpdates() {
    isLiveUpdating = !isLiveUpdating;
    const toggleBtn = document.querySelector('#liveToggleText');
    
    if (isLiveUpdating) {
        toggleBtn.textContent = 'Disable Live';
        toggleBtn.parentElement.classList.add('active');
        
        // Refresh every 10 seconds
        liveUpdateInterval = setInterval(function() {
            updateStats();
        }, 10000);
        
        showNotification('✅ Live updates enabled. Page will refresh every 10 seconds.', 'success');
    } else {
        toggleBtn.textContent = 'Enable Live';
        toggleBtn.parentElement.classList.remove('active');
        clearInterval(liveUpdateInterval);
        showNotification('❌ Live updates disabled.', 'info');
    }
}

// Update statistics
function updateStats() {
    fetch('<?= APP_URL ?>results/getStats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalVotesCard').textContent = data.total_votes_cast;
                document.getElementById('turnoutPercentageCard').textContent = data.voter_turnout_percentage + '%';
                document.getElementById('lastUpdatedCard').textContent = new Date().toLocaleTimeString();
                
                // Show notification
                showNotification('✅ Results updated', 'success');
            }
        })
        .catch(error => console.error('Error updating stats:', error));
}

// Show notification
function showNotification(message, type = 'info') {
    const alertHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    
    document.body.insertAdjacentHTML('afterbegin', alertHTML);
    
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) alert.remove();
    }, 3000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});
</script>
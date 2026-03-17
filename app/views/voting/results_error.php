<div class="page-content">
    <div class="page-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div style="font-size: 4rem; color: #dc3545; margin-bottom: 1rem;">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <h3 class="mb-3">⚠️ Error Loading Results</h3>
                        <p class="text-muted mb-4">
                            <?= htmlspecialchars($message ?? 'An error occurred while loading the voting results.') ?>
                        </p>
                        <a href="<?= APP_URL ?>voting" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Back to Voting
                        </a>
                        <button onclick="location.reload()" class="btn btn-secondary ms-2">
                            <i class="bi bi-arrow-clockwise"></i> Retry
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
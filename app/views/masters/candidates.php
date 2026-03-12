<div class="page-content">
    <div class="page-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom border-dashed d-flex align-items-center">
                        <h4 class="header-title">Add New Candidate</h4>
                    </div>

                    <div class="card-body">
                        <form id="candidateInsertForm" method="post" >
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="candidate_name" class="form-label">Candidate Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="candidate_name" name="candidate_name"
                                        placeholder="Enter candidate name" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="candidate_roll_no" class="form-label">Roll No / ID</label>
                                    <input type="text" class="form-control" id="candidate_roll_no" name="candidate_roll_no"
                                        placeholder="Enter candidate roll number">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="position_id" class="form-label">Position <span class="text-danger">*</span></label>
                                    <select class="form-select select2" id="position_id" name="position_id" required
                                        data-placeholder="Select position">
                                        <option value="">-- Select Position --</option>
                                        <?php if (!empty($positions)): ?>
                                            <?php foreach ($positions as $pos): ?>
                                                <option value="<?= htmlspecialchars($pos['id']) ?>">
                                                    <?= htmlspecialchars($pos['position_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="voter_id" class="form-label">Voter / Student <span class="text-danger">*</span></label>
                                    <select class="form-select select2" id="voter_id" name="voter_id" required
                                        data-placeholder="Select voter">
                                        <option value="">-- Select Voter --</option>
                                        <?php if (!empty($voters)): ?>
                                            <?php foreach ($voters as $voter): ?>
                                                <option value="<?= htmlspecialchars($voter['id']) ?>">
                                                    <?= htmlspecialchars($voter['student_name']) ?> (<?= htmlspecialchars($voter['student_roll_no']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="department_id" class="form-label">Department</label>
                                    <select class="form-select select2" id="department_id" name="department_id"
                                        data-placeholder="Select department">
                                        <option value="">-- Select Department --</option>
                                        <?php if (!empty($departments)): ?>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?= htmlspecialchars($dept['id']) ?>">
                                                    <?= htmlspecialchars($dept['department_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nomination_date" class="form-label">Nomination Date</label>
                                    <input type="date" class="form-control" id="nomination_date" name="nomination_date">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="candidate_photo" class="form-label">Photo URL</label>
                                    <input type="text" class="form-control" id="candidate_photo" name="candidate_photo"
                                        placeholder="Enter photo URL">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="display_order" class="form-label">Display Order</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order"
                                        placeholder="Enter display order (e.g., 1, 2, 3)">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="bio" class="form-label">Bio / Manifesto</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="3"
                                        placeholder="Enter candidate bio or manifesto"></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="display" class="form-label">Display</label>
                                    <select class="form-select" id="display" name="display">
                                        <option value="Y" selected>Yes</option>
                                        <option value="N">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-content-save"></i> Save Candidate
                                </button>
                                <a href="" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div> <!-- end card-body -->
                    
                    <div class="card-body">
                        <h4 class="header-title">Candidates List</h4>

                        <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Candidate Name</th>
                                    <th>Roll No</th>
                                    <th>Position</th>
                                    <th>Voter</th>
                                    <th>Department</th>
                                    <th>Nomination Date</th>
                                    <th>Order</th>
                                    <th>Display</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                if (!empty($candidates)): ?>
                                    <?php foreach ($candidates as $row): ?>
                                        <tr id="candidateRow_<?= $row['id']; ?>">
                                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['candidate_roll_no']); ?></td>
                                            <td><?php echo htmlspecialchars($row['position_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['voter_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['department_id']); ?></td>
                                            <td><?= $row['nomination_date'] ? date('d-m-Y', strtotime($row['nomination_date'])) : '-'; ?></td>
                                            <td><?php echo htmlspecialchars($row['display_order']); ?></td>
                                            <td>
                                                <span class="badge <?= $row['display'] === 'Y' ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?= $row['display'] === 'Y' ? 'Yes' : 'No'; ?>
                                                </span>
                                            </td>
                                            <td><?= date('d-m-Y', strtotime($row['created_at'])); ?></td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-primary editCandidateBtn"
                                                    data-id="<?= $row['id']; ?>" title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                <a href="#" 
                                                    class="btn btn-sm btn-danger deleteCandidateBtn" 
                                                    data-id="<?= $row['id']; ?>" 
                                                    title="Delete">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">No candidates found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div> <!-- end card body-->

                </div> <!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->
    </div> <!-- end container -->

    <?php include(VIEW_PATH . 'layouts/partials/footer.php'); ?>
</div>

<!-- Edit Candidate Modal -->
<div class="modal fade" id="candidateEditModal" tabindex="-1" role="dialog" aria-labelledby="candidateEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="candidateUpdateForm" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="candidateEditModalLabel">Edit Candidate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Candidate Name -->
                    <div class="mb-2">
                        <label class="form-label">Candidate Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="candidate_name" id="candidate_name_edit" required>
                    </div>

                    <!-- Roll No -->
                    <div class="mb-2">
                        <label class="form-label">Roll No</label>
                        <input type="text" class="form-control" name="candidate_roll_no" id="candidate_roll_no_edit">
                    </div>

                    <!-- Position -->
                    <div class="mb-2">
                        <label class="form-label">Position <span class="text-danger">*</span></label>
                        <select class="form-select select2" name="position_id" id="position_id_edit" required>
                            <option value="">-- Select Position --</option>
                            <?php if (!empty($positions)): ?>
                                <?php foreach ($positions as $pos): ?>
                                    <option value="<?= htmlspecialchars($pos['id']) ?>">
                                        <?= htmlspecialchars($pos['position_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Voter -->
                    <div class="mb-2">
                        <label class="form-label">Voter / Student <span class="text-danger">*</span></label>
                        <select class="form-select select2" name="voter_id" id="voter_id_edit" required>
                            <option value="">-- Select Voter --</option>
                            <?php if (!empty($voters)): ?>
                                <?php foreach ($voters as $voter): ?>
                                    <option value="<?= htmlspecialchars($voter['id']) ?>">
                                        <?= htmlspecialchars($voter['student_name']) ?> (<?= htmlspecialchars($voter['student_roll_no']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Department -->
                    <div class="mb-2">
                        <label class="form-label">Department</label>
                        <select class="form-select select2" name="department_id" id="department_id_edit">
                            <option value="">-- Select Department --</option>
                            <?php if (!empty($departments)): ?>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= htmlspecialchars($dept['id']) ?>">
                                        <?= htmlspecialchars($dept['department_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Nomination Date -->
                    <div class="mb-2">
                        <label class="form-label">Nomination Date</label>
                        <input type="date" class="form-control" name="nomination_date" id="nomination_date_edit">
                    </div>

                    <!-- Photo URL -->
                    <div class="mb-2">
                        <label class="form-label">Photo URL</label>
                        <input type="text" class="form-control" name="candidate_photo" id="candidate_photo_edit">
                    </div>

                    <!-- Display Order -->
                    <div class="mb-2">
                        <label class="form-label">Display Order</label>
                        <input type="number" class="form-control" name="display_order" id="display_order_edit">
                    </div>

                    <!-- Bio -->
                    <div class="mb-2">
                        <label class="form-label">Bio / Manifesto</label>
                        <textarea class="form-control" name="bio" id="bio_edit" rows="3"></textarea>
                    </div>

                    <!-- Display -->
                    <div class="mb-2">
                        <label class="form-label">Display</label>
                        <select class="form-select" name="display" id="display_edit">
                            <option value="Y">Yes</option>
                            <option value="N">No</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update Candidate</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function () {
    // Insert Candidate
    $('#candidateInsertForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '<?php echo APP_URL; ?>/candidates/crudData/insertion',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    alert('✅ Candidate inserted successfully!');
                    $('#candidateInsertForm')[0].reset();
                    location.reload(); 
                } else {
                    alert('❌ ' + res.message);
                }
            },
            error: function(xhr, status, error) {
                alert('❌ Error: ' + xhr.responseText);
            }
        });
    });

    // Open Edit Modal and load data
    $(document).on('click', '.editCandidateBtn', function (e) {
        e.preventDefault();
        let id = $(this).data('id');

        $.ajax({
            url: 'getCandidateById',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    const data = res.data;

                    $('#candidate_name_edit').val(data.candidate_name);
                    $('#candidate_roll_no_edit').val(data.candidate_roll_no);
                    $('#position_id_edit').val(data.position_id).trigger('change');
                    $('#voter_id_edit').val(data.voter_id).trigger('change');
                    $('#department_id_edit').val(data.department_id).trigger('change');
                    $('#nomination_date_edit').val(data.nomination_date);
                    $('#candidate_photo_edit').val(data.candidate_photo);
                    $('#display_order_edit').val(data.display_order);
                    $('#bio_edit').val(data.bio);
                    $('#display_edit').val(data.display);

                    $('#candidateEditModal').data('id', id).modal('show');
                } else {
                    alert(res.message);
                }
            },
            error: function() {
                alert('Error fetching candidate data.');
            }
        });
    });

    // Update Candidate
    $('#candidateUpdateForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#candidateEditModal').data('id');

        $.ajax({
            url: 'crudData/updation?id=' + id,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    alert('✅ Candidate updated successfully!');
                    $('#candidateEditModal').modal('hide');
                    location.reload();
                } else {
                    alert('❌ ' + res.message);
                }
            },
            error: function() {
                alert('Error updating candidate.');
            }
        });
    });

    // Delete Candidate
    $(document).on('click', '.deleteCandidateBtn', function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        if (!confirm('Are you sure you want to delete this candidate?')) {
            return;
        }

        $.ajax({
            url: 'crudData/deletion?id=' + id,
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    alert('✅ Candidate deleted successfully!');
                    $('#candidateRow_' + id).fadeOut(500, function() {
                        $(this).remove();
                    });
                    location.reload();
                } else {
                    alert('❌ ' + res.message);
                }
            },
            error: function() {
                alert('Error deleting candidate.');
            }
        });
    });

});
</script>
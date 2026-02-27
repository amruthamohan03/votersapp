<div class="page-content">
    <div class="page-container">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <div class="card-header border-bottom border-dashed d-flex align-items-center">
                        <h4 class="header-title">Voter Registration</h4>
                    </div>

                    <div class="card-body">
                        <form id="voterInsertForm">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Student Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="student_name" required>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Roll Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="student_roll_no" placeholder="e.g., CT001" required>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Registration No</label>
                                    <input type="text" class="form-control" name="registration_no" placeholder="e.g., REG20251001">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="student@college.edu">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Mobile</label>
                                    <input type="tel" class="form-control" name="mobile" placeholder="+91-9876543210">
                                </div>

                                <!-- DEPARTMENT DROPDOWN -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-select" name="department_id" required>
                                        <option value="">-- Select Department --</option>
                                        <?php if (!empty($departments)): ?>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['department_name']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- SEMESTER DROPDOWN -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Semester <span class="text-danger">*</span></label>
                                    <select class="form-select" name="semester_id" required>
                                        <option value="">-- Select Semester --</option>
                                        <?php if (!empty($semesters)): ?>
                                            <?php foreach ($semesters as $sem): ?>
                                                <option value="<?= $sem['id'] ?>"><?= htmlspecialchars($sem['semester_name']) ?> (<?= htmlspecialchars($sem['academic_year']) ?>)</option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- CLASS SECTION DROPDOWN -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Class/Section <span class="text-danger">*</span></label>
                                    <select class="form-select" name="class_section_id" required>
                                        <option value="">-- Select Class/Section --</option>
                                        <?php if (!empty($classSections)): ?>
                                            <?php foreach ($classSections as $cls): ?>
                                                <option value="<?= $cls['id'] ?>"><?= htmlspecialchars($cls['class_name']) ?>-<?= htmlspecialchars($cls['section_name']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- VOTING STATUS DROPDOWN -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Voting Status <span class="text-danger">*</span></label>
                                    <select class="form-select" name="voting_status" required>
                                        <option value="ELIGIBLE">Eligible</option>
                                        <option value="NOT_ELIGIBLE">Not Eligible</option>
                                        <option value="SUSPENDED">Suspended</option>
                                    </select>
                                </div>

                                <!-- DISPLAY -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Display</label>
                                    <select class="form-select" name="display">
                                        <option value="Y">Yes</option>
                                        <option value="N">No</option>
                                    </select>
                                </div>

                            </div>

                            <div class="text-end">
                                <button class="btn btn-primary">Register Voter</button>
                                <a href="" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>

                    <!-- VOTER LIST -->
                    <div class="card-body">
                        <h4 class="header-title">Voters List</h4>

                        <table id="voter-datatable" class="table table-striped nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student Name</th>
                                    <th>Roll Number</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Class/Section</th>
                                    <th>Voting Status</th>
                                    <th>Voted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($result)): $i=1; ?>
                                    <?php foreach ($result as $row): ?>

                                        <tr id="row_<?= $row['id'] ?>">
                                            <td><?= $i++ ?></td>
                                            <td><?= htmlspecialchars($row['student_name']) ?></td>

                                            <!-- ROLL NUMBER -->
                                            <td>
                                                <code><?= htmlspecialchars($row['student_roll_no']) ?></code>
                                            </td>

                                            <!-- EMAIL -->
                                            <td>
                                                <small><?= htmlspecialchars($row['email'] ?? '-') ?></small>
                                            </td>

                                            <!-- DEPARTMENT NAME -->
                                            <td>
                                                <?php if (!empty($row['department_name'])): ?>
                                                    <span class='badge bg-info'><?= htmlspecialchars($row['department_name']) ?></span>
                                                <?php else: ?>
                                                    <span class='badge bg-secondary'>Not Set</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- CLASS/SECTION -->
                                            <td>
                                                <?php if (!empty($row['class_name'])): ?>
                                                    <span class='badge bg-primary'><?= htmlspecialchars($row['class_name']) ?>-<?= htmlspecialchars($row['section_name']) ?></span>
                                                <?php else: ?>
                                                    <span class='badge bg-secondary'>-</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- VOTING STATUS -->
                                            <td>
                                                <?php if ($row['voting_status'] == 'ELIGIBLE'): ?>
                                                    <span class="badge bg-success">Eligible</span>
                                                <?php elseif ($row['voting_status'] == 'NOT_ELIGIBLE'): ?>
                                                    <span class="badge bg-danger">Not Eligible</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Suspended</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- HAS VOTED -->
                                            <td>
                                                <?php if ($row['has_voted'] == 'Y'): ?>
                                                    <span class="badge bg-success">Yes</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">No</span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <a href="#" class="btn btn-sm btn-primary editVoterBtn" data-id="<?= $row['id'] ?>">
                                                    <i class="ti ti-edit"></i>
                                                </a>

                                                <a href="#" class="btn btn-sm btn-danger deleteVoterBtn" data-id="<?= $row['id'] ?>">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>

                        </table>

                    </div>

                </div>
            </div>
        </div>
    </div>
    <?php include(VIEW_PATH . 'layouts/partials/footer.php'); ?>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="voterEditModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="voterUpdateForm">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Voter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Student Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="student_name" id="edit_student_name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Roll Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="student_roll_no" id="edit_student_roll_no" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Registration No</label>
                            <input type="text" class="form-control" name="registration_no" id="edit_registration_no">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" id="edit_email">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Mobile</label>
                            <input type="tel" class="form-control" name="mobile" id="edit_mobile">
                        </div>

                        <!-- DEPARTMENT DROPDOWN -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select" name="department_id" id="edit_department_id" required>
                                <option value="">-- Select Department --</option>
                                <?php if (!empty($departments)): ?>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['department_name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- SEMESTER DROPDOWN -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Semester <span class="text-danger">*</span></label>
                            <select class="form-select" name="semester_id" id="edit_semester_id" required>
                                <option value="">-- Select Semester --</option>
                                <?php if (!empty($semesters)): ?>
                                    <?php foreach ($semesters as $sem): ?>
                                        <option value="<?= $sem['id'] ?>"><?= htmlspecialchars($sem['semester_name']) ?> (<?= htmlspecialchars($sem['academic_year']) ?>)</option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- CLASS SECTION DROPDOWN -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Class/Section <span class="text-danger">*</span></label>
                            <select class="form-select" name="class_section_id" id="edit_class_section_id" required>
                                <option value="">-- Select Class/Section --</option>
                                <?php if (!empty($classSections)): ?>
                                    <?php foreach ($classSections as $cls): ?>
                                        <option value="<?= $cls['id'] ?>"><?= htmlspecialchars($cls['class_name']) ?>-<?= htmlspecialchars($cls['section_name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- VOTING STATUS DROPDOWN -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Voting Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="voting_status" id="edit_voting_status" required>
                                <option value="ELIGIBLE">Eligible</option>
                                <option value="NOT_ELIGIBLE">Not Eligible</option>
                                <option value="SUSPENDED">Suspended</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Display</label>
                            <select name="display" class="form-select" id="edit_display">
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function(){

    $('#voter-datatable').DataTable();

    /* INSERT */
    $('#voterInsertForm').submit(function(e){
        e.preventDefault();

        $.post("<?= APP_URL ?>voter/crudData/insertion", $(this).serialize(), function(res){
            alert(res.message);
            if(res.success) location.reload();
        }, 'json');
    });

    /* DELETE */
    $(document).on('click','.deleteVoterBtn',function(){
        if(!confirm("Delete this voter record?")) return;
        let id = $(this).data('id');

        $.post("<?= APP_URL ?>voter/crudData/deletion?id="+id, function(res){
            alert(res.message);
            if(res.success) location.reload();
        }, 'json');
    });

    /* OPEN EDIT MODAL */
    $(document).on('click','.editVoterBtn',function(e){
        e.preventDefault();
        let id = $(this).data('id');

        $.get("<?= APP_URL ?>voter/getVoterById",{id:id}, function(res){
            if(res.success){
                let d = res.data;

                $('#edit_student_name').val(d.student_name);
                $('#edit_student_roll_no').val(d.student_roll_no);
                $('#edit_registration_no').val(d.registration_no);
                $('#edit_email').val(d.email);
                $('#edit_mobile').val(d.mobile);
                $('#edit_department_id').val(d.department_id);
                $('#edit_semester_id').val(d.semester_id);
                $('#edit_class_section_id').val(d.class_section_id);
                $('#edit_voting_status').val(d.voting_status);
                $('#edit_display').val(d.display);

                $('#voterEditModal').data('id', id).modal('show');
            }
        }, 'json');
    });

    /* UPDATE */
    $('#voterUpdateForm').submit(function(e){
        e.preventDefault();

        let id = $('#voterEditModal').data('id');

        $.post("<?= APP_URL ?>voter/crudData/updation?id="+id, $(this).serialize(), function(res){
            alert(res.message);
            if(res.success){
                $('#voterEditModal').modal('hide');
                location.reload();
            }
        }, 'json');
    });

});
</script>
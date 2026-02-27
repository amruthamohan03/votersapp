<?php

class VoterController extends Controller
{
    public function index()
    {
        $db = new Database();
        
        // Get voters with department, semester, and class/section names using JOINs
        $query = "SELECT v.*, 
                         d.department_name,
                         s.semester_name,
                         s.academic_year,
                         cs.class_name,
                         cs.section_name
                  FROM voters_t v
                  LEFT JOIN department_master_t d ON v.department_id = d.id
                  LEFT JOIN semester_master_t s ON v.semester_id = s.id
                  LEFT JOIN class_section_master_t cs ON v.class_section_id = cs.id
                  WHERE v.display = 'Y'
                  ORDER BY v.student_roll_no ASC";
        $result = $db->customQuery($query);
        
        // Get all departments for dropdown
        $departments = $db->selectData('department_master_t', 'id, department_name', 
                                      ['display' => 'Y'], 'department_name ASC');
        
        // Get all semesters for dropdown
        $semesters = $db->selectData('semester_master_t', 'id, semester_name, academic_year', 
                                    ['display' => 'Y'], 'academic_year DESC, semester_name ASC');
        
        // Get all class sections for dropdown
        $classSections = $db->selectData('class_section_master_t', 'id, class_name, section_name', 
                                         ['display' => 'Y'], 'class_name ASC');

        $data = [
            'title' => 'Voter Registration',
            'result' => $result,
            'departments' => $departments,
            'semesters' => $semesters,
            'classSections' => $classSections
        ];

        $this->viewWithLayout('voters/voters', $data);
    }

    public function crudData($action = 'insertion')
    {
        $db = new Database();
        $table = 'voters_t';

        function s($v) { return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8'); }

        /* ------------------------------------
         * INSERTION
         * ------------------------------------ */
        if ($action === 'insertion' && $_SERVER['REQUEST_METHOD'] == 'POST') {

            header('Content-Type: application/json');

            $data = [
                'college_id'         => 1,  // Set from session if needed
                'student_name'       => s($_POST['student_name']),
                'student_roll_no'    => s($_POST['student_roll_no']),
                'registration_no'    => s($_POST['registration_no'] ?? ''),
                'email'              => s($_POST['email'] ?? ''),
                'mobile'             => s($_POST['mobile'] ?? ''),
                'department_id'      => (int)($_POST['department_id'] ?? 0),
                'semester_id'        => (int)($_POST['semester_id'] ?? 0),
                'class_section_id'   => (int)($_POST['class_section_id'] ?? 0),
                'voting_status'      => s($_POST['voting_status'] ?? 'ELIGIBLE'),
                'display'            => ($_POST['display'] ?? 'Y'),
                'created_by'         => 1,
                'updated_by'         => 1,
            ];

            // Validation
            if (!$data['student_name']) {
                echo json_encode(['success' => false, 'message' => '⚠ Please fill required fields.']);
                exit;
            }

            if (!$data['student_roll_no']) {
                echo json_encode(['success' => false, 'message' => '⚠ Please enter roll number.']);
                exit;
            }

            if ($data['department_id'] <= 0) {
                echo json_encode(['success' => false, 'message' => '⚠ Please select a department.']);
                exit;
            }

            if ($data['semester_id'] <= 0) {
                echo json_encode(['success' => false, 'message' => '⚠ Please select a semester.']);
                exit;
            }

            if ($data['class_section_id'] <= 0) {
                echo json_encode(['success' => false, 'message' => '⚠ Please select a class/section.']);
                exit;
            }

            // Check for duplicate roll number
            $existing = $db->selectData($table, 'id', 
                                       ['student_roll_no' => $data['student_roll_no'], 'college_id' => 1]);
            if (!empty($existing)) {
                echo json_encode(['success' => false, 'message' => '⚠ This roll number already exists.']);
                exit;
            }

            $insertId = $db->insertData($table, $data);

            echo json_encode([
                'success' => $insertId ? true : false,
                'message' => $insertId ? '✅ Voter registered successfully!' : '❌ Insert failed.'
            ]);
            exit;
        }

        /* ------------------------------------
         * UPDATION
         * ------------------------------------ */
        if ($action === 'updation' && $_SERVER['REQUEST_METHOD'] == 'POST') {

            header('Content-Type: application/json');

            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => '❌ Invalid ID']);
                exit;
            }

            $data = [
                'student_name'       => s($_POST['student_name']),
                'student_roll_no'    => s($_POST['student_roll_no']),
                'registration_no'    => s($_POST['registration_no'] ?? ''),
                'email'              => s($_POST['email'] ?? ''),
                'mobile'             => s($_POST['mobile'] ?? ''),
                'department_id'      => (int)($_POST['department_id'] ?? 0),
                'semester_id'        => (int)($_POST['semester_id'] ?? 0),
                'class_section_id'   => (int)($_POST['class_section_id'] ?? 0),
                'voting_status'      => s($_POST['voting_status'] ?? 'ELIGIBLE'),
                'display'            => ($_POST['display'] ?? 'Y'),
                'updated_by'         => 1,
                'updated_at'         => date('Y-m-d H:i:s')
            ];

            // Validation
            if (!$data['student_name']) {
                echo json_encode(['success' => false, 'message' => '⚠ Please fill required fields.']);
                exit;
            }

            if (!$data['student_roll_no']) {
                echo json_encode(['success' => false, 'message' => '⚠ Please enter roll number.']);
                exit;
            }

            if ($data['department_id'] <= 0) {
                echo json_encode(['success' => false, 'message' => '⚠ Please select a department.']);
                exit;
            }

            if ($data['semester_id'] <= 0) {
                echo json_encode(['success' => false, 'message' => '⚠ Please select a semester.']);
                exit;
            }

            if ($data['class_section_id'] <= 0) {
                echo json_encode(['success' => false, 'message' => '⚠ Please select a class/section.']);
                exit;
            }

            // Check for duplicate roll number (excluding current record)
            $existing = $db->selectData($table, 'id', 
                                       ['student_roll_no' => $data['student_roll_no'], 'college_id' => 1]);
            if (!empty($existing) && $existing[0]['id'] != $id) {
                echo json_encode(['success' => false, 'message' => '⚠ This roll number already exists.']);
                exit;
            }

            $update = $db->updateData($table, $data, ['id' => $id]);

            echo json_encode([
                'success' => $update ? true : false,
                'message' => $update ? '✅ Voter record updated successfully!' : '❌ Update failed.'
            ]);
            exit;
        }

        /* ------------------------------------
         * DELETION
         * ------------------------------------ */
        if ($action === 'deletion') {
            
            header('Content-Type: application/json');

            $id = (int)($_GET['id'] ?? 0);

            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => '❌ Invalid ID']);
                exit;
            }

            // Soft delete - set display to 'N'
            $delete = $db->updateData($table, ['display' => 'N'], ['id' => $id]);

            echo json_encode([
                'success' => $delete ? true : false,
                'message' => $delete ? '✅ Voter record deleted successfully!' : '❌ Delete failed.'
            ]);
            exit;
        }

        echo json_encode(['success' => false, 'message' => '❌ Invalid request']);
        exit;
    }

    public function getVoterById()
    {
        header('Content-Type: application/json');

        $db = new Database();
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => '❌ Invalid ID']);
            exit;
        }

        $row = $db->selectData('voters_t', '*', ['id' => $id]);

        echo json_encode(!empty($row)
            ? ['success' => true, 'data' => $row[0]]
            : ['success' => false, 'message' => '❌ Voter not found']
        );
        exit;
    }

    /**
     * Get voters by semester (for voting interface)
     */
    public function getVotersBySemester()
    {
        header('Content-Type: application/json');

        $db = new Database();
        $semester_id = (int)($_GET['semester_id'] ?? 0);

        if ($semester_id <= 0) {
            echo json_encode(['success' => false, 'message' => '❌ Invalid semester']);
            exit;
        }

        $query = "SELECT v.*, 
                         d.department_name,
                         s.semester_name,
                         cs.class_name,
                         cs.section_name
                  FROM voters_t v
                  LEFT JOIN department_master_t d ON v.department_id = d.id
                  LEFT JOIN semester_master_t s ON v.semester_id = s.id
                  LEFT JOIN class_section_master_t cs ON v.class_section_id = cs.id
                  WHERE v.semester_id = $semester_id 
                    AND v.voting_status = 'ELIGIBLE'
                    AND v.display = 'Y'
                  ORDER BY v.student_roll_no ASC";
        
        $voters = $db->customQuery($query);

        echo json_encode([
            'success' => true,
            'count' => count($voters ?? []),
            'data' => $voters ?? []
        ]);
        exit;
    }

    /**
     * Get voting statistics
     */
    public function getVotingStats()
    {
        header('Content-Type: application/json');

        $db = new Database();
        $semester_id = (int)($_GET['semester_id'] ?? 0);

        if ($semester_id <= 0) {
            echo json_encode(['success' => false, 'message' => '❌ Invalid semester']);
            exit;
        }

        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN voting_status = 'ELIGIBLE' THEN 1 ELSE 0 END) as eligible,
                    SUM(CASE WHEN voting_status = 'NOT_ELIGIBLE' THEN 1 ELSE 0 END) as not_eligible,
                    SUM(CASE WHEN voting_status = 'SUSPENDED' THEN 1 ELSE 0 END) as suspended,
                    SUM(CASE WHEN has_voted = 'Y' THEN 1 ELSE 0 END) as voted,
                    SUM(CASE WHEN has_voted = 'N' AND voting_status = 'ELIGIBLE' THEN 1 ELSE 0 END) as pending
                  FROM voters_t
                  WHERE semester_id = $semester_id 
                    AND display = 'Y'";
        
        $stats = $db->customQuery($query);

        echo json_encode([
            'success' => true,
            'data' => $stats[0] ?? []
        ]);
        exit;
    }
}
?>
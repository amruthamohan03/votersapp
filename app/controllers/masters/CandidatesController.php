<?php

class CandidatesController extends Controller{
    public function index()
    {
        $db             = new Database();
        $candidates     = $db->selectData('candidates_t', '*', []);
        $positions      = $db->selectData('positions_master_t', 'id,position_name', ['display' => 'Y']);
        $departments    = $db->selectData('department_master_t', 'id,department_name', ['display' => 'Y']);
        $voters         = $db->selectData('voters_t', 'id,student_name,student_roll_no', ['display' => 'Y']);
        
        $data = [
            'title'         => 'Candidates',
            'candidates'    => $candidates,
            'positions'     => $positions,
            'departments'   => $departments,
            'voters'        => $voters
        ];
        $this->viewWithLayout('masters/candidates', $data);
    }

    public function crudData($action = 'insertion')
    {
        $db     = new Database();
        $table  = 'candidates_t';

        // Helper function to sanitize
        function sanitize($value)
        {
            return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }

        // 🔹 INSERTION (Add new candidate)
        if ($action === 'insertion' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'college_id'        => isset($_POST['college_id']) ? (int) $_POST['college_id'] : 1,
                'position_id'       => isset($_POST['position_id']) ? (int) $_POST['position_id'] : null,
                'voter_id'          => isset($_POST['voter_id']) ? (int) $_POST['voter_id'] : null,
                'candidate_name'    => sanitize($_POST['candidate_name'] ?? ''),
                'candidate_roll_no' => sanitize($_POST['candidate_roll_no'] ?? ''),
                'candidate_photo'   => isset($_POST['candidate_photo']) ? sanitize($_POST['candidate_photo']) : null,
                'bio'               => isset($_POST['bio']) ? sanitize($_POST['bio']) : null,
                'department_id'     => isset($_POST['department_id']) ? (int) $_POST['department_id'] : null,
                'nomination_date'   => isset($_POST['nomination_date']) && !empty($_POST['nomination_date']) ? $_POST['nomination_date'] : null,
                'display_order'     => isset($_POST['display_order']) ? (int) $_POST['display_order'] : 0,
                'display'           => isset($_POST['display']) && in_array($_POST['display'], ['Y','N']) ? $_POST['display'] : 'Y',
                'created_by'        => 1,
                'updated_by'        => 1,
            ];

            if (empty($data['candidate_name'])) {
                echo json_encode(['success'=>false,'message'=>'Candidate Name is required.']);
                exit;
            }

            if (empty($data['position_id'])) {
                echo json_encode(['success'=>false,'message'=>'Position is required.']);
                exit;
            }

            if (empty($data['voter_id'])) {
                echo json_encode(['success'=>false,'message'=>'Voter is required.']);
                exit;
            }

            $insertId = $db->insertData($table, $data);

            if ($insertId) {
                echo json_encode(['success'=>true,'message'=>'Candidate inserted successfully.','id'=>$insertId]);
            } else {
                echo json_encode(['success'=>false,'message'=>'Insert failed.']);
            }
            exit;
        }


        // 🔹 UPDATION (Edit existing candidate)
        elseif ($action === 'updation' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid Candidate ID']);
                exit;
            }

            $data = [
                'position_id'       => isset($_POST['position_id']) ? (int) $_POST['position_id'] : null,
                'voter_id'          => isset($_POST['voter_id']) ? (int) $_POST['voter_id'] : null,
                'candidate_name'    => sanitize($_POST['candidate_name'] ?? ''),
                'candidate_roll_no' => sanitize($_POST['candidate_roll_no'] ?? ''),
                'candidate_photo'   => isset($_POST['candidate_photo']) ? sanitize($_POST['candidate_photo']) : null,
                'bio'               => isset($_POST['bio']) ? sanitize($_POST['bio']) : null,
                'department_id'     => isset($_POST['department_id']) ? (int) $_POST['department_id'] : null,
                'nomination_date'   => isset($_POST['nomination_date']) && !empty($_POST['nomination_date']) ? $_POST['nomination_date'] : null,
                'display_order'     => isset($_POST['display_order']) ? (int) $_POST['display_order'] : 0,
                'display'           => isset($_POST['display']) && in_array($_POST['display'], ['Y','N']) ? $_POST['display'] : 'Y',
                'updated_by'        => 1
            ];

            if (empty($data['candidate_name'])) {
                echo json_encode(['success' => false, 'message' => 'Candidate Name is required']);
                exit;
            }

            $db = new Database();
            $update = $db->updateData('candidates_t', $data, ['id' => $id]);

            if($update){
                echo json_encode(['success'=>true,'message'=>'Candidate updated successfully']);
            } else {
                echo json_encode(['success'=>false,'message'=>'Update failed']);
            }
            exit;
        }


        // 🔹 DELETION (Delete by ID)
        elseif($action === 'deletion'){
            header('Content-Type: application/json');
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

            if($id <= 0){
                echo json_encode(['success'=>false,'message'=>'Invalid Candidate ID']);
                exit;
            }

            $delete = $db->deleteData('candidates_t',['id'=>$id]);
            if($delete){
                echo json_encode(['success'=>true,'message'=>'Candidate deleted successfully']);
            } else {
                echo json_encode(['success'=>false,'message'=>'Delete failed']);
            }
            exit;
        }
    }

    public function getCandidateById() {
        header('Content-Type: application/json');
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if($id <= 0){
            echo json_encode(['success'=>false,'message'=>'Invalid ID']);
            exit;
        }

        $db = new Database();
        $candidate = $db->selectData('candidates_t','*',['id'=>$id]);
        if(!empty($candidate)){
            echo json_encode(['success'=>true,'data'=>$candidate[0]]);
        } else {
            echo json_encode(['success'=>false,'message'=>'Candidate not found']);
        }
        exit;
    }

}
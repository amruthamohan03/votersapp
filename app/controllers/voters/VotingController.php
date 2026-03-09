<?php

class VotingController extends Controller
{
    /**
     * Ensure session is started before any operation
     */
    private function ensureSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        $this->ensureSession();
        $db = new Database();
        
        // Check if user is authenticated and get voter info
        $voterId = $_SESSION['voter_id'] ?? null;
        
        if (!$voterId) {
            header('Location: '.APP_URL.'voting/authenticate');
            exit;
        }

        // Get current voter details
        $voter = $db->selectData('voters_t', '*', ['id' => $voterId]);
        
        if (empty($voter) || $voter[0]['voting_status'] !== 'ELIGIBLE' || $voter[0]['has_voted'] === 'Y') {
            $data = [
                'title' => 'Voting Portal',
                'error' => true,
                'message' => 'You are not eligible to vote or have already voted.',
                'voter' => $voter[0] ?? null
            ];
            $this->viewWithLayout('voting/voting_error', $data);
            exit;
        }

        // Get all active positions
        $positions = $db->selectData('positions_master_t', '*', ['display' => 'Y'], 'id ASC');

        // Get all active candidates with their position info
        $candidatesQuery = "SELECT c.*, 
                                   p.position_name,
                                   p.id as position_id,
                                   v.student_name as voter_name,
                                   d.department_name
                            FROM candidates_t c
                            LEFT JOIN positions_master_t p ON c.position_id = p.id
                            LEFT JOIN voters_t v ON c.voter_id = v.id
                            LEFT JOIN department_master_t d ON c.department_id = d.id
                            WHERE c.display = 'Y' AND p.display = 'Y'
                            ORDER BY p.id ASC, c.display_order ASC";
        
        $candidates = $db->customQuery($candidatesQuery);

        // Get voter's already cast votes
        $alreadyVoted = $db->selectData('votes_t', 'position_id, candidate_id', ['voter_id' => $voterId]);

        $data = [
            'title' => 'Online Voting Portal',
            'voter' => $voter[0],
            'positions' => $positions,
            'candidates' => $candidates,
            'alreadyVoted' => $alreadyVoted,
            'voterId' => $voterId
        ];

        $this->viewWithLayout('voting/voting', $data);
    }

    /**
     * Authenticate voter - verify by roll number and verification code
     */
    public function authenticate()
    {
        $this->ensureSession();
        $db = new Database();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            header('Cache-Control: no-cache, no-store, must-revalidate');

            try {
                $rollNo = htmlspecialchars(trim($_POST['student_roll_no'] ?? ''), ENT_QUOTES, 'UTF-8');
                $verification = htmlspecialchars(trim($_POST['verification_code'] ?? ''), ENT_QUOTES, 'UTF-8');

                if (!$rollNo || !$verification) {
                    echo json_encode(['success' => false, 'message' => '⚠ Please enter roll number and verification code.']);
                    exit;
                }

                // Get voter by roll number
                $voter = $db->selectData('voters_t', 'id, student_name, student_roll_no, voting_status, has_voted, registration_no', 
                                         ['student_roll_no' => $rollNo, 'display' => 'Y']);

                if (empty($voter)) {
                    echo json_encode(['success' => false, 'message' => '❌ Roll number not found.']);
                    exit;
                }

                if ($voter[0]['voting_status'] !== 'ELIGIBLE') {
                    echo json_encode(['success' => false, 'message' => '❌ You are not eligible to vote.']);
                    exit;
                }

                if ($voter[0]['has_voted'] === 'Y') {
                    echo json_encode(['success' => false, 'message' => '⚠ You have already voted!']);
                    exit;
                }

                // Verify code (last 4 digits of registration number)
                $expectedCode = substr($voter[0]['registration_no'] ?? '0000', -4);

                if ($verification !== $expectedCode) {
                    echo json_encode(['success' => false, 'message' => '❌ Invalid verification code.']);
                    exit;
                }

                // Set session and allow voting
                $_SESSION['voter_id'] = $voter[0]['id'];
                $_SESSION['student_name'] = $voter[0]['student_name'];
                $_SESSION['student_roll_no'] = $voter[0]['student_roll_no'];

                echo json_encode([
                    'success' => true,
                    'message' => '✅ Authentication successful! Redirecting to voting page.',
                    'redirect' => 'voting'
                ]);
                exit;
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => '❌ Server error: ' . $e->getMessage()]);
                exit;
            }
        }

        $data = ['title' => 'Voter Authentication'];
        $this->viewWithLayout('voting/voter_login', $data);
    }

    /**
     * Cast a vote
     */
    public function castVote()
    {
        // CRITICAL: Set JSON header BEFORE any output
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        
        $this->ensureSession();
        
        $db = new Database();
        $voterId = $_SESSION['voter_id'] ?? null;

        try {
            if (!$voterId || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => '❌ Unauthorized access']);
                exit;
            }

            $candidateId = (int)($_POST['candidate_id'] ?? 0);
            $positionId = (int)($_POST['position_id'] ?? 0);

            if ($candidateId <= 0 || $positionId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => '⚠ Invalid candidate or position selected.']);
                exit;
            }

            // Verify voter is eligible
            $voter = $db->selectData('voters_t', '*', ['id' => $voterId]);
            if (empty($voter) || $voter[0]['voting_status'] !== 'ELIGIBLE' || $voter[0]['has_voted'] === 'Y') {
                echo json_encode(['success' => false, 'message' => '❌ You are not eligible to vote.']);
                exit;
            }

            // Check if voter has already voted for this position
            $existingVote = $db->selectData('votes_t', 'id', ['voter_id' => $voterId, 'position_id' => $positionId]);
            if (!empty($existingVote)) {
                echo json_encode(['success' => false, 'message' => '⚠ You have already voted for this position.']);
                exit;
            }

            // Verify candidate exists and is valid for this position
            $candidate = $db->selectData('candidates_t', '*', 
                                        ['id' => $candidateId, 'position_id' => $positionId, 'display' => 'Y']);
            if (empty($candidate)) {
                echo json_encode(['success' => false, 'message' => '❌ Invalid candidate selected.']);
                exit;
            }

            // Record the vote
            $voteData = [
                'college_id' => 1,
                'voter_id' => $voterId,
                'position_id' => $positionId,
                'candidate_id' => $candidateId,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                'device_info' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255),
                'is_valid' => 'Y'
            ];

            $insertVoteId = $db->insertData('votes_t', $voteData);

            if (!$insertVoteId) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => '❌ Failed to record vote. Please try again.']);
                exit;
            }

            // Recalculate and update voting results
            $this->updateVotingResults($positionId);

            echo json_encode([
                'success' => true,
                'message' => '✅ Vote recorded successfully!',
                'voteId' => $insertVoteId,
                'candidateName' => $candidate[0]['candidate_name'],
                'positionName' => $positionId
            ]);
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => '❌ Error: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Submit all votes and mark voter as voted
     */
    public function submitVotes()
    {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        
        $this->ensureSession();
        
        $db = new Database();
        $voterId = $_SESSION['voter_id'] ?? null;

        try {
            if (!$voterId || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => '❌ Unauthorized access']);
                exit;
            }

            // Get all votes cast by this voter
            $votes = $db->selectData('votes_t', 'position_id', ['voter_id' => $voterId]);

            // if (empty($votes)) {
            //     http_response_code(400);
            //     echo json_encode(['success' => false, 'message' => '⚠ You must cast at least one vote.']);
            //     exit;
            // }

            // Update voter as voted
            $updateVoter = $db->updateData('voters_t', 
                [
                    'has_voted' => 'Y',
                    'voted_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $voterId
                ],
                ['id' => $voterId]
            );

            if (!$updateVoter) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => '❌ Failed to finalize voting. Please contact support.']);
                exit;
            }

            // Clear session
            unset($_SESSION['voter_id']);
            unset($_SESSION['student_name']);
            unset($_SESSION['student_roll_no']);

            echo json_encode([
                'success' => true,
                'message' => '✅ Voting completed successfully! Thank you for voting.',
                'voteCount' => count($votes),
                'redirect' => APP_URL.'voting/thank-you'
            ]);
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => '❌ Error: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Change vote for a position
     */
    public function changeVote()
    {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        
        $this->ensureSession();
        
        $db = new Database();
        $voterId = $_SESSION['voter_id'] ?? null;
        $positionId = (int)($_POST['position_id'] ?? 0);
        $newCandidateId = (int)($_POST['candidate_id'] ?? 0);

        try {
            if (!$voterId || $positionId <= 0 || $newCandidateId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => '❌ Invalid request']);
                exit;
            }

            // Check if voter has voted for this position
            $existingVote = $db->selectData('votes_t', 'id', 
                                           ['voter_id' => $voterId, 'position_id' => $positionId]);
            
            if (empty($existingVote)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => '⚠ No previous vote found for this position.']);
                exit;
            }

            // Update the vote
            $updateVote = $db->updateData('votes_t',
                ['candidate_id' => $newCandidateId],
                ['voter_id' => $voterId, 'position_id' => $positionId]
            );

            if (!$updateVote) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => '❌ Failed to update vote.']);
                exit;
            }

            // Recalculate voting results
            $this->updateVotingResults($positionId);

            echo json_encode(['success' => true, 'message' => '✅ Vote changed successfully!']);
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => '❌ Error: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Get voting results in real-time
     */
    public function getResults()
    {
        header('Content-Type: application/json');
        
        $db = new Database();
        $positionId = (int)($_GET['position_id'] ?? 0);

        try {
            if ($positionId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => '❌ Invalid position']);
                exit;
            }

            $query = "SELECT 
                        c.id,
                        c.candidate_name,
                        c.candidate_roll_no,
                        p.position_name,
                        COUNT(v.id) as vote_count,
                        ROUND((COUNT(v.id) * 100.0 / (SELECT COUNT(*) FROM votes_t WHERE position_id = $positionId)), 2) as vote_percentage
                      FROM candidates_t c
                      LEFT JOIN positions_master_t p ON c.position_id = p.id
                      LEFT JOIN votes_t v ON c.id = v.candidate_id AND v.position_id = $positionId AND v.is_valid = 'Y'
                      WHERE c.position_id = $positionId AND c.display = 'Y'
                      GROUP BY c.id, c.candidate_name, c.candidate_roll_no, p.position_name
                      ORDER BY vote_count DESC";

            $results = $db->customQuery($query);

            echo json_encode([
                'success' => true,
                'data' => $results ?? [],
                'totalVotes' => array_sum(array_column($results ?? [], 'vote_count'))
            ]);
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => '❌ Error: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Get voter statistics
     */
    public function getVotingStats()
    {
        header('Content-Type: application/json');
        
        $db = new Database();

        try {
            $query = "SELECT 
                        COUNT(DISTINCT voter_id) as total_voters_voted,
                        COUNT(*) as total_votes_cast,
                        COUNT(DISTINCT position_id) as positions,
                        COUNT(DISTINCT candidate_id) as total_candidates
                      FROM votes_t
                      WHERE is_valid = 'Y'";

            $stats = $db->customQuery($query);

            $eligibleVoters = $db->selectData('voters_t', 'id', 
                                             ['voting_status' => 'ELIGIBLE', 'display' => 'Y']);
            $votedVoters = $db->selectData('voters_t', 'id', 
                                           ['has_voted' => 'Y', 'display' => 'Y']);

            echo json_encode([
                'success' => true,
                'data' => [
                    'total_eligible_voters' => count($eligibleVoters ?? []),
                    'total_voters_voted' => (int)($stats[0]['total_voters_voted'] ?? 0),
                    'total_votes_cast' => (int)($stats[0]['total_votes_cast'] ?? 0),
                    'total_positions' => (int)($stats[0]['positions'] ?? 0),
                    'voter_turnout' => count($eligibleVoters ?? []) > 0 ? round((count($votedVoters ?? []) / count($eligibleVoters ?? [])) * 100, 2) . '%' : '0%'
                ]
            ]);
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => '❌ Error: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Thank you page after voting
     */
    public function thankYou()
    {
        $data = ['title' => 'Thank You'];
        $this->viewWithLayout('voting/thank_you', $data);
    }

    /**
     * Update voting results table
     */
    private function updateVotingResults($positionId)
    {
        $db = new Database();

        $candidates = $db->selectData('candidates_t', 'id', ['position_id' => $positionId, 'display' => 'Y']);

        $totalVotesQuery = "SELECT COUNT(*) as total FROM votes_t WHERE position_id = $positionId AND is_valid = 'Y'";
        $totalVotes = $db->customQuery($totalVotesQuery);
        $total = (int)($totalVotes[0]['total'] ?? 1);

        foreach ($candidates as $candidate) {
            $candidateId = $candidate['id'];

            $voteCountQuery = "SELECT COUNT(*) as count FROM votes_t 
                              WHERE position_id = $positionId AND candidate_id = $candidateId AND is_valid = 'Y'";
            $voteCount = $db->customQuery($voteCountQuery);
            $votes = (int)($voteCount[0]['count'] ?? 0);
            $percentage = ($total > 0) ? round(($votes / $total) * 100, 2) : 0;

            $existing = $db->selectData('voting_results_t', 'id', 
                                       ['position_id' => $positionId, 'candidate_id' => $candidateId]);

            if (!empty($existing)) {
                $db->updateData('voting_results_t',
                    [
                        'total_votes' => $votes,
                        'vote_percentage' => $percentage
                    ],
                    ['position_id' => $positionId, 'candidate_id' => $candidateId]
                );
            } else {
                $db->insertData('voting_results_t', [
                    'position_id' => $positionId,
                    'candidate_id' => $candidateId,
                    'total_votes' => $votes,
                    'vote_percentage' => $percentage
                ]);
            }
        }
    }

    /**
     * Export voting results to PDF
     */
    public function exportResults()
    {
        $db = new Database();

        $query = "SELECT 
                    p.position_name,
                    c.candidate_name,
                    c.candidate_roll_no,
                    v.student_name as voter_name,
                    COUNT(vo.id) as total_votes
                  FROM positions_master_t p
                  LEFT JOIN candidates_t c ON p.id = c.position_id
                  LEFT JOIN voters_t v ON c.voter_id = v.id
                  LEFT JOIN votes_t vo ON c.id = vo.candidate_id AND vo.position_id = p.id
                  WHERE p.display = 'Y' AND c.display = 'Y'
                  GROUP BY p.id, c.id
                  ORDER BY p.id, total_votes DESC";

        $results = $db->customQuery($query);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $results ?? []]);
        exit;
    }
}
?>
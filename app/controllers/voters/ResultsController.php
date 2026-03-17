<?php

class ResultsController extends Controller
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

    /**
     * Display voting results page with all statistics and charts
     */
    public function index()
    {
        $this->ensureSession();
        $db = new Database();

        try {
            // Get all positions
            $positions = $db->selectData('positions_master_t', '*', ['display' => 'Y'], 'id ASC');

            // Get overall voting statistics
            $statsQuery = "SELECT 
                            COUNT(DISTINCT voter_id) as total_voters_voted,
                            COUNT(*) as total_votes_cast,
                            COUNT(DISTINCT position_id) as positions_count,
                            COUNT(DISTINCT candidate_id) as total_candidates
                          FROM votes_t
                          WHERE is_valid = 'Y'";
            
            $statsResult = $db->customQuery($statsQuery);
            $stats = $statsResult[0] ?? [];

            // Get eligible voters
            $eligibleVoters = $db->selectData('voters_t', 'id', 
                                             ['voting_status' => 'ELIGIBLE', 'display' => 'Y']);
            $totalEligible = count($eligibleVoters ?? []);

            // Calculate turnout
            $votersTurnout = (int)($stats['total_voters_voted'] ?? 0);
            $turnoutPercentage = $totalEligible > 0 ? round(($votersTurnout / $totalEligible) * 100, 2) : 0;

            // Get results for each position
            $positionResults = [];
            foreach ($positions as $position) {
                $positionId = $position['id'];
                
                // Get candidates with vote counts for this position
                $query = "SELECT 
                            c.id,
                            c.candidate_name,
                            c.candidate_roll_no,
                            c.candidate_photo,
                            c.department_id,
                            d.department_name,
                            COUNT(v.id) as vote_count,
                            ROUND((COUNT(v.id) * 100.0 / NULLIF((SELECT COUNT(*) FROM votes_t 
                                    WHERE position_id = $positionId AND is_valid = 'Y'), 0)), 2) as vote_percentage
                          FROM candidates_t c
                          LEFT JOIN department_master_t d ON c.department_id = d.id
                          LEFT JOIN votes_t v ON c.id = v.candidate_id AND v.position_id = $positionId AND v.is_valid = 'Y'
                          WHERE c.position_id = $positionId AND c.display = 'Y'
                          GROUP BY c.id, c.candidate_name, c.candidate_roll_no, c.candidate_photo, 
                                   c.department_id, d.department_name
                          ORDER BY vote_count DESC";
                
                $candidates = $db->customQuery($query);
                
                $positionResults[$positionId] = [
                    'position' => $position,
                    'candidates' => $candidates ?? [],
                    'total_votes' => array_sum(array_column($candidates ?? [], 'vote_count')),
                    'winner' => isset($candidates[0]) ? $candidates[0] : null
                ];
            }

            // Get department-wise vote distribution
            $deptQuery = "SELECT 
                            d.department_name,
                            COUNT(v.id) as vote_count
                          FROM votes_t v
                          LEFT JOIN candidates_t c ON v.candidate_id = c.id
                          LEFT JOIN department_master_t d ON c.department_id = d.id
                          WHERE v.is_valid = 'Y'
                          GROUP BY d.department_name
                          ORDER BY vote_count DESC";
            
            $deptDistribution = $db->customQuery($deptQuery) ?? [];

            // Get hourly voting trend (if voting system is running for multiple hours)
            $trendQuery = "SELECT 
                            DATE_FORMAT(created_at, '%Y-%m-%d %H:00') as vote_hour,
                            COUNT(*) as vote_count
                          FROM votes_t
                          WHERE is_valid = 'Y'
                          GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d %H:00')
                          ORDER BY vote_hour ASC";
            
            $votingTrend = $db->customQuery($trendQuery) ?? [];

            $data = [
                'title' => 'Voting Results & Statistics',
                'stats' => $stats,
                'totalEligible' => $totalEligible,
                'votersTurnout' => $votersTurnout,
                'turnoutPercentage' => $turnoutPercentage,
                'positions' => $positions,
                'positionResults' => $positionResults,
                'deptDistribution' => $deptDistribution,
                'votingTrend' => $votingTrend
            ];

            $this->viewWithLayout('voting/results', $data);

        } catch (Exception $e) {
            $data = [
                'title' => 'Error',
                'error' => true,
                'message' => 'Error loading results: ' . $e->getMessage()
            ];
            $this->viewWithLayout('voting/results_error', $data);
        }
    }

    /**
     * Get real-time results for a specific position (AJAX)
     */
    public function getPositionResults()
    {
        header('Content-Type: application/json');
        
        $this->ensureSession();
        $db = new Database();
        $positionId = (int)($_GET['position_id'] ?? 0);

        try {
            if ($positionId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => '❌ Invalid position']);
                exit;
            }

            // Get position info
            $position = $db->selectData('positions_master_t', '*', ['id' => $positionId]);
            
            if (empty($position)) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => '❌ Position not found']);
                exit;
            }

            // Get candidates with vote counts
            $query = "SELECT 
                        c.id,
                        c.candidate_name,
                        c.candidate_roll_no,
                        c.candidate_photo,
                        d.department_name,
                        COUNT(v.id) as vote_count,
                        ROUND((COUNT(v.id) * 100.0 / NULLIF((SELECT COUNT(*) FROM votes_t 
                                WHERE position_id = $positionId AND is_valid = 'Y'), 0)), 2) as vote_percentage
                      FROM candidates_t c
                      LEFT JOIN department_master_t d ON c.department_id = d.id
                      LEFT JOIN votes_t v ON c.id = v.candidate_id AND v.position_id = $positionId AND v.is_valid = 'Y'
                      WHERE c.position_id = $positionId AND c.display = 'Y'
                      GROUP BY c.id, c.candidate_name, c.candidate_roll_no, c.candidate_photo, d.department_name
                      ORDER BY vote_count DESC";
            
            $candidates = $db->customQuery($query);
            $totalVotes = array_sum(array_column($candidates ?? [], 'vote_count'));

            echo json_encode([
                'success' => true,
                'position' => $position[0],
                'candidates' => $candidates ?? [],
                'total_votes' => $totalVotes,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => '❌ Error: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Get voting statistics (AJAX)
     */
    public function getStats()
    {
        header('Content-Type: application/json');
        
        $db = new Database();

        try {
            // Overall statistics
            $statsQuery = "SELECT 
                            COUNT(DISTINCT voter_id) as total_voters_voted,
                            COUNT(*) as total_votes_cast,
                            COUNT(DISTINCT position_id) as positions_count,
                            COUNT(DISTINCT candidate_id) as total_candidates
                          FROM votes_t
                          WHERE is_valid = 'Y'";
            
            $stats = $db->customQuery($statsQuery)[0] ?? [];

            // Eligible voters
            $eligibleVoters = $db->selectData('voters_t', 'id', 
                                             ['voting_status' => 'ELIGIBLE', 'display' => 'Y']);
            $totalEligible = count($eligibleVoters ?? []);

            // Turnout percentage
            $votersTurnout = (int)($stats['total_voters_voted'] ?? 0);
            $turnoutPercentage = $totalEligible > 0 ? round(($votersTurnout / $totalEligible) * 100, 2) : 0;

            echo json_encode([
                'success' => true,
                'total_eligible_voters' => $totalEligible,
                'total_voters_voted' => $votersTurnout,
                'total_votes_cast' => (int)($stats['total_votes_cast'] ?? 0),
                'total_positions' => (int)($stats['positions_count'] ?? 0),
                'voter_turnout_percentage' => $turnoutPercentage,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => '❌ Error: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Export results to PDF using mPDF
     * REQUIRES: composer require mpdf/mpdf
     */
    public function exportPDF()
    {
        $this->ensureSession();
        $db = new Database();

        try {
            // Check if mPDF is available
            if (!class_exists('Mpdf\Mpdf')) {
                throw new Exception('mPDF library not installed. Run: composer require mpdf/mpdf');
            }

            // Get all results data
            $positions = $db->selectData('positions_master_t', '*', ['display' => 'Y'], 'id ASC');

            $statsQuery = "SELECT 
                            COUNT(DISTINCT voter_id) as total_voters_voted,
                            COUNT(*) as total_votes_cast,
                            COUNT(DISTINCT position_id) as positions_count,
                            COUNT(DISTINCT candidate_id) as total_candidates
                          FROM votes_t
                          WHERE is_valid = 'Y'";
            
            $statsResult = $db->customQuery($statsQuery);
            $stats = $statsResult[0] ?? [];

            $eligibleVoters = $db->selectData('voters_t', 'id', 
                                             ['voting_status' => 'ELIGIBLE', 'display' => 'Y']);
            $totalEligible = count($eligibleVoters ?? []);

            $votersTurnout = (int)($stats['total_voters_voted'] ?? 0);
            $turnoutPercentage = $totalEligible > 0 ? round(($votersTurnout / $totalEligible) * 100, 2) : 0;

            // Get detailed results for each position
            $positionResults = [];
            foreach ($positions as $position) {
                $positionId = $position['id'];
                
                $query = "SELECT 
                            c.id,
                            c.candidate_name,
                            c.candidate_roll_no,
                            d.department_name,
                            COUNT(v.id) as vote_count,
                            ROUND((COUNT(v.id) * 100.0 / NULLIF((SELECT COUNT(*) FROM votes_t 
                                    WHERE position_id = $positionId AND is_valid = 'Y'), 0)), 2) as vote_percentage
                          FROM candidates_t c
                          LEFT JOIN department_master_t d ON c.department_id = d.id
                          LEFT JOIN votes_t v ON c.id = v.candidate_id AND v.position_id = $positionId AND v.is_valid = 'Y'
                          WHERE c.position_id = $positionId AND c.display = 'Y'
                          GROUP BY c.id, c.candidate_name, c.candidate_roll_no, d.department_name
                          ORDER BY vote_count DESC";
                
                $candidates = $db->customQuery($query);
                
                $positionResults[$positionId] = [
                    'position' => $position,
                    'candidates' => $candidates ?? [],
                    'total_votes' => array_sum(array_column($candidates ?? [], 'vote_count'))
                ];
            }

            // Generate PDF content
            $html = $this->generatePDFContent($stats, $totalEligible, $votersTurnout, $turnoutPercentage, $positionResults);

            // Create PDF
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
                'autoScriptToLang' => true,
                'autoLangToScript' => true,
            ]);

            // Set document properties
            $mpdf->SetTitle('Voting Results Report');
            $mpdf->SetAuthor('Government Polytechnic College Nedumkandam');
            $mpdf->SetSubject('Online Voting System Results');
            $mpdf->SetKeywords('Voting, Results, Report');

            // Add watermark
            $mpdf->SetWatermarkText('VOTING RESULTS - ' . date('Y-m-d'));
            $mpdf->showWatermarkText = true;

            // Write HTML content
            $mpdf->WriteHTML($html);

            // Output PDF
            $filename = 'voting_results_' . date('Y-m-d_H-i-s') . '.pdf';
            $mpdf->Output($filename, 'D');
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => '❌ PDF Error: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Generate HTML content for PDF
     */
    private function generatePDFContent($stats, $totalEligible, $votersTurnout, $turnoutPercentage, $positionResults)
    {
        $html = '
        <html>
        <head>
            <meta charset="UTF-8" />
            <style>
                body {
                    font-family: Arial, sans-serif;
                    color: #333;
                    line-height: 1.6;
                }
                .header {
                    text-align: center;
                    border-bottom: 3px solid #333;
                    padding-bottom: 15px;
                    margin-bottom: 20px;
                }
                .header h1 {
                    margin: 0;
                    color: #0d6efd;
                    font-size: 24px;
                }
                .header .date {
                    font-size: 12px;
                    color: #666;
                }
                .stats-container {
                    display: table;
                    width: 100%;
                    margin-bottom: 20px;
                    border-collapse: collapse;
                }
                .stat-card {
                    display: table-cell;
                    width: 25%;
                    border: 1px solid #ddd;
                    padding: 12px;
                    text-align: center;
                    background-color: #f8f9fa;
                }
                .stat-card .number {
                    font-size: 24px;
                    font-weight: bold;
                    color: #0d6efd;
                }
                .stat-card .label {
                    font-size: 12px;
                    color: #666;
                }
                .position-section {
                    margin-bottom: 25px;
                    page-break-inside: avoid;
                }
                .position-header {
                    background-color: #0d6efd;
                    color: white;
                    padding: 10px;
                    font-size: 16px;
                    font-weight: bold;
                    margin-bottom: 10px;
                }
                .position-stats {
                    margin-bottom: 10px;
                    padding: 8px;
                    background-color: #f0f0f0;
                    border-left: 4px solid #0d6efd;
                }
                .candidates-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }
                .candidates-table th {
                    background-color: #e9ecef;
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                    font-weight: bold;
                    font-size: 12px;
                }
                .candidates-table td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    font-size: 11px;
                }
                .candidates-table tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                .rank-1 {
                    background-color: #fff3cd !important;
                    font-weight: bold;
                }
                .rank {
                    text-align: center;
                    font-weight: bold;
                    width: 30px;
                }
                .votes {
                    text-align: center;
                    font-weight: bold;
                }
                .percentage {
                    text-align: center;
                }
                .footer {
                    margin-top: 30px;
                    padding-top: 15px;
                    border-top: 1px solid #ddd;
                    font-size: 10px;
                    color: #666;
                    text-align: center;
                }
                .badge {
                    display: inline-block;
                    padding: 3px 6px;
                    border-radius: 3px;
                    font-size: 10px;
                    font-weight: bold;
                }
                .badge-gold {
                    background-color: #ffc107;
                    color: #000;
                }
                .badge-silver {
                    background-color: #c0c0c0;
                }
                .badge-bronze {
                    background-color: #cd7f32;
                    color: white;
                }
            </style>
        </head>
        <body>
            <!-- HEADER -->
            <div class="header">
                <h1>🗳️ VOTING RESULTS REPORT</h1>
                <div class="date">Government Polytechnic College Nedumkandam</div>
                <div class="date">Generated on: ' . date('Y-m-d H:i:s') . '</div>
            </div>

            <!-- STATISTICS -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="number">' . ($stats['total_votes_cast'] ?? 0) . '</div>
                    <div class="label">Total Votes Cast</div>
                </div>
                <div class="stat-card">
                    <div class="number">' . $turnoutPercentage . '%</div>
                    <div class="label">Voter Turnout</div>
                </div>
                <div class="stat-card">
                    <div class="number">' . $votersTurnout . '/' . $totalEligible . '</div>
                    <div class="label">Voters Participated</div>
                </div>
                <div class="stat-card">
                    <div class="number">' . count($positionResults) . '</div>
                    <div class="label">Positions</div>
                </div>
            </div>

            <!-- POSITION RESULTS -->
        ';

        foreach ($positionResults as $positionId => $result) {
            $html .= '
            <div class="position-section">
                <div class="position-header">
                    ' . htmlspecialchars($result['position']['position_name']) . '
                </div>
                <div class="position-stats">
                    <strong>Total Votes for this position:</strong> ' . $result['total_votes'] . '
                    <br><small>' . htmlspecialchars($result['position']['description'] ?? '') . '</small>
                </div>
                
                <table class="candidates-table">
                    <thead>
                        <tr>
                            <th class="rank">Rank</th>
                            <th>Candidate Name</th>
                            <th>Roll No</th>
                            <th>Department</th>
                            <th class="votes">Votes</th>
                            <th class="percentage">%</th>
                        </tr>
                    </thead>
                    <tbody>
            ';

            $rank = 1;
            foreach ($result['candidates'] as $candidate) {
                $rowClass = ($rank === 1) ? ' rank-1' : '';
                $rankBadge = '';
                
                if ($rank === 1) {
                    $rankBadge = '<span class="badge badge-gold">1st</span>';
                } elseif ($rank === 2) {
                    $rankBadge = '<span class="badge badge-silver">2nd</span>';
                } elseif ($rank === 3) {
                    $rankBadge = '<span class="badge badge-bronze">3rd</span>';
                } else {
                    $rankBadge = '<strong>' . $rank . '</strong>';
                }

                $html .= '
                        <tr' . $rowClass . '>
                            <td class="rank">' . $rankBadge . '</td>
                            <td><strong>' . htmlspecialchars($candidate['candidate_name']) . '</strong></td>
                            <td>' . htmlspecialchars($candidate['candidate_roll_no'] ?? 'N/A') . '</td>
                            <td>' . htmlspecialchars($candidate['department_name'] ?? 'N/A') . '</td>
                            <td class="votes">' . $candidate['vote_count'] . '</td>
                            <td class="percentage"><strong>' . ($candidate['vote_percentage'] ?? 0) . '%</strong></td>
                        </tr>
                ';
                $rank++;
            }

            $html .= '
                    </tbody>
                </table>
            </div>
            ';
        }

        $html .= '
            <!-- FOOTER -->
            <div class="footer">
                <p>This is an official voting results report generated by the Online Voting System.</p>
                <p>For more information, contact the college administration.</p>
                <p>Report generated: ' . date('Y-m-d H:i:s') . '</p>
            </div>

        </body>
        </html>
        ';

        return $html;
    }

    /**
     * Export results to Excel/CSV
     */
    public function exportCSV()
    {
        $this->ensureSession();
        $db = new Database();

        try {
            $query = "SELECT 
                        p.position_name,
                        c.candidate_name,
                        c.candidate_roll_no,
                        d.department_name,
                        COUNT(v.id) as total_votes,
                        ROUND((COUNT(v.id) * 100.0 / NULLIF((SELECT COUNT(*) FROM votes_t 
                                WHERE position_id = p.id AND is_valid = 'Y'), 0)), 2) as vote_percentage
                      FROM positions_master_t p
                      LEFT JOIN candidates_t c ON p.id = c.position_id AND c.display = 'Y'
                      LEFT JOIN department_master_t d ON c.department_id = d.id
                      LEFT JOIN votes_t v ON c.id = v.candidate_id AND v.position_id = p.id AND v.is_valid = 'Y'
                      WHERE p.display = 'Y'
                      GROUP BY p.id, p.position_name, c.id, c.candidate_name, c.candidate_roll_no, d.department_name
                      ORDER BY p.id, total_votes DESC";

            $results = $db->customQuery($query);

            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="voting_results_' . date('Y-m-d_H-i-s') . '.csv"');

            $output = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($output, ['Position', 'Candidate Name', 'Candidate Roll No', 'Department', 'Total Votes', 'Vote Percentage']);

            // Add data rows
            foreach ($results as $row) {
                fputcsv($output, [
                    $row['position_name'],
                    $row['candidate_name'],
                    $row['candidate_roll_no'],
                    $row['department_name'],
                    $row['total_votes'],
                    $row['vote_percentage'] . '%'
                ]);
            }

            fclose($output);
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => '❌ Error: ' . $e->getMessage()]);
            exit;
        }
    }
}
?>
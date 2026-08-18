<?php

/**
 * ============================================================================
 * Match Controller
 * ============================================================================
 * 
 * Handles the intelligent matching system.
 * Compares skills offered, skills wanted, category, and experience
 * to calculate match scores and recommend users.
 * 
 * NO AI. NO Machine Learning. NO Graph Algorithms.
 * Simple PHP comparison logic only.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class MatchController
{
    /**
     * Match model instance
     * @var MatchModel
     */
    private MatchModel $matchModel;

    /**
     * Skill model instance
     * @var Skill
     */
    private Skill $skillModel;

    /**
     * Profile model instance
     * @var Profile
     */
    private Profile $profileModel;

    /**
     * Notification model instance
     * @var Notification
     */
    private Notification $notificationModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->matchModel = new MatchModel();
        $this->skillModel = new Skill();
        $this->profileModel = new Profile();
        $this->notificationModel = new Notification();
    }

    /**
     * =========================================================================
     * LIST MATCHES
     * =========================================================================
     * 
     * Display all matches for the logged-in user.
     */
    public function index(): void
    {
        $userId = getCurrentUserId();

        $status = isset($_GET['status']) && is_string($_GET['status'])
            ? $_GET['status']
            : '';
        $page = (int) ($_GET['page'] ?? 1);
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        // Get user's matches
        $matches = $this->matchModel->getUserMatches($userId, $status, $limit, $offset);

        // Get total count
        $total = $this->matchModel->countUserMatches($userId, $status);

        $totalPages = (int)ceil($total / $limit);

        setPageTitle('My Matches');
        require_once BASE_PATH . '/views/matches/index.php';
    }

    /**
     * =========================================================================
     * FIND MATCHES (INTELLIGENT MATCHING)
     * =========================================================================
     * 
     * Run the matching algorithm to find compatible users.
     * Compares:
     * - User A's offered skills vs User B's wanted skills
     * - User B's offered skills vs User A's wanted skills
     * - Category overlap
     * - Experience level compatibility
     */
    public function find(): void
    {
        $userId = getCurrentUserId();

        // Get current user's data
        $mySkills = $this->skillModel->getUserSkills($userId);
        $myWanted = $this->skillModel->getWantedSkills($userId);
        $myProfile = $this->profileModel->getByUserId($userId);

        // Get all other active users
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT id, full_name 
            FROM users 
            WHERE id != :user_id AND is_active = 1 AND role = 'user'
        ");
        $stmt->execute([':user_id' => $userId]);
        $otherUsers = $stmt->fetchAll();

        $newMatches = 0;

        foreach ($otherUsers as $otherUser) {
            $otherId = $otherUser['id'];

            // Skip if match already exists and is not declined
            $existingMatch = $this->matchModel->getMatch($userId, $otherId);
            if ($existingMatch && $existingMatch['status'] !== 'declined') {
                continue;
            }

            // Get other user's data
            $otherSkills = $this->skillModel->getUserSkills($otherId);
            $otherWanted = $this->skillModel->getWantedSkills($otherId);
            $otherProfile = $this->profileModel->getByUserId($otherId);

            // Calculate match score
            $scoreData = $this->calculateMatchScore(
                $mySkills,
                $myWanted,
                $myProfile,
                $otherSkills,
                $otherWanted,
                $otherProfile
            );

            $score = $scoreData['score'];

            // Only save if score meets threshold
            if ($score >= MATCH_THRESHOLD) {
                $matchId = $this->matchModel->save(
                    $userId,
                    $otherId,
                    $score,
                    $scoreData['matched_skill_id'],
                    $scoreData['notes']
                );

                if ($matchId && !$existingMatch) {
                    $newMatches++;

                    // Notify both users
                    $this->notificationModel->notifyMatch(
                        $userId,
                        $matchId,
                        $otherUser['full_name'],
                        $score
                    );

                    $this->notificationModel->notifyMatch(
                        $otherId,
                        $matchId,
                        $myProfile['full_name'],
                        $score
                    );
                }
            }
        }

        if ($newMatches > 0) {
            flash("Found {$newMatches} new match" . ($newMatches > 1 ? 'es' : '') . "!", 'success');
        } else {
            flash('No new matches found at this time. Try adding more skills!', 'info');
        }

        redirect(url('Match', 'index'));
    }

    /**
     * =========================================================================
     * CALCULATE MATCH SCORE
     * =========================================================================
     * 
     * Simple scoring algorithm:
     * - Skill match (User A offers what User B wants): 40 points max
     * - Reverse skill match (User B offers what User A wants): 40 points max
     * - Category match: 10 points max
     * - Experience compatibility: 10 points max
     * - Total: 100 points
     * 
     * @param array $mySkills      Current user's offered skills
     * @param array $myWanted      Current user's wanted skills
     * @param array $myProfile     Current user's profile
     * @param array $otherSkills   Other user's offered skills
     * @param array $otherWanted   Other user's wanted skills
     * @param array $otherProfile  Other user's profile
     * @return array               ['score' => int, 'matched_skill_id' => int, 'notes' => string]
     */
    private function calculateMatchScore(
        array $mySkills,
        array $myWanted,
        array $myProfile,
        array $otherSkills,
        array $otherWanted,
        array $otherProfile
    ): array {
        $score = 0;
        $notes = [];
        $matchedSkillId = 0;

        // === SCORE 1: My offered skills match their wanted skills (40 points max) ===
        $forwardMatches = [];
        foreach ($mySkills as $mySkill) {
            foreach ($otherWanted as $theirWant) {
                if ($mySkill['skill_id'] == $theirWant['skill_id']) {
                    // Base 25 points for skill match
                    $points = 25;

                    // Bonus for experience compatibility (0-15 points)
                    $expLevels = ['Beginner' => 1, 'Intermediate' => 2, 'Advanced' => 3, 'Expert' => 4];
                    $myExp = $expLevels[$mySkill['experience_level']] ?? 1;
                    $theirExp = $expLevels[$theirWant['experience_level']] ?? 1;

                    // Higher score if I have MORE experience than they want
                    $expDiff = $myExp - $theirExp;
                    if ($expDiff >= 2) {
                        $points += 15; // I exceed their requirement significantly
                    } elseif ($expDiff >= 0) {
                        $points += 10; // I meet or slightly exceed
                    } elseif ($expDiff >= -1) {
                        $points += 5;  // Close enough
                    }

                    $forwardMatches[] = [
                        'skill_name' => $mySkill['skill_name'],
                        'points' => $points
                    ];

                    if ($matchedSkillId === 0) {
                        $matchedSkillId = $mySkill['skill_id'];
                    }
                }
            }
        }

        // Take highest forward match score (max 40)
        $forwardScore = 0;
        foreach ($forwardMatches as $match) {
            $forwardScore = max($forwardScore, $match['points']);
        }
        $score += min($forwardScore, 40);

        if (!empty($forwardMatches)) {
            $bestForward = $forwardMatches[0];
            $notes[] = "You offer " . $bestForward['skill_name'] . " which they want to learn";
        }

        // === SCORE 2: Their offered skills match my wanted skills (40 points max) ===
        $reverseMatches = [];
        foreach ($otherSkills as $theirSkill) {
            foreach ($myWanted as $myWant) {
                if ($theirSkill['skill_id'] == $myWant['skill_id']) {
                    // Base 25 points for skill match
                    $points = 25;

                    // Bonus for experience compatibility (0-15 points)
                    $expLevels = ['Beginner' => 1, 'Intermediate' => 2, 'Advanced' => 3, 'Expert' => 4];
                    $theirExp = $expLevels[$theirSkill['experience_level']] ?? 1;
                    $myExp = $expLevels[$myWant['experience_level']] ?? 1;

                    $expDiff = $theirExp - $myExp;
                    if ($expDiff >= 2) {
                        $points += 15;
                    } elseif ($expDiff >= 0) {
                        $points += 10;
                    } elseif ($expDiff >= -1) {
                        $points += 5;
                    }

                    $reverseMatches[] = [
                        'skill_name' => $theirSkill['skill_name'],
                        'points' => $points
                    ];

                    if ($matchedSkillId === 0) {
                        $matchedSkillId = $theirSkill['skill_id'];
                    }
                }
            }
        }

        // Take highest reverse match score (max 40)
        $reverseScore = 0;
        foreach ($reverseMatches as $match) {
            $reverseScore = max($reverseScore, $match['points']);
        }
        $score += min($reverseScore, 40);

        if (!empty($reverseMatches)) {
            $bestReverse = $reverseMatches[0];
            $notes[] = "They offer " . $bestReverse['skill_name'] . " which you want to learn";
        }

        // === SCORE 3: Category overlap (10 points max) ===
        $myCategories = [];
        foreach ($mySkills as $skill) {
            $myCategories[$skill['category_id']] = true;
        }
        foreach ($myWanted as $want) {
            $myCategories[$want['category_id']] = true;
        }

        $otherCategories = [];
        foreach ($otherSkills as $skill) {
            $otherCategories[$skill['category_id']] = true;
        }
        foreach ($otherWanted as $want) {
            $otherCategories[$want['category_id']] = true;
        }

        $commonCategories = array_intersect_key($myCategories, $otherCategories);
        $categoryScore = min(count($commonCategories) * 3, 10);
        $score += $categoryScore;

        if (!empty($commonCategories)) {
            $notes[] = "You share " . count($commonCategories) . " common interest area(s)";
        }

        // === SCORE 4: Experience level compatibility (10 points max) ===
        $expLevels = ['Beginner' => 1, 'Intermediate' => 2, 'Advanced' => 3, 'Expert' => 4];
        $myLevel = $expLevels[$myProfile['experience_level'] ?? 'Beginner'] ?? 1;
        $otherLevel = $expLevels[$otherProfile['experience_level'] ?? 'Beginner'] ?? 1;

        $levelDiff = abs($myLevel - $otherLevel);
        if ($levelDiff === 0) {
            $score += 10; // Same level - great compatibility
        } elseif ($levelDiff === 1) {
            $score += 7;  // Close levels
        } elseif ($levelDiff === 2) {
            $score += 4;  // Somewhat different
        } else {
            $score += 1;  // Very different, but still some compatibility
        }

        // Ensure score doesn't exceed 100
        $score = min($score, 100);

        // Build notes string
        $notesText = !empty($notes) ? implode(". ", $notes) . "." : "Based on profile compatibility.";

        return [
            'score' => $score,
            'matched_skill_id' => $matchedSkillId,
            'notes' => $notesText
        ];
    }

    /**
     * =========================================================================
     * ACCEPT MATCH
     * =========================================================================
     * 
     * User accepts a match recommendation.
     */
    public function accept(): void
    {
        $userId = getCurrentUserId();
        $matchId = (int) ($_GET['id'] ?? 0);

        if ($this->matchModel->updateResponse($matchId, $userId, 'accepted')) {
            flash('Match accepted! You can now send an exchange request.', 'success');
        } else {
            flash('Failed to accept match.', 'danger');
        }

        redirect(url('Match', 'index'));
    }

    /**
     * =========================================================================
     * DECLINE MATCH
     * =========================================================================
     * 
     * User declines a match recommendation.
     */
    public function decline(): void
    {
        $userId = getCurrentUserId();
        $matchId = (int) ($_GET['id'] ?? 0);

        if ($this->matchModel->updateResponse($matchId, $userId, 'declined')) {
            flash('Match declined.', 'info');
        } else {
            flash('Failed to decline match.', 'danger');
        }

        redirect(url('Match', 'index'));
    }

    /**
     * =========================================================================
     * VIEW MATCH DETAILS
     * =========================================================================
     * 
     * View detailed information about a specific match.
     */
    public function view(): void
    {
        $userId = getCurrentUserId();
        $matchId = (int) ($_GET['id'] ?? 0);

        $match = $this->matchModel->getById($matchId);

        if (!$match || ($match['user_id_1'] != $userId && $match['user_id_2'] != $userId)) {
            flash('Match not found.', 'danger');
            redirect(url('Match', 'index'));
            return;
        }

        // Determine other user
        $otherUserId = ($match['user_id_1'] == $userId) ? $match['user_id_2'] : $match['user_id_1'];

        // Get other user's profile
        $otherProfile = $this->profileModel->getByUserId($otherUserId);

        // Get other user's skills
        $otherSkills = $this->skillModel->getUserSkillsPublic($otherUserId);
        $otherWanted = $this->skillModel->getWantedSkillsPublic($otherUserId);

        // Get my skills for comparison
        $mySkills = $this->skillModel->getUserSkills($userId);
        $myWanted = $this->skillModel->getWantedSkills($userId);

        setPageTitle('Match Details');
        require_once BASE_PATH . '/views/matches/view.php';
    }
}

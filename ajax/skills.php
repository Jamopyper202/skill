<?php
/**
 * ============================================================================
 * AJAX Skill Handler
 * ============================================================================
 * 
 * Handles AJAX requests for skill functionality:
 * - Get skills by category
 * - Search skills
 * - Check if user has skill
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

// Verify user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = getCurrentUserId();
$action = $_GET['action'] ?? '';

$skillModel = new Skill();

switch ($action) {
    // =========================================================================
    // GET SKILLS BY CATEGORY
    // =========================================================================
    case 'get_by_category':
        $categoryId = (int) ($_GET['category_id'] ?? 0);

        if ($categoryId === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid category']);
            exit;
        }

        $skills = $skillModel->getByCategory($categoryId);

        $formatted = [];
        foreach ($skills as $skill) {
            $formatted[] = [
                'id' => $skill['id'],
                'name' => e($skill['name']),
                'description' => e($skill['description'] ?? '')
            ];
        }

        echo json_encode([
            'success' => true,
            'skills' => $formatted
        ]);
        break;

    // =========================================================================
    // SEARCH MASTER SKILLS
    // =========================================================================
    case 'search':
        $query = trim($_GET['query'] ?? '');

        if (empty($query)) {
            echo json_encode(['success' => false, 'message' => 'Search query required']);
            exit;
        }

        $skills = $skillModel->searchMaster($query);

        $formatted = [];
        foreach ($skills as $skill) {
            $formatted[] = [
                'id' => $skill['id'],
                'name' => e($skill['name']),
                'category' => e($skill['category_name'] ?? ''),
                'description' => e($skill['description'] ?? '')
            ];
        }

        echo json_encode([
            'success' => true,
            'skills' => $formatted
        ]);
        break;

    // =========================================================================
    // CHECK IF USER HAS SKILL
    // =========================================================================
    case 'check_user_skill':
        $skillId = (int) ($_GET['skill_id'] ?? 0);

        if ($skillId === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid skill']);
            exit;
        }

        $hasSkill = $skillModel->userHasSkill($userId, $skillId);
        $wantsSkill = $skillModel->userWantsSkill($userId, $skillId);

        echo json_encode([
            'success' => true,
            'has_skill' => $hasSkill,
            'wants_skill' => $wantsSkill
        ]);
        break;

    // =========================================================================
    // DEFAULT
    // =========================================================================
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
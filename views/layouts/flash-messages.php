<?php
/**
 * ============================================================================
 * Flash Messages Layout
 * ============================================================================
 * 
 * Displays Bootstrap alert messages stored in session.
 * Included in header.php so it appears on every page.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// Get and clear flash messages from session
$messages = getFlashMessages();

if (!empty($messages)):
?>
<div class="container mt-3">
    <?php foreach ($messages as $msg): 
        $alertClass = match($msg['type']) {
            'success' => 'alert-success',
            'danger'  => 'alert-danger',
            'warning' => 'alert-warning',
            'info'    => 'alert-info',
            default   => 'alert-info'
        };
        
        $icon = match($msg['type']) {
            'success' => 'bi-check-circle-fill',
            'danger'  => 'bi-exclamation-triangle-fill',
            'warning' => 'bi-exclamation-circle-fill',
            'info'    => 'bi-info-circle-fill',
            default   => 'bi-info-circle-fill'
        };
    ?>
    <div class="alert <?php echo $alertClass; ?> alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="bi <?php echo $icon; ?> me-2 fs-5"></i>
        <div><?php echo e($msg['message']); ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
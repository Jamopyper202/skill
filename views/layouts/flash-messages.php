<?php

/**
 * ============================================================================
 * SkillSwap Flash Messages
 * ============================================================================
 */

$messages = getFlashMessages();

if (!empty($messages)):
?>

    <div class="container mt-3">

        <?php foreach ($messages as $msg): ?>

            <?php

            $type = $msg['type'] ?? 'info';

            $config = match ($type) {

                'success' => [
                    'class' => 'alert-success',
                    'icon'  => 'bi-check-circle-fill'
                ],

                'danger' => [
                    'class' => 'alert-danger',
                    'icon'  => 'bi-exclamation-triangle-fill'
                ],

                'warning' => [
                    'class' => 'alert-warning',
                    'icon'  => 'bi-exclamation-circle-fill'
                ],

                'info' => [
                    'class' => 'alert-info',
                    'icon'  => 'bi-info-circle-fill'
                ],

                default => [
                    'class' => 'alert-info',
                    'icon'  => 'bi-info-circle-fill'
                ]
            };

            ?>

            <div
                class="alert <?php echo $config['class']; ?> alert-dismissible fade show d-flex align-items-center shadow-sm border-0 rounded-3"
                role="alert">

                <!-- Icon -->

                <div
                    class="d-flex align-items-center justify-content-center flex-shrink-0 me-3"
                    style="
                    width: 38px;
                    height: 38px;
                    border-radius: 50%;
                    background: rgba(255,255,255,.45);
                ">

                    <i
                        class="bi <?php echo $config['icon']; ?>"
                        style="font-size: 1.1rem;"></i>

                </div>


                <!-- Message -->

                <div class="flex-grow-1">

                    <?php echo e($msg['message']); ?>

                </div>


                <!-- Close -->

                <button
                    type="button"
                    class="btn-close ms-3"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>
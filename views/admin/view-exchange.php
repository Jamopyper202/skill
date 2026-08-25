<?php

/**
 * Admin View Exchange
 */
$title = 'Exchange #' . ($exchange['id'] ?? '?');
$activeTab = 'admin';

$exchange = $exchange ?? [];
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/admin-navbar.php'; ?>

<div class="container-fluid py-4">

    <!-- Breadcrumb -->

    <nav aria-label="breadcrumb" class="mb-4">

        <ol class="breadcrumb">

            <li class="breadcrumb-item">
                <a href="<?= url('Admin', 'index') ?>">
                    Dashboard
                </a>
            </li>

            <li class="breadcrumb-item">
                <a href="<?= url('Admin', 'exchanges') ?>">
                    Exchanges
                </a>
            </li>

            <li class="breadcrumb-item active">
                Exchange #<?= (int) $exchange['id'] ?>
            </li>

        </ol>

    </nav>


    <div class="row g-4">

        <!-- =====================================================
             MAIN DETAILS
        ====================================================== -->

        <div class="col-lg-8">

            <div class="card shadow border-0">

                <div class="card-header bg-white">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">

                        <h4 class="mb-0">

                            <i class="fas fa-exchange-alt
                                      text-primary me-2"></i>

                            Exchange Details

                        </h4>


                        <?php
                        $status = $exchange['status'] ?? '';

                        $badge = match ($status) {

                            'pending'
                            => 'warning text-dark',

                            'accepted'
                            => 'success',

                            'in_progress'
                            => 'info',

                            'completed'
                            => 'primary',

                            'declined'
                            => 'danger',

                            default
                            => 'secondary'
                        };
                        ?>

                        <span class="badge bg-<?= $badge ?>">

                            <?= e(
                                ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $status
                                    )
                                )
                            ) ?>

                        </span>

                    </div>

                </div>


                <div class="card-body p-4">

                    <!-- Requesters -->

                    <h5 class="mb-3">
                        <i class="fas fa-users
                                  text-primary me-2"></i>
                        Participants
                    </h5>


                    <div class="row g-3 mb-4">

                        <div class="col-md-6">

                            <div class="border rounded p-3">

                                <small class="text-muted">
                                    Requester
                                </small>

                                <h6 class="mb-1 mt-1">

                                    <?= e(
                                        $exchange['requester_name']
                                    ) ?>

                                </h6>

                                <small class="text-muted">

                                    <?= e(
                                        $exchange['requester_email']
                                    ) ?>

                                </small>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="border rounded p-3">

                                <small class="text-muted">
                                    Receiver
                                </small>

                                <h6 class="mb-1 mt-1">

                                    <?= e(
                                        $exchange['receiver_name']
                                    ) ?>

                                </h6>

                                <small class="text-muted">

                                    <?= e(
                                        $exchange['receiver_email']
                                    ) ?>

                                </small>

                            </div>

                        </div>

                    </div>


                    <!-- Skills -->

                    <h5 class="mb-3">

                        <i class="fas fa-lightbulb
                                  text-warning me-2"></i>

                        Skills

                    </h5>


                    <div class="row g-3 mb-4">

                        <div class="col-md-6">

                            <div class="card
                                        bg-light
                                        border-0
                                        h-100">

                                <div class="card-body">

                                    <small class="text-muted">
                                        Skill Offered
                                    </small>

                                    <h5 class="mt-2">

                                        <?= e(
                                            $exchange['offered_skill_name']
                                        ) ?>

                                    </h5>

                                    <?php if (
                                        !empty($exchange['offered_skill_description'])
                                    ): ?>

                                        <p class="small
                                                  text-muted
                                                  mb-0">

                                            <?= e(
                                                $exchange['offered_skill_description']
                                            ) ?>

                                        </p>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="card
                                        bg-light
                                        border-0
                                        h-100">

                                <div class="card-body">

                                    <small class="text-muted">
                                        Skill Requested
                                    </small>

                                    <h5 class="mt-2">

                                        <?= e(
                                            $exchange['requested_skill_name']
                                        ) ?>

                                    </h5>

                                    <?php if (
                                        !empty($exchange['requested_skill_description'])
                                    ): ?>

                                        <p class="small
                                                  text-muted
                                                  mb-0">

                                            <?= e(
                                                $exchange['requested_skill_description']
                                            ) ?>

                                        </p>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- Message -->

                    <h5 class="mb-3">

                        <i class="fas fa-comment-alt
                                  text-info me-2"></i>

                        Request Message

                    </h5>


                    <div class="bg-light
                                rounded
                                p-3
                                mb-4">

                        <?php if (
                            !empty($exchange['message'])
                        ): ?>

                            <?= nl2br(
                                e($exchange['message'])
                            ) ?>

                        <?php else: ?>

                            <span class="text-muted">
                                No message provided.
                            </span>

                        <?php endif; ?>

                    </div>


                    <!-- Dates -->

                    <h5 class="mb-3">

                        <i class="fas fa-calendar-alt
                                  text-success me-2"></i>

                        Exchange Schedule

                    </h5>


                    <dl class="row mb-0">

                        <dt class="col-sm-4">
                            Start Date
                        </dt>

                        <dd class="col-sm-8">

                            <?= !empty($exchange['start_date'])
                                ? formatDate(
                                    $exchange['start_date'],
                                    'F j, Y'
                                )
                                : 'Not specified'
                            ?>

                        </dd>


                        <dt class="col-sm-4">
                            End Date
                        </dt>

                        <dd class="col-sm-8">

                            <?= !empty($exchange['end_date'])
                                ? formatDate(
                                    $exchange['end_date'],
                                    'F j, Y'
                                )
                                : 'Not specified'
                            ?>

                        </dd>


                        <dt class="col-sm-4">
                            Created
                        </dt>

                        <dd class="col-sm-8">

                            <?= !empty($exchange['created_at'])
                                ? formatDate(
                                    $exchange['created_at'],
                                    'F j, Y g:i A'
                                )
                                : ''
                            ?>

                        </dd>


                        <dt class="col-sm-4">
                            Last Updated
                        </dt>

                        <dd class="col-sm-8">

                            <?= !empty($exchange['updated_at'])
                                ? formatDate(
                                    $exchange['updated_at'],
                                    'F j, Y g:i A'
                                )
                                : ''
                            ?>

                        </dd>

                    </dl>

                </div>

            </div>

        </div>


        <!-- =====================================================
             SIDEBAR
        ====================================================== -->

        <div class="col-lg-4">

            <div class="card shadow border-0 mb-4">

                <div class="card-header bg-white">

                    <h5 class="mb-0">

                        <i class="fas fa-info-circle
                                  text-primary me-2"></i>

                        Exchange Information

                    </h5>

                </div>


                <div class="card-body">

                    <dl class="row mb-0">

                        <dt class="col-6">
                            Exchange ID
                        </dt>

                        <dd class="col-6">
                            #<?= (int) $exchange['id'] ?>
                        </dd>


                        <dt class="col-6">
                            Match ID
                        </dt>

                        <dd class="col-6">
                            #<?= (int) $exchange['match_id'] ?>
                        </dd>


                        <dt class="col-6">
                            Status
                        </dt>

                        <dd class="col-6">

                            <span class="badge bg-<?= $badge ?>">

                                <?= e(
                                    ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $status
                                        )
                                    )
                                ) ?>

                            </span>

                        </dd>

                    </dl>

                </div>

            </div>

            <!-- Exchange Status Management -->

            <div class="card shadow border-0 mb-4">

                <div class="card-header bg-white">

                    <h5 class="mb-0">

                        <i class="fas fa-tasks text-primary me-2"></i>

                        Manage Exchange

                    </h5>

                </div>


                <div class="card-body">

                    <form
                        action="<?= url(
                                    'Admin',
                                    'updateExchangeStatus',
                                    [
                                        'id' => (int) $exchange['id']
                                    ]
                                ) ?>"
                        method="POST"
                        onsubmit="return confirm('Are you sure you want to update this exchange status?');">

                        

                        <div class="mb-3">

                            <label
                                for="status"
                                class="form-label fw-bold">
                                Change Status
                            </label>

                            <select
                                name="status"
                                id="status"
                                class="form-select"
                                required>

                                <option value="">
                                    -- Select Status --
                                </option>

                                <option
                                    value="pending"
                                    <?= $exchange['status'] === 'pending'
                                        ? 'selected'
                                        : '' ?>>
                                    Pending
                                </option>

                                <option
                                    value="accepted"
                                    <?= $exchange['status'] === 'accepted'
                                        ? 'selected'
                                        : '' ?>>
                                    Accepted
                                </option>

                                <option
                                    value="in_progress"
                                    <?= $exchange['status'] === 'in_progress'
                                        ? 'selected'
                                        : '' ?>>
                                    In Progress
                                </option>

                                <option
                                    value="completed"
                                    <?= $exchange['status'] === 'completed'
                                        ? 'selected'
                                        : '' ?>>
                                    Completed
                                </option>

                                <option
                                    value="declined"
                                    <?= $exchange['status'] === 'declined'
                                        ? 'selected'
                                        : '' ?>>
                                    Declined
                                </option>

                            </select>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary w-100">

                            <i class="fas fa-save me-1"></i>

                            Update Status

                        </button>

                    </form>

                </div>

            </div>


            <!-- Back Button -->

            <a
                href="<?= url(
                            'Admin',
                            'exchanges'
                        ) ?>"
                class="btn btn-outline-secondary
                       w-100">

                <i class="fas fa-arrow-left me-1"></i>

                Back to Exchanges

            </a>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
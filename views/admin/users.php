<?php

/**
 * Admin Users Management View
 *  
 *  @var array $offset 
 */
 

$title = 'Manage Users';
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>


<div class="container-fluid py-4">

    <!-- =========================================================
         PAGE HEADER
    ========================================================== -->

    <div class="row mb-4 align-items-center">

        <div class="col-md-6">

            <h1 class="h2 mb-0">

                <i class="fas fa-users text-primary me-2"></i>

                Manage Users

            </h1>

            <p class="text-muted mb-0">
                Manage SkillSwap users and account status.
            </p>

        </div>

        <div class="col-md-6">

            <form
                action="<?= url('Admin', 'users') ?>"
                method="GET"
                class="d-flex justify-content-md-end mt-3 mt-md-0" id="adminSearchForm">

                <input
                    type="text"
                    id="adminSearch"
                    name="search"
                    class="form-control"
                    style="max-width: 300px;"
                    placeholder="Search users..."
                    value="<?= e($_GET['search'] ?? '') ?>">

                <button
                    type="submit"
                    class="btn btn-primary ms-2">

                    <i class="bi bi-search"></i>

                </button>

            </form>

        </div>
        <script>
            document.getElementById('adminSearchForm').addEventListener('submit', function(e) {

                e.preventDefault();

                const search =
                    document.getElementById('adminSearch').value;

                const targetUrl =
                    this.action +
                    '&search=' +
                    encodeURIComponent(search);

                console.log('NAVIGATING TO:', targetUrl);

                window.location.href = targetUrl;

            });
        </script>
    </div>


    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


    <!-- =========================================================
         USERS TABLE
    ========================================================== -->

    <div class="card shadow border-0">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="mb-0">
                        <i class="bi bi-people me-2"></i>
                        All Users
                    </h5>

                    <?php if (isset($totalUsers)): ?>

                        <small class="text-muted">
                            <?= (int) $totalUsers ?>
                            user<?= $totalUsers != 1 ? 's' : '' ?>
                        </small>

                    <?php endif; ?>

                </div>

            </div>

        </div>


        <div class="table-responsive">

            <table class="table table-hover mb-0 align-middle">

                <thead class="table-light">

                    <tr>
                        <th class="text-center">S/N</th>

                        <th>
                            User
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Joined
                        </th>

                        <th>
                            Exchanges
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Role
                        </th>

                        <th class="text-center">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (!empty($users)): ?>


                        <?php foreach ($users as $index => $user): ?>




                            <?php

                            $serialNumber = $offset + $index + 1;

                            $userId =
                                (int) ($user['id'] ?? 0);

                            $fullName =
                                $user['full_name']
                                ?? 'Unknown User';

                            $email =
                                $user['email']
                                ?? '';

                            $profilePicture =
                                $user['profile_picture']
                                ?? '';

                            $isActive =
                                !empty($user['is_active']);

                            /*
                             * Support either role or is_admin
                             * depending on your current database.
                             */
                            $role =
                                $user['role']
                                ?? null;

                            if ($role !== null) {

                                $isAdmin =
                                    strtolower($role) === 'admin';
                            } else {

                                $isAdmin =
                                    !empty($user['is_admin']);
                            }

                            $exchangeCount =
                                (int) (
                                    $user['exchange_count']
                                    ?? 0
                                );

                            ?>


                            <tr>
                                <td class="text-center">
                                    <?= $offset + $index + 1 ?>
                                </td>

                                <!-- =================================
                                     USER
                                ================================== -->

                                <td>


                                    <div
                                        class="d-flex align-items-center">

                                        <img
                                            src="<?= e(
                                                        uploadUrl(
                                                            $profilePicture
                                                        )
                                                    ) ?>"
                                            alt="<?= e(
                                                        $fullName
                                                    ) ?>"
                                            class="rounded-circle me-2"
                                            width="40"
                                            height="40"
                                            style="object-fit: cover;">


                                        <div>

                                            <div class="fw-bold">

                                                <?= e(
                                                    $fullName
                                                ) ?>

                                            </div>

                                            <?php if (!empty($user['username'])): ?>

                                                <small
                                                    class="text-muted">

                                                    @<?= e(
                                                            $user['username']
                                                        ) ?>

                                                </small>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                </td>


                                <!-- =================================
                                     EMAIL
                                ================================== -->

                                <td>

                                    <?= e($email) ?>

                                </td>


                                <!-- =================================
                                     JOINED
                                ================================== -->

                                <td>

                                    <?php if (!empty($user['created_at'])): ?>

                                        <small>

                                            <?= e(
                                                formatDate(
                                                    $user['created_at'],
                                                    'M j, Y'
                                                )
                                            ) ?>

                                        </small>

                                    <?php else: ?>

                                        <small class="text-muted">
                                            N/A
                                        </small>

                                    <?php endif; ?>

                                </td>


                                <!-- =================================
                                     EXCHANGES
                                ================================== -->

                                <td>

                                    <span class="fw-semibold">

                                        <?= $exchangeCount ?>

                                    </span>

                                </td>


                                <!-- =================================
                                     STATUS
                                ================================== -->

                                <td>

                                    <?php if ($isActive): ?>

                                        <span class="badge bg-success">

                                            <i
                                                class="fas fa-check-circle
                                                       me-1">
                                            </i>

                                            Active

                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-secondary">

                                            <i
                                                class="fas fa-ban me-1">
                                            </i>

                                            Inactive

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- =================================
                                     ROLE
                                ================================== -->

                                <td>

                                    <?php if ($isAdmin): ?>

                                        <span class="badge bg-danger">

                                            <i
                                                class="fas fa-shield-alt
                                                       me-1">
                                            </i>

                                            Admin

                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-info">

                                            <i
                                                class="fas fa-user me-1">
                                            </i>

                                            User

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- =================================
                                     ACTIONS
                                ================================== -->

                                <td class="text-center">

                                    <div class="btn-group" role="group">

                                        <!-- View User -->

                                        <a
                                            href="<?= url(
                                                        'Admin',
                                                        'viewUser',
                                                        ['id' => $userId]
                                                    ) ?>"
                                            class="btn btn-sm btn-outline-primary"
                                            title="View User">

                                            <i class="bi bi-eye"></i>

                                        </a>


                                        <!-- Activate / Deactivate -->
                                        <?php if ($userId > 0): ?>

                                            <?php
                                            $buttonClass = $isActive
                                                ? 'warning'
                                                : 'success';

                                            $buttonTitle = $isActive
                                                ? 'Deactivate User'
                                                : 'Activate User';

                                            $actionText = $isActive
                                                ? 'Deactivate'
                                                : 'Activate';
                                            ?>

                                            <form
                                                action="<?= url(
                                                            'Admin',
                                                            'toggleUser',
                                                            ['id' => $userId]
                                                        ) ?>"
                                                method="POST"
                                                class="d-inline">


                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-<?= $buttonClass ?>"
                                                    title="<?= e($buttonTitle) ?>"
                                                    onclick="return confirm('<?= e($actionText) ?> this user?');">

                                                    <?php if ($isActive): ?>

                                                        <i class="bi bi-person-x"></i>

                                                    <?php else: ?>

                                                        <i class="bi bi-person-check"></i>

                                                    <?php endif; ?>

                                                </button>

                                            </form>

                                        <?php endif; ?>

                                    </div>

                                </td>

                            </tr>


                        <?php endforeach; ?>


                    <?php else: ?>


                        <!-- No Users -->

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-5">

                                <div class="text-muted">

                                    <i
                                        class="fas fa-users fa-3x
                                               mb-3 opacity-50">
                                    </i>

                                    <h5>
                                        No users found
                                    </h5>

                                    <p class="mb-0">

                                        There are no users matching
                                        your search.

                                    </p>

                                </div>

                            </td>

                        </tr>


                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


    <!-- =========================================================
         PAGINATION
    ========================================================== -->

    <?php if (($totalPages ?? 1) > 1): ?>

        <nav
            class="mt-4"
            aria-label="Users pagination">

            <ul
                class="pagination justify-content-center">


                <!-- Previous -->
                <?php
                $currentPage = (int) ($currentPage ?? 1);
                $totalPages = (int) ($totalPages ?? 1);
                ?>
                <?php if (($currentPage ?? 1) > 1): ?>

                    <li class="page-item">

                        <a
                            class="page-link"
                            href="<?= url(
                                        'Admin',
                                        'users',
                                        [
                                            'page' =>
                                            $currentPage - 1,
                                            'search' =>
                                            $_GET['search'] ?? ''
                                        ]
                                    ) ?>">

                            <i class="bi bi-chevron-left"></i>

                        </a>

                    </li>

                <?php endif; ?>


                <!-- Page Numbers -->

                <?php for (
                    $i = 1;
                    $i <= $totalPages;
                    $i++
                ): ?>

                    <li
                        class="page-item
                        <?= ($currentPage ?? 1) == $i
                            ? 'active'
                            : '' ?>">

                        <a
                            class="page-link"
                            href="<?= url(
                                        'Admin',
                                        'users',
                                        [
                                            'page' => $i,
                                            'search' =>
                                            $_GET['search'] ?? ''
                                        ]
                                    ) ?>">

                            <?= $i ?>

                        </a>

                    </li>

                <?php endfor; ?>


                <!-- Next -->

                <?php if (
                    ($currentPage ?? 1)
                    < $totalPages
                ): ?>

                    <li class="page-item">

                        <a
                            class="page-link"
                            href="<?= url(
                                        'Admin',
                                        'users',
                                        [
                                            'page' =>
                                            $currentPage + 1,
                                            'search' =>
                                            $_GET['search'] ?? ''
                                        ]
                                    ) ?>">

                            <i class="bi bi-chevron-right"></i>

                        </a>

                    </li>

                <?php endif; ?>


            </ul>

        </nav>

    <?php endif; ?>

</div>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
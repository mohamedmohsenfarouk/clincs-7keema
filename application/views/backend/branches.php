<script src="<?= asset_url('assets/js/backend_branches_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_categories_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_branches.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl: <?= json_encode($base_url) ?>,
        dateFormat: <?= json_encode($date_format) ?>,
        timeFormat: <?= json_encode($time_format) ?>,
        services: <?= json_encode($services) ?>,
        branches: <?= json_encode($branches) ?>,
        categories: <?= json_encode($categories) ?>,
        timezones: <?= json_encode($timezones) ?>,
        user: {
            id: <?= $user_id ?>,
            email: <?= json_encode($user_email) ?>,
            timezone: <?= json_encode($timezone) ?>,
            role_slug: <?= json_encode($role_slug) ?>,
            privileges: <?= json_encode($privileges) ?>
        }
    };

    $(function () {
        BackendBranches.initialize(true);
    });
</script>

<div class="container-fluid backend-page" id="branches-page">
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link active" href="#branches" data-toggle="tab">
                <?= lang('branches') ?>
            </a>
        </li>
      
    </ul>

    <div class="tab-content">

        <!-- branches TAB -->

        <div class="tab-pane active" id="branches">
            <div class="row">
                <div id="filter-branches" class="filter-records col col-12 col-md-5">
                    <form class="mb-4">
                        <div class="input-group">
                            <input type="text" class="key form-control">

                            <div class="input-group-addon">
                                <div>
                                    <button class="filter btn btn-outline-secondary" type="submit"
                                            data-tippy-content="<?= lang('filter') ?>">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="clear btn btn-outline-secondary" type="button"
                                            data-tippy-content="<?= lang('clear') ?>">
                                        <i class="fas fa-redo-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <h3><?= lang('branches') ?></h3>
                    <div class="results"></div>
                </div>

                <div class="record-details column col-12 col-md-5">
                    <div class="btn-toolbar mb-4">
                        <div class="add-edit-delete-group btn-group">
                            <button id="add-branch" class="btn btn-primary">
                                <i class="fas fa-plus-square mr-2"></i>
                                <?= lang('add') ?>
                            </button>
                            <button id="edit-branch" class="btn btn-outline-secondary" disabled="disabled">
                                <i class="fas fa-edit mr-2"></i>
                                <?= lang('edit') ?>
                            </button>
                            <button id="delete-branch" class="btn btn-outline-secondary" disabled="disabled">
                                <i class="fas fa-trash-alt mr-2"></i>
                                <?= lang('delete') ?>
                            </button>
                        </div>

                        <div class="save-cancel-group btn-group" style="display:none;">
                            <button id="save-branch" class="btn btn-primary">
                                <i class="fas fa-check-square mr-2"></i>
                                <?= lang('save') ?>
                            </button>
                            <button id="cancel-branch" class="btn btn-outline-secondary">
                                <i class="fas fa-ban mr-2"></i>
                                <?= lang('cancel') ?>
                            </button>
                        </div>
                    </div>

                    <h3><?= lang('details') ?></h3>

                    <div class="form-message alert" style="display:none;"></div>

                    <input type="hidden" id="branch-id">

                    <div class="form-group">
                        <label for="branch-name">
                            <?= lang('name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="branch-name" class="form-control required" maxlength="128">
                    </div>


                    <div class="form-group">
                        <label for="branch-location">
                            <?= lang('location') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="branch-location" class="form-control">
                    </div>

                         

                </div>
            </div>
        </div>

    </div>
</div>

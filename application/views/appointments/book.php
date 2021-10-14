<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#35A768">

    <title><?=lang('page_title') . ' ' . $company_name?></title>

    <link rel="stylesheet" type="text/css" href="<?=asset_url('assets/ext/bootstrap/css/bootstrap.min.css')?>">
    <link rel="stylesheet" type="text/css" href="<?=asset_url('assets/ext/jquery-ui/jquery-ui.min.css')?>">
    <link rel="stylesheet" type="text/css" href="<?=asset_url('assets/ext/cookieconsent/cookieconsent.min.css')?>">
    <link rel="stylesheet" type="text/css" href="<?=asset_url('assets/css/frontend.css')?>">
    <link rel="stylesheet" type="text/css" href="<?=asset_url('assets/css/general.css')?>">

    <link rel="icon" type="image/x-icon" href="<?=asset_url('assets/img/favicon.ico')?>">
    <link rel="icon" sizes="192x192" href="<?=asset_url('assets/img/logo.png')?>">

    <script src="<?=asset_url('assets/ext/fontawesome/js/fontawesome.min.js')?>"></script>
    <script src="<?=asset_url('assets/ext/fontawesome/js/solid.min.js')?>"></script>
</head>

<body>
    <div id="main" class="container">
        <div class="row wrapper">
            <div id="book-appointment-wizard" class="col-12 col-lg-10 col-xl-8">

                <!-- FRAME TOP BAR -->

                <div id="header">
                    <span id="company-name"><?=$company_name?></span>

                    <div id="steps">
                        <?php

if (empty($this->session->user_id)) {?>
                        <div id="step-1" class="book-step active-step"
                            data-tippy-content="<?=lang('service_and_provider')?>">
                            <strong>1</strong>
                        </div>

                        <div id="step-2" class="book-step" data-toggle="tooltip"
                            data-tippy-content="<?=lang('appointment_date_and_time')?>">
                            <strong>2</strong>
                        </div>
                        <div id="step-3" class="book-step" data-toggle="tooltip" data-tippy-content="Register">
                            <strong>3</strong>
                        </div>
                        <div id="step-4" class="book-step" data-toggle="tooltip"
                            data-tippy-content="<?=lang('customer_information')?>">
                            <strong>4</strong>
                        </div>
                        <div id="step-5" class="book-step" data-toggle="tooltip"
                            data-tippy-content="<?=lang('appointment_confirmation')?>">
                            <strong>5</strong>
                        </div>
                        <?php } else {?>
                        <div id="step-1" class="book-step active-step"
                            data-tippy-content="<?=lang('service_and_provider')?>">
                            <strong>1</strong>
                        </div>

                        <div id="step-2" class="book-step" data-toggle="tooltip"
                            data-tippy-content="<?=lang('appointment_date_and_time')?>">
                            <strong>2</strong>
                        </div>
                        <div id="step-3" class="book-step" data-toggle="tooltip"
                            data-tippy-content="<?=lang('customer_information')?>">
                            <strong>3</strong>
                        </div>
                        <div id="step-4" class="book-step" data-toggle="tooltip"
                            data-tippy-content="<?=lang('appointment_confirmation')?>">
                            <strong>4</strong>
                        </div>

                        <?php }?>
                    </div>
                </div>

                <?php if ($manage_mode): ?>
                <div id="cancel-appointment-frame" class="row booking-header-bar">
                    <div class="col-12 col-md-10">
                        <small><?=lang('cancel_appointment_hint')?></small>
                    </div>
                    <div class="col-12 col-md-2">
                        <form id="cancel-appointment-form" method="post"
                            action="<?=site_url('appointments/cancel/' . $appointment_data['hash'])?>">

                            <input type="hidden" name="csrfToken" value="<?=$this->security->get_csrf_hash()?>" />

                            <textarea name="cancel_reason" style="display:none"></textarea>

                            <button id="cancel-appointment" class="btn btn-warning btn-sm">
                                <?=lang('cancel')?>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="booking-header-bar row">
                    <div class="col-12 col-md-10">
                        <small><?=lang('delete_personal_information_hint')?></small>
                    </div>
                    <div class="col-12 col-md-2">
                        <button id="delete-personal-information"
                            class="btn btn-danger btn-sm"><?=lang('delete')?></button>
                    </div>
                </div>
                <?php endif;?>

                <?php if (isset($exceptions)): ?>
                <div style="margin: 10px">
                    <h4><?=lang('unexpected_issues')?></h4>

                    <?php foreach ($exceptions as $exception): ?>
                    <?=exceptionToHtml($exception)?>
                    <?php endforeach?>
                </div>
                <?php endif?>

                <?php if (empty($this->session->user_id)) {?>

                <!-- SELECT SERVICE AND PROVIDER -->

                <div id="wizard-frame-1" class="wizard-frame">
                    <div class="frame-container">
                        <h2 class="frame-title"><?=lang('service_and_provider')?></h2>

                        <div class="row frame-content">
                            <div class="col">
                                <div class="form-group">
                                    <label for="select-service">
                                        <strong><?=lang('service')?></strong>
                                    </label>

                                    <select id="select-service" class="form-control">
                                        <?php
// Group services by category, only if there is at least one service with a parent category.
    $has_category = false;
    foreach ($available_services as $service) {
        if ($service['category_id'] != null) {
            $has_category = true;
            break;
        }
    }

    if ($has_category) {
        $grouped_services = [];

        foreach ($available_services as $service) {
            if ($service['category_id'] != null) {
                if (!isset($grouped_services[$service['category_name']])) {
                    $grouped_services[$service['category_name']] = [];
                }

                $grouped_services[$service['category_name']][] = $service;
            }
        }

        // We need the uncategorized services at the end of the list so we will use
        // another iteration only for the uncategorized services.
        $grouped_services['uncategorized'] = [];
        foreach ($available_services as $service) {
            if ($service['category_id'] == null) {
                $grouped_services['uncategorized'][] = $service;
            }
        }

        foreach ($grouped_services as $key => $group) {
            $group_label = ($key != 'uncategorized')
            ? $group[0]['category_name'] : 'Uncategorized';

            if (count($group) > 0) {
                echo '<optgroup label="' . $group_label . '">';
                foreach ($group as $service) {
                    echo '<option value="' . $service['id'] . '">'
                        . $service['name'] . '</option>';
                }
                echo '</optgroup>';
            }
        }
    } else {
        foreach ($available_services as $service) {
            echo '<option value="' . $service['id'] . '">' . $service['name'] . '</option>';
        }
    }
    ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="one_eye">One Eye</label>
                                    <input type="radio" name="eyes" id="one_eye" value="One Eye" checked
                                        style="margin-right:75px;">
                                    <label for="both_eyes">Both Eyes</label>
                                    <input type="radio" name="eyes" id="both_eyes" value="Both Eyes">
                                </div>
                                <div class="form-group">
                                    <label for="provider-div">
                                        <strong><?=lang('provider')?></strong>
                                    </label>

                                    <!-- <select id="select-provider" class="form-control" size="3"> -->
                                    <div class="col" id="provider-div">
                                        <?php foreach ($available_providers as $provider) {?>
                                        <div class="form-group provider-card">
                                            <label for="select-provider-<?php echo $provider['id'] ?>">Dr/
                                                <?php echo $provider['first_name'] . " " . $provider['last_name'] ?></label>
                                            <input type="radio" name="select-provider"
                                                data-name="<?php echo $provider['first_name'] ?>"
                                                value="<?php echo $provider['id'] ?>"
                                                id="select-provider-<?php echo $provider['id'] ?>"
                                                style="margin:8px;float:left;" checked>
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        <img src="./assets/img/map-marker-alt-solid.svg"
                                                            class="card-custom-icon">
                                                        <?php echo $provider['address'] . ', ' . $provider['city'] . ', ' . $provider['state'] ?>
                                                    </h5>
                                                    <p class="card-text">
                                                        <img src="./assets/img/phone-alt-solid.svg"
                                                            class="card-custom-icon">
                                                        <?php echo $provider['phone_number'] ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php }?>
                                    </div>

                                    <!-- </select> -->
                                </div>

                                <div id="service-description"></div>
                            </div>
                        </div>
                    </div>

                    <div class="command-buttons">
                        <span>&nbsp;</span>

                        <button type="button" id="button-next-1" class="btn button-next btn-dark" data-step_index="1">
                            <?=lang('next')?>
                            <i class="fas fa-chevron-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- SELECT APPOINTMENT DATE -->

                <div id="wizard-frame-2" class="wizard-frame" style="display:none;">
                    <div class="frame-container">

                        <h2 class="frame-title"><?=lang('appointment_date_and_time')?></h2>

                        <div class="row frame-content">
                            <div class="col-12 col-md-6">
                                <div id="select-date"></div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div id="select-time">
                                    <div class="form-group">
                                        <label for="select-timezone"><?=lang('timezone')?></label>
                                        <?=render_timezone_dropdown('id="select-timezone" class="form-control" value="UTC"');?>
                                    </div>

                                    <div id="available-hours"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="command-buttons">
                        <button type="button" id="button-back-2" class="btn button-back btn-outline-secondary"
                            data-step_index="2">
                            <i class="fas fa-chevron-left mr-2"></i>
                            <?=lang('back')?>
                        </button>
                        <button type="button" id="button-next-2" class="btn button-next btn-dark" data-step_index="2">
                            <?=lang('next')?>
                            <i class="fas fa-chevron-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- ENTER CUSTOMER DATA -->

                <div id="wizard-frame-3" class="wizard-frame" style="display:none;">
                    <div class="frame-container">

                        <h2 class="frame-title"><?=lang('customer_information')?></h2>

                        <div class="row frame-content">

                            <div class="col-12 col-md-6">
                                <form id="register_user" method="post"
                                    action="<?php echo base_url(); ?>index.php/register/validation">
                                    <div class="form-group">
                                        <label>Enter Your First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" id="first_name" class="form-control"
                                            value="<?php echo set_value('first_name'); ?>" />
                                        <span class="text-danger"><?php echo form_error('first_name'); ?></span>
                                    </div>

                                    <div class="form-group">
                                        <label>Enter Your Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" id="last_name" class="form-control"
                                            value="<?php echo set_value('last_name'); ?>" />
                                        <span class="text-danger"><?php echo form_error('last_name'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label>Enter Your Valid Email Address <span class="text-danger">*</span></label>
                                        <input type="text" name="user_email" id="email" class="form-control"
                                            value="<?php echo set_value('user_email'); ?>" />
                                        <span class="text-danger"><?php echo form_error('user_email'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label>Enter Password <span class="text-danger">*</span></label>
                                        <input type="password" name="user_password" id="password" class="form-control"
                                            value="<?php echo set_value('user_password'); ?>" />
                                        <span class="text-danger"><?php echo form_error('user_password'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label>Enter Phone Number <span class="text-danger">*</span></label>
                                        <input type="number" name="phone_number" id="phone_number" class="form-control"
                                            value="<?php echo set_value('phone_number'); ?>" />
                                        <span class="text-danger"><?php echo form_error('phone_number'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <input type="button" name="register" id="register_btn" value="Register"
                                            class="register_button btn btn-info" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a
                                            href="javascript:void(0);" id="loginUser">Login</a>
                                    </div>

                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                        value="<?php echo $this->security->get_csrf_hash(); ?>">
                                </form>

                                <div id="show_login_form" style="display: none;">
                                    <form method="post" id="login_user"
                                        action="<?php echo base_url(); ?>index.php/login/validation">
                                        <div class="form-group">
                                            <label>Enter Email Address</label>
                                            <input type="text" name="user_email" id="email" class="form-control"
                                                value="<?php echo set_value('user_email'); ?>" />
                                            <!-- <input type="text" name="user_email" class="form-control" value="<?php echo set_value('user_email'); ?>" /> -->
                                            <span class="text-danger"><?php echo form_error('user_email'); ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label>Enter Password</label>
                                            <input type="password" name="user_password" id="password"
                                                class="form-control"
                                                value="<?php echo set_value('user_password'); ?>" />

                                            <!-- <input type="password" name="user_password" class="form-control" value="<?php echo set_value('user_password'); ?>" /> -->
                                            <span class="text-danger"><?php echo form_error('user_password'); ?></span>
                                        </div>
                                        <div class="form-group">
                                            <input type="button" name="login" id="login_btn" value="Login"
                                                class="btn btn-info login_button" />
                                        </div>

                                        <input type="hidden"
                                            name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                            value="<?php echo $this->security->get_csrf_hash(); ?>">
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>

                    <?php if ($display_terms_and_conditions): ?>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="required form-check-input" id="accept-to-terms-and-conditions">
                        <label class="form-check-label" for="accept-to-terms-and-conditions">
                            <?=strtr(lang('read_and_agree_to_terms_and_conditions'),
        [
            '{$link}' => '<a href="#" data-toggle="modal" data-target="#terms-and-conditions-modal">',
            '{/$link}' => '</a>',
        ])
    ?>
                        </label>
                    </div>
                    <?php endif?>

                    <?php if ($display_privacy_policy): ?>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="required form-check-input" id="accept-to-privacy-policy">
                        <label class="form-check-label" for="accept-to-privacy-policy">
                            <?=strtr(lang('read_and_agree_to_privacy_policy'),
        [
            '{$link}' => '<a href="#" data-toggle="modal" data-target="#privacy-policy-modal">',
            '{/$link}' => '</a>',
        ])
    ?>
                        </label>
                    </div>
                    <?php endif?>

                    <div class="command-buttons">
                        <button type="button" id="button-back-3" class="btn button-back btn-outline-secondary"
                            data-step_index="3">
                            <i class="fas fa-chevron-left mr-2"></i>
                            <?=lang('back')?>
                        </button>
                        <button type="button" id="button-next-3" class="btn button-next btn-dark" style="display: none;"
                            data-step_index="3">
                            <?=lang('next')?>
                            <i class="fas fa-chevron-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- APPOINTMENT DATA CONFIRMATION 4-->

                <!-- <div id="wizard-frame-4" class="wizard-frame" style="display:none;">
                    <div class="frame-container">

                        <h2 class="frame-title"><?=lang('customer_information')?></h2>

                        <div class="row frame-content">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="address" class="control-label">
                                        <?=lang('address')?>
                                    </label>
                                    <input type="text" id="address" class="form-control" maxlength="120" />
                                </div>
                                <div class="form-group">
                                    <label for="city" class="control-label">
                                        <?=lang('city')?>
                                    </label>
                                    <input type="text" id="city" class="form-control" maxlength="120" />
                                </div>
                                <div class="form-group">
                                    <label for="zip-code" class="control-label">
                                        <?=lang('zip_code')?>
                                    </label>
                                    <input type="text" name="zip_code" id="zip_code" class="form-control"
                                        maxlength="120" />
                                </div>
                                <div class="form-group">
                                    <label for="notes" class="control-label">
                                        <?=lang('notes')?>
                                    </label>
                                    <textarea id="notes" maxlength="500" class="form-control" rows="1"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($display_terms_and_conditions): ?>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="required form-check-input" id="accept-to-terms-and-conditions">
                        <label class="form-check-label" for="accept-to-terms-and-conditions">
                            <?=strtr(lang('read_and_agree_to_terms_and_conditions'),
        [
            '{$link}' => '<a href="#" data-toggle="modal" data-target="#terms-and-conditions-modal">',
            '{/$link}' => '</a>',
        ])
    ?>
                        </label>
                    </div>
                    <?php endif?>

                    <?php if ($display_privacy_policy): ?>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="required form-check-input" id="accept-to-privacy-policy">
                        <label class="form-check-label" for="accept-to-privacy-policy">
                            <?=strtr(lang('read_and_agree_to_privacy_policy'),
        [
            '{$link}' => '<a href="#" data-toggle="modal" data-target="#privacy-policy-modal">',
            '{/$link}' => '</a>',
        ])
    ?>
                        </label>
                    </div>
                    <?php endif?>

                    <div class="command-buttons">
                        <button type="button" id="button-back-4" class="btn button-back btn-outline-secondary"
                            data-step_index="4">
                            <i class="fas fa-chevron-left mr-2"></i>
                            <?=lang('back')?>
                        </button>
                        <button type="button" id="button-next-4" class="btn button-next btn-dark" data-step_index="4">
                            <?=lang('next')?>
                            <i class="fas fa-chevron-right ml-2"></i>
                        </button>
                    </div>
                </div> -->


                <!-- APPOINTMENT DATA CONFIRMATION 4-->

                <div id="wizard-frame-4" class="wizard-frame" style="display:none;">
                    <div class="frame-container">
                        <h2 class="frame-title"><?=lang('appointment_confirmation')?></h2>
                        <div class="row frame-content">
                            <div id="appointment-details" class="col-12 col-md-6"></div>
                            <div id="customer-details" class="col-12 col-md-6"></div>
                        </div>
                        <?php if ($this->settings_model->get_setting('require_captcha') === '1'): ?>
                        <div class="row frame-content">
                            <div class="col-12 col-md-6">
                                <h4 class="captcha-title">
                                    CAPTCHA
                                    <button class="btn btn-link text-dark text-decoration-none py-0">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </h4>
                                <img class="captcha-image" src="<?=site_url('captcha')?>">
                                <input class="captcha-text form-control" type="text" value="" />
                                <span id="captcha-hint" class="help-block" style="opacity:0">&nbsp;</span>
                            </div>
                        </div>
                        <?php endif;?>
                    </div>

                    <div class="command-buttons">
                        <button type="button" id="button-back-4" class="btn button-back btn-outline-secondary"
                            data-step_index="4">
                            <i class="fas fa-chevron-left mr-2"></i>
                            <?=lang('back')?>
                        </button>
                        <form id="book-appointment-form" style="display:inline-block" method="post">
                            <button id="book-appointment-submit" type="button" class="btn btn-success">
                                <i class="fas fa-check-square mr-2"></i>
                                <?=!$manage_mode ? lang('confirm') : lang('update')?>
                            </button>
                            <input type="hidden" name="csrfToken" />
                            <input type="hidden" name="post_data" />
                        </form>
                    </div>
                </div>
                <?php } else {?>
                <!-- SELECT SERVICE AND PROVIDER -->
                <?php
$user_customer_query = $this->db->select('*')->from('ea_users')->where('id', $this->session->user_id)->get();

    if (empty($user_customer_query->result())) {
        $user_customer_query = $this->db->select('*')->from('ea_customers')->where('id', $this->session->user_id)->get();

    }

    ?>
                <div id="wizard-frame-1" class="wizard-frame">
                    <div class="frame-container">
                        <h2 class="frame-title"><?=lang('service_and_provider')?></h2>

                        <div class="row frame-content">
                            <div class="col">
                                <div class="form-group">
                                    <label for="select-service">
                                        <strong><?=lang('service')?></strong>
                                    </label>

                                    <select id="select-service" class="form-control">
                                        <?php
// Group services by category, only if there is at least one service with a parent category.
    $has_category = false;
    foreach ($available_services as $service) {
        if ($service['category_id'] != null) {
            $has_category = true;
            break;
        }
    }

    if ($has_category) {
        $grouped_services = [];

        foreach ($available_services as $service) {
            if ($service['category_id'] != null) {
                if (!isset($grouped_services[$service['category_name']])) {
                    $grouped_services[$service['category_name']] = [];
                }

                $grouped_services[$service['category_name']][] = $service;
            }
        }

        // We need the uncategorized services at the end of the list so we will use
        // another iteration only for the uncategorized services.
        $grouped_services['uncategorized'] = [];
        foreach ($available_services as $service) {
            if ($service['category_id'] == null) {
                $grouped_services['uncategorized'][] = $service;
            }
        }

        foreach ($grouped_services as $key => $group) {
            $group_label = ($key != 'uncategorized')
            ? $group[0]['category_name'] : 'Uncategorized';

            if (count($group) > 0) {
                echo '<optgroup label="' . $group_label . '">';
                foreach ($group as $service) {
                    echo '<option value="' . $service['id'] . '">'
                        . $service['name'] . '</option>';
                }
                echo '</optgroup>';
            }
        }
    } else {
        foreach ($available_services as $service) {
            echo '<option value="' . $service['id'] . '">' . $service['name'] . '</option>';
        }
    }
    ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="one_eye">One Eye</label>
                                    <input type="radio" name="eyes" id="one_eye" value="One Eye" checked
                                        style="margin-right:75px;">
                                    <label for="both_eyes">Both Eyes</label>
                                    <input type="radio" name="eyes" id="both_eyes" value="Both Eyes">
                                </div>
                                <label for="provider-div">
                                    <strong><?=lang('provider')?></strong>
                                </label>
                                <div class="col" id="provider-div">
                                    <?php foreach ($available_providers as $provider) {?>
                                    <div class="form-group provider-card">
                                        <label for="select-provider-<?php echo $provider['id'] ?>">Dr/
                                            <?php echo $provider['first_name'] . " " . $provider['last_name'] ?></label>
                                        <input type="radio" name="select-provider"
                                            data-name="<?php echo $provider['first_name'] ?>"
                                            value="<?php echo $provider['id'] ?>"
                                            id="select-provider-<?php echo $provider['id'] ?>"
                                            style="margin:8px;float:left;" checked>
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <img src="./assets/img/map-marker-alt-solid.svg"
                                                        class="card-custom-icon">
                                                    <?php echo $provider['address'] . ', ' . $provider['city'] . ', ' . $provider['state'] ?>
                                                </h5>
                                                <p class="card-text">
                                                    <img src="./assets/img/phone-alt-solid.svg"
                                                        class="card-custom-icon">
                                                    <?php echo $provider['phone_number'] ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php }?>
                                </div>

                                <div id="service-description"></div>
                            </div>
                        </div>
                    </div>

                    <div class="command-buttons">
                        <span>&nbsp;</span>

                        <button type="button" id="button-next-1" class="btn button-next btn-dark" data-step_index="1">
                            <?=lang('next')?>
                            <i class="fas fa-chevron-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- SELECT APPOINTMENT DATE -->

                <div id="wizard-frame-2" class="wizard-frame" style="display:none;">
                    <div class="frame-container">

                        <h2 class="frame-title"><?=lang('appointment_date_and_time')?></h2>

                        <div class="row frame-content">
                            <div class="col-12 col-md-6">
                                <div id="select-date"></div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div id="select-time">
                                    <div class="form-group">
                                        <label for="select-timezone"><?=lang('timezone')?></label>
                                        <?=render_timezone_dropdown('id="select-timezone" class="form-control" value="UTC"');?>
                                    </div>

                                    <div id="available-hours"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="command-buttons">
                        <button type="button" id="button-back-2" class="btn button-back btn-outline-secondary"
                            data-step_index="2">
                            <i class="fas fa-chevron-left mr-2"></i>
                            <?=lang('back')?>
                        </button>
                        <button type="button" id="button-next-2" class="btn button-next btn-dark" data-step_index="2">
                            <?=lang('next')?>
                            <i class="fas fa-chevron-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- ENTER CUSTOMER DATA -->

                <div id="wizard-frame-3" class="wizard-frame" style="display:none;">
                    <div class="frame-container">

                        <h2 class="frame-title"><?=lang('customer_information')?></h2>

                        <div class="row frame-content">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="first_name" id="first_name" class="form-control"
                                        value="<?php echo $user_customer_query->result()[0]->first_name; ?>" />
                                </div>

                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="last_name" id="last_name" class="form-control"
                                        value="<?php echo $user_customer_query->result()[0]->last_name; ?>" />
                                </div>
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="text" name="user_email" id="email" class="form-control"
                                        value="<?php echo $user_customer_query->result()[0]->email; ?>" />
                                </div>

                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="number" name="phone_number" id="phone_number" class="form-control"
                                        value="<?php echo $user_customer_query->result()[0]->phone_number; ?>" />
                                </div>
                            </div>

                            <!-- <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="address" class="control-label">
                                        <?=lang('address')?>
                                    </label>
                                    <input type="text" id="address" class="form-control" maxlength="120" />
                                </div>
                                <div class="form-group">
                                    <label for="city" class="control-label">
                                        <?=lang('city')?>
                                    </label>
                                    <input type="text" id="city" class="form-control" maxlength="120" />
                                </div>
                                <div class="form-group">
                                    <label for="zip-code" class="control-label">
                                        <?=lang('zip_code')?>
                                    </label>
                                    <input type="text" id="zip_code" name="zip_code" class="form-control"
                                        maxlength="120" />
                                </div>
                                <div class="form-group">
                                    <label for="notes" class="control-label">
                                        <?=lang('notes')?>
                                    </label>
                                    <textarea id="notes" maxlength="500" class="form-control" rows="1"></textarea>
                                </div>
                            </div> -->
                        </div>
                    </div>

                    <?php if ($display_terms_and_conditions): ?>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="required form-check-input" id="accept-to-terms-and-conditions">
                        <label class="form-check-label" for="accept-to-terms-and-conditions">
                            <?=strtr(lang('read_and_agree_to_terms_and_conditions'),
        [
            '{$link}' => '<a href="#" data-toggle="modal" data-target="#terms-and-conditions-modal">',
            '{/$link}' => '</a>',
        ])
    ?>
                        </label>
                    </div>
                    <?php endif?>

                    <?php if ($display_privacy_policy): ?>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="required form-check-input" id="accept-to-privacy-policy">
                        <label class="form-check-label" for="accept-to-privacy-policy">
                            <?=strtr(lang('read_and_agree_to_privacy_policy'),
        [
            '{$link}' => '<a href="#" data-toggle="modal" data-target="#privacy-policy-modal">',
            '{/$link}' => '</a>',
        ])
    ?>
                        </label>
                    </div>
                    <?php endif?>

                    <div class="command-buttons">
                        <button type="button" id="button-back-3" class="btn button-back btn-outline-secondary"
                            data-step_index="3">
                            <i class="fas fa-chevron-left mr-2"></i>
                            <?=lang('back')?>
                        </button>
                        <button type="button" id="button-next-3" class="btn button-next btn-dark" data-step_index="3">
                            <?=lang('next')?>
                            <i class="fas fa-chevron-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- APPOINTMENT DATA CONFIRMATION -->

                <div id="wizard-frame-4" class="wizard-frame" style="display:none;">
                    <div class="frame-container">
                        <h2 class="frame-title"><?=lang('appointment_confirmation')?></h2>
                        <div class="row frame-content">
                            <div id="appointment-details" class="col-12 col-md-6"></div>
                            <div id="customer-details" class="col-12 col-md-6"></div>
                        </div>
                        <?php if ($this->settings_model->get_setting('require_captcha') === '1'): ?>
                        <div class="row frame-content">
                            <div class="col-12 col-md-6">
                                <h4 class="captcha-title">
                                    CAPTCHA
                                    <button class="btn btn-link text-dark text-decoration-none py-0">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </h4>
                                <img class="captcha-image" src="<?=site_url('captcha')?>">
                                <input class="captcha-text form-control" type="text" value="" />
                                <span id="captcha-hint" class="help-block" style="opacity:0">&nbsp;</span>
                            </div>
                        </div>
                        <?php endif;?>
                    </div>

                    <div class="command-buttons">
                        <button type="button" id="button-back-4" class="btn button-back btn-outline-secondary"
                            data-step_index="4">
                            <i class="fas fa-chevron-left mr-2"></i>
                            <?=lang('back')?>
                        </button>
                        <form id="book-appointment-form" style="display:inline-block" method="post">
                            <button id="book-appointment-submit" type="button" class="btn btn-success">
                                <i class="fas fa-check-square mr-2"></i>
                                <?=!$manage_mode ? lang('confirm') : lang('update')?>
                            </button>
                            <input type="hidden" name="csrfToken" />
                            <input type="hidden" name="post_data" />
                        </form>
                    </div>
                </div>

                <?php }?>
                <!-- FRAME FOOTER -->

                <!-- <div id="frame-footer">
                    <input type="hidden" name="user_id" id="user_id" value="<?=$this->session->user_id;?>">

                    <small>
                        <span class="footer-options">
                            <span id="select-language" class="badge badge-secondary">
                                <i class="fas fa-language mr-2"></i>
                                <?=ucfirst(config('language'))?>
                            </span>
                            <a class="backend-link badge badge-primary" href="<?=site_url('backend');?>">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                <?=$this->session->user_id ? lang('backend_section') : lang('login')?>
                            </a>
                        </span>
                    </small>
                </div> -->
            </div>
        </div>
    </div>

    <?php if ($display_cookie_notice === '1'): ?>
    <?php require 'cookie_notice_modal.php'?>
    <?php endif?>

    <?php if ($display_terms_and_conditions === '1'): ?>
    <?php require 'terms_and_conditions_modal.php'?>
    <?php endif?>

    <?php if ($display_privacy_policy === '1'): ?>
    <?php require 'privacy_policy_modal.php'?>
    <?php endif?>

    <script>
    var GlobalVariables = {
        availableServices: <?=json_encode($available_services)?>,
        availableProviders: <?=json_encode($available_providers)?>,
        baseUrl: <?=json_encode(config('base_url'))?>,
        manageMode: <?=$manage_mode ? 'true' : 'false'?>,
        customerToken: <?=json_encode($customer_token)?>,
        dateFormat: <?=json_encode($date_format)?>,
        timeFormat: <?=json_encode($time_format)?>,
        firstWeekday: <?=json_encode($first_weekday)?>,
        displayCookieNotice: <?=json_encode($display_cookie_notice === '1')?>,
        appointmentData: <?=json_encode($appointment_data)?>,
        providerData: <?=json_encode($provider_data)?>,
        customerData: <?=json_encode($customer_data)?>,
        displayAnyProvider: <?=json_encode($display_any_provider)?>,
        csrfToken: <?=json_encode($this->security->get_csrf_hash())?>
    };

    var EALang = <?=json_encode($this->lang->language)?>;
    var availableLanguages = <?=json_encode(config('available_languages'))?>;
    </script>

    <script src="<?=asset_url('assets/js/general_functions.js')?>"></script>
    <script src="<?=asset_url('assets/ext/jquery/jquery.min.js')?>"></script>
    <script src="<?=asset_url('assets/ext/jquery-ui/jquery-ui.min.js')?>"></script>
    <script src="<?=asset_url('assets/ext/cookieconsent/cookieconsent.min.js')?>"></script>
    <script src="<?=asset_url('assets/ext/bootstrap/js/bootstrap.bundle.min.js')?>"></script>
    <script src="<?=asset_url('assets/ext/popper/popper.min.js')?>"></script>
    <script src="<?=asset_url('assets/ext/tippy/tippy-bundle.umd.min.js')?>"></script>
    <script src="<?=asset_url('assets/ext/datejs/date.min.js')?>"></script>
    <script src="<?=asset_url('assets/ext/moment/moment.min.js')?>"></script>
    <script src="<?=asset_url('assets/ext/moment/moment-timezone-with-data.min.js')?>"></script>
    <script src="<?=asset_url('assets/js/frontend_book_api.js')?>"></script>
    <script src="<?=asset_url('assets/js/frontend_book.js')?>"></script>

    <script>
    $(function() {
        FrontendBook.initialize(true, GlobalVariables.manageMode);
        GeneralFunctions.enableLanguageSelection($('#select-language'));
    });


    $("#loginUser").click(function(event) {
        $('#show_login_form').show();
        $('#register_user').hide();
    });

    $("#button-back-3").click(function(event) {
        $('#show_login_form').hide();
        $('#register_user').show();
    });


    $("#login_btn").click(function(event) {
        $.ajax({
            url: GlobalVariables.baseUrl + `/index.php/login/validation`,
            type: "POST",
            dataType: "json",
            data: $("#login_user").serialize(),
        }).done(function(data) {
            if (data.msg == 'success') {
                $("#first_name").val(data.data.first_name);
                $("#last_name").val(data.data.last_name);
                $("#phone_number").val(data.data.phone_number);
                $("#email").val(data.data.email);
                // If we are on the 3rd tab then we will need to validate the user's input before proceeding to the next
                // step.
                $("#button-next-3").click();
            }

        });
        event.preventDefault();
    });

    $("#register_btn").click(function(event) {

        $.ajax({
            url: GlobalVariables.baseUrl + `/index.php/register/validation`,
            type: "POST",
            dataType: "json",
            data: $("#register_user").serialize(),
        }).done(function(data) {
            if (data == 'success') {
                // If we are on the 3rd tab then we will need to validate the user's input before proceeding to the next
                // step.

                $("#button-next-3").click();
            }

        });
        event.preventDefault();
    });



    $('#button-next-4').click(function() {
        // Customer Details
        var firstName = GeneralFunctions.escapeHtml($("#first_name").val());
        var lastName = GeneralFunctions.escapeHtml($("#last_name").val());
        var phoneNumber = GeneralFunctions.escapeHtml($("#phone_number").val());
        var email = GeneralFunctions.escapeHtml($("#email").val());
        var address = GeneralFunctions.escapeHtml($("#address").val());
        var city = GeneralFunctions.escapeHtml($("#city").val());
        var zipCode = GeneralFunctions.escapeHtml($("#zip_code").val());

        $("#customer-details").empty();

        $("<div/>", {
            html: [
                $("<h4/>)", {
                    text: EALang.customer,
                }),
                $("<p/>", {
                    html: [
                        $("<span/>", {
                            text: EALang.customer + ": " + firstName + " " + lastName,
                        }),
                        $("<br/>"),
                        $("<span/>", {
                            text: EALang.phone_number + ": " + phoneNumber,
                        }),
                        $("<br/>"),
                        $("<span/>", {
                            text: EALang.email + ": " + email,
                        }),
                        $("<br/>"),
                        $("<span/>", {
                            text: address ? EALang.address + ": " + address : "",
                        }),
                        $("<br/>"),
                        $("<span/>", {
                            text: city ? EALang.city + ": " + city : "",
                        }),
                        $("<br/>"),
                        $("<span/>", {
                            text: zipCode ? EALang.zip_code + ": " + zipCode : "",
                        }),
                        $("<br/>"),
                    ],
                }),
            ],
        }).appendTo("#customer-details");

    });
    </script>

    <?php google_analytics_script();?>
</body>

</html>
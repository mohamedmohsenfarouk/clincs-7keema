/* ----------------------------------------------------------------------------
 * 7keema - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

(function() {

    'use strict';

    /**
     * BranchesHelper
     *
     * This class contains the methods that will be used by the "branches" tab of the page.
     *
     * @class BranchesHelper
     */
    function BranchesHelper() {
        this.filterResults = {};
        this.filterLimit = 20;
    }

    BranchesHelper.prototype.bindEventHandlers = function() {
        var instance = this;

        /**
         * Event: Filter branches Form "Submit"
         *
         * @param {jQuery.Event} event
         */
        $('#branches').on('submit', '#filter-branches form', function(event) {
            event.preventDefault();
            var key = $('#filter-branches .key').val();
            $('#filter-branches .selected').removeClass('selected');
            instance.resetForm();
            instance.filter(key);
        });

        /**
         * Event: Filter branch Cancel Button "Click"
         */
        $('#branches').on('click', '#filter-branches .clear', function() {
            $('#filter-branches .key').val('');
            instance.filter('');
            instance.resetForm();
        });

        /**
         * Event: Filter branch Row "Click"
         *
         * Display the selected branch data to the user.
         */
        $('#branches').on('click', '.branch-row', function() {
            if ($('#filter-branches .filter').prop('disabled')) {
                $('#filter-branches .results').css('color', '#AAA');
                return; // exit because we are on edit mode
            }

            var branchId = $(this).attr('data-id');

            var branch = instance.filterResults.find(function(filterResult) {
                return Number(filterResult.id) === Number(branchId);
            });

            // Add dedicated provider link.
            var dedicatedUrl = GlobalVariables.baseUrl + '/index.php?branch=' + encodeURIComponent(branch.id);
            var $link = $('<a/>', {
                'href': dedicatedUrl,
                'html': [
                    $('<span/>', {
                        'class': 'fas fa-link'
                    })
                ]
            });

            $('#branches .record-details h3')
                .find('a')
                .remove()
                .end()
                .append($link);

            instance.display(branch);
            $('#filter-branches .selected').removeClass('selected');
            $(this).addClass('selected');
            $('#edit-branch, #delete-branch').prop('disabled', false);
        });

        /**
         * Event: Add New branch Button "Click"
         */
        $('#branches').on('click', '#add-branch', function() {
            instance.resetForm();
            $('#branches .add-edit-delete-group').hide();
            $('#branches .save-cancel-group').show();
            $('#branches .record-details')
                .find('input, select, textarea')
                .prop('disabled', false);
            $('#filter-branches button').prop('disabled', true);
            $('#filter-branches .results').css('color', '#AAA');

            // Default values
            $('#branch-name').val('branch');
            $('#branch-location').val('');

        });

        /**
         * Event: Cancel branch Button "Click"
         *
         * Cancel add or edit of a branch record.
         */
        $('#branches').on('click', '#cancel-branch', function() {
            var id = $('#branch-id').val();
            instance.resetForm();
            if (id !== '') {
                instance.select(id, true);
            }
        });

        /**
         * Event: Save branch Button "Click"
         */
        $('#branches').on('click', '#save-branch', function() {
            var branch = {
                name: $('#branch-name').val(),
                location: $('#branch-location').val(),
            };

            if ($('#branch-category').val() !== 'null') {
                branch.id_branch_categories = $('#branch-category').val();
            } else {
                branch.id_branch_categories = null;
            }

            if ($('#branch-id').val() !== '') {
                branch.id = $('#branch-id').val();
            }

            if (!instance.validate()) {
                return;
            }

            instance.save(branch);
        });

        /**
         * Event: Edit branch Button "Click"
         */
        $('#branches').on('click', '#edit-branch', function() {
            $('#branches .add-edit-delete-group').hide();
            $('#branches .save-cancel-group').show();
            $('#branches .record-details')
                .find('input, select, textarea')
                .prop('disabled', false);
            $('#filter-branches button').prop('disabled', true);
            $('#filter-branches .results').css('color', '#AAA');
        });

        /**
         * Event: Delete branch Button "Click"
         */
        $('#branches').on('click', '#delete-branch', function() {
            var branchId = $('#branch-id').val();
            var buttons = [{
                    text: EALang.cancel,
                    click: function() {
                        $('#message-box').dialog('close');
                    }
                },
                {
                    text: EALang.delete,
                    click: function() {
                        instance.delete(branchId);
                        $('#message-box').dialog('close');
                    }
                }
            ];

            GeneralFunctions.displayMessageBox(EALang.delete_branch,
                EALang.delete_record_prompt, buttons);
        });
    };

    /**
     * Remove the previously registered event handlers.
     */
    BranchesHelper.prototype.unbindEventHandlers = function() {
        $('#branches')
            .off('submit', '#filter-branches form')
            .off('click', '#filter-branches .clear')
            .off('click', '.branch-row')
            .off('click', '#add-branch')
            .off('click', '#cancel-branch')
            .off('click', '#save-branch')
            .off('click', '#edit-branch')
            .off('click', '#delete-branch');
    };

    /**
     * Save branch record to database.
     *
     * @param {Object} branch Contains the branch record data. If an 'id' value is provided
     * then the update operation is going to be executed.
     */
    BranchesHelper.prototype.save = function(branch) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_branch';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            branch: JSON.stringify(branch)
        };

        $.post(url, data)
            .done(function(response) {
                Backend.displayNotification(EALang.branch_saved);
                this.resetForm();
                $('#filter-branches .key').val('');
                this.filter('', response.id, true);
            }.bind(this));
    };

    /**
     * Delete a branch record from database.
     *
     * @param {Number} id Record ID to be deleted.
     */
    BranchesHelper.prototype.delete = function(id) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_branch';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            branch_id: id
        };

        $.post(url, data)
            .done(function() {
                Backend.displayNotification(EALang.branch_deleted);

                this.resetForm();
                this.filter($('#filter-branches .key').val());
            }.bind(this));
    };

    /**
     * Validates a branch record.
     *
     * @return {Boolean} Returns the validation result.
     */
    BranchesHelper.prototype.validate = function() {
        $('#branches .has-error').removeClass('has-error');
        $('#branches .form-message')
            .removeClass('alert-danger')
            .hide();

        try {
            // Validate required fields.
            var missingRequired = false;

            $('#branches .required').each(function(index, requiredField) {
                if (!$(requiredField).val()) {
                    $(requiredField).closest('.form-group').addClass('has-error');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(EALang.fields_are_required);
            }

            // Validate the duration.
            if (Number($('#branch-duration').val()) < 5) {
                $('#branch-duration').closest('.form-group').addClass('has-error');
                throw new Error(EALang.invalid_duration);
            }

            return true;
        } catch (error) {
            $('#branches .form-message')
                .addClass('alert-danger')
                .text(error.message)
                .show();
            return false;
        }
    };

    /**
     * Resets the branch tab form back to its initial state.
     */
    BranchesHelper.prototype.resetForm = function() {
        $('#filter-branches .selected').removeClass('selected');
        $('#filter-branches button').prop('disabled', false);
        $('#filter-branches .results').css('color', '');

        $('#branches .record-details')
            .find('input, select, textarea')
            .val('')
            .prop('disabled', true);
        $('#branches .record-details h3 a').remove();

        $('#branches .add-edit-delete-group').show();
        $('#branches .save-cancel-group').hide();
        $('#edit-branch, #delete-branch').prop('disabled', true);

        $('#branches .record-details .has-error').removeClass('has-error');
        $('#branches .record-details .form-message').hide();
    };

    /**
     * Display a branch record into the branch form.
     *
     * @param {Object} branch Contains the branch record data.
     */
    BranchesHelper.prototype.display = function(branch) {
        $('#branch-id').val(branch.id);
        $('#branch-name').val(branch.name);
        $('#branch-location').val(branch.location);
    };

    /**
     * Filters branch records depending a string key.
     *
     * @param {String} key This is used to filter the branch records of the database.
     * @param {Number} selectId Optional, if set then after the filter operation the record with this
     * ID will be selected (but not displayed).
     * @param {Boolean} display Optional (false), if true then the selected record will be displayed on the form.
     */
    BranchesHelper.prototype.filter = function(key, selectId, display) {
        display = display || false;

        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_branches';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            key: key,
            limit: this.filterLimit
        };

        $.post(url, data)
            .done(function(response) {
                this.filterResults = response;

                $('#filter-branches .results').empty();

                response.forEach(function(branch, index) {
                    $('#filter-branches .results')
                        .append(BranchesHelper.prototype.getFilterHtml(branch))
                        .append($('<hr/>'))
                });

                if (response.length === 0) {
                    $('#filter-branches .results').append(
                        $('<em/>', {
                            'text': EALang.no_records_found
                        })
                    );
                } else if (response.length === this.filterLimit) {
                    $('<button/>', {
                            'type': 'button',
                            'class': 'btn btn-block btn-outline-secondary load-more text-center',
                            'text': EALang.load_more,
                            'click': function() {
                                this.filterLimit += 20;
                                this.filter(key, selectId, display);
                            }.bind(this)
                        })
                        .appendTo('#filter-branches .results');
                }

                if (selectId) {
                    this.select(selectId, display);
                }
            }.bind(this));
    };

    /**
     * Get Filter HTML
     *
     * Get a branch row HTML code that is going to be displayed on the filter results list.
     *
     * @param {Object} branch Contains the branch record data.
     *
     * @return {String} The HTML code that represents the record on the filter results list.
     */
    BranchesHelper.prototype.getFilterHtml = function(branch) {
        var name = branch.name;

        var info = branch.location;

        return $('<div/>', {
            'class': 'branch-row entry',
            'data-id': branch.id,
            'html': [
                $('<strong/>', {
                    'text': name
                }),
                $('<br/>'),
                $('<span/>', {
                    'text': info
                }),
                $('<br/>')
            ]
        });
    };

    /**
     * Select a specific record from the current filter results. If the branch id does not exist
     * in the list then no record will be selected.
     *
     * @param {Number} id The record id to be selected from the filter results.
     * @param {Boolean} display Optional (false), if true then the method will display the record on the form.
     */
    BranchesHelper.prototype.select = function(id, display) {
        display = display || false;

        $('#filter-branches .selected').removeClass('selected');

        $('#filter-branches .branch-row[data-id="' + id + '"]').addClass('selected');

        if (display) {
            var branch = this.filterResults.find(function(filterResult) {
                return Number(filterResult.id) === Number(id);
            });

            this.display(branch);

            $('#edit-branch, #delete-branch').prop('disabled', false);
        }
    };

    window.BranchesHelper = BranchesHelper;
})();
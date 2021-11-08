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

window.BackendBranches = window.BackendBranches || {};

/**
 * Backend branches
 *
 * This namespace handles the js functionality of the backend branches page.
 *
 * @module BackendBranches
 */
(function(exports) {

    'use strict';

    /**
     * Contains the basic record methods for the page.
     *
     * @type {branchesHelper}
     */
    var helper;

    var branchesHelper = new BranchesHelper();

    /**
     * Default initialize method of the page.
     *
     * @param {Boolean} [defaultEventHandlers] Optional (true), determines whether to bind the  default event handlers.
     */
    exports.initialize = function(defaultEventHandlers) {
        defaultEventHandlers = defaultEventHandlers || true;





        // Instantiate helper object (service helper by default).
        helper = branchesHelper;
        helper.resetForm();
        helper.filter('');
        helper.bindEventHandlers();

        if (defaultEventHandlers) {
            bindEventHandlers();
        }
    };

    /**
     * Binds the default event handlers of the backend branches page.
     *
     * Do not use this method if you include the "BackendBranches" namespace on another page.
     */
    function bindEventHandlers() {
        /**
         * Event: Page Tab Button "Click"
         *
         * Changes the displayed tab.
         */
        $('a[data-toggle="tab"]').on('shown.bs.tab', function() {
            if (helper) {
                helper.unbindEventHandlers();
            }

            if ($(this).attr('href') === '#branches') {
                helper = branchesHelper;
            }

            helper.resetForm();
            helper.filter('');
            helper.bindEventHandlers();
            $('.filter-key').val('');
            Backend.placeFooterToBottom();
        });
    }



})(window.BackendBranches);
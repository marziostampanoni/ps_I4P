YUI.add('moodle-enrol_supsifb-supsifbws', function(Y) {

    // initialize the YUI module
    M.enrol_supsifb = M.enro_supsifb || {};
    M.enrol_supsifb.supsifbws = {
        init: function() {
			/*
             * Register the handler function for loading the academic units and the curricula 
			 * as a callback for the 'change' event of the 'target_academic_year' select box.
             */
            Y.one('#id_target_academic_year').after('change', loadCurriculaAndAcademicUnits);

            /*
             * Register the handler function for loading the modules as a callback for
             * the 'change' event in certain select boxes.
             */
            Y.one('#id_target_academic_unit').after('change', loadModules);
            Y.one('#id_target_curricula').after('change', loadModules);

            /*
             * Register the handler function for loading the students as a callback for the
             * 'change' event of the 'target_module' select box.
             */
            Y.one('#id_target_module').after('change', loadStudents);

            // by default disable the submit button
            disableSubmitButton();
        }
    };

    /*
     * Errors handling
     */
    function showErrorMessage(message) {
        Y.one('#supsifb_sync_form').setHTML('<h3>' + message + '</h3>'); 
    }
    
    /*
     * Helper functions for creating select box options
     */

    function getChooseDots() {
        return M.util.get_string('choosedots', 'moodle');
    }

    function createOption(text, value) {
        return Y.Node.create('<option value=\'' + value + '\'>' + text + '</option>');
    }

    function createDefaultOption() {
        var dots = getChooseDots();
        return createOption(dots, dots);
    }

    /*
     * Submit button related methods.
     */

    function getSubmitButton() {
        return Y.one('#id_submitbutton');
    }

    function enableSubmitButton() {
        getSubmitButton().set('disabled', false);
    }

    function disableSubmitButton() {
        getSubmitButton().set('disabled', true);
    }

    /*
     * Loading box show/hide functions.
     */

    function showLoadingBox() {
        setNumberOfStudents('<img src="/pix/y/loading.gif" alt="loading"/>');
    }

    function hideLoadingBox() {
        // DO NOTHING
        //resetNumberOfStudents();
    }

    /*
     * Getter methods for the following fields:
     *     courseID, semester, faculty, studentType, module
     */

    function getCourseID() {
        return Y.one('input[name=id]').get('value').trim();
    }

    function getAcademicYear() {
        return Y.one('#id_target_academic_year').get('value').trim();
    }

    function getAcademicUnit() {
        return Y.one('#id_target_academic_unit').get('value').trim();
    }

    function getCurricula() {
        return Y.one('#id_target_curricula').get('value').trim();
    }

    function getModule() {
        return Y.one('#id_target_module').get('value').trim();
    }

    function getModuleText() {
        var node = Y.one('#id_target_module');
        var options = node.get('options');
        var selectedIndex = node.get('selectedIndex');

        return options.item(selectedIndex).get('text');
    }

    function isModuleSelected() {
        return getModule() != getChooseDots();
    }

    /*
     * Number of students getter and setter
     */

    function getNumberOfStudentsLabel() {
        return  Y.one('#number_of_students');
    }

    function setNumberOfStudents(value) {
        getNumberOfStudentsLabel().setHTML(value);
    }

    function resetNumberOfStudents() {
        setNumberOfStudents('0');
    }

    /*
     * Students IDs getter and setter
     */

    function getStudentsIDs() {
        return Y.one('input[name=unique_ids]');
    }

    function setStudentsIDs(value) {
        getStudentsIDs().set('value', value);
    }

    function resetStudentsIDs() {
        setStudentsIDs('');
    }

    /*
     * Module name getter and setter
     */

    function getModuleName() {
        return Y.one('input[name=target_module_name]');
    }

    function setModuleName(value) {
        getModuleName().set('value', value);
    }

    function resetModuleName() {
        setModuleName('');
    }

	function resetAcademicUnits() {
        var academicUnitsNode = Y.one('#id_target_academic_unit');
		academicUnitsNode.all('option').remove();
		academicUnitsNode.append(createDefaultOption());
	}

	function resetCurricula() {
		var curriculaNode = Y.one('#id_target_curricula');
		curriculaNode.all('option').remove();
		curriculaNode.append(createDefaultOption());
	}
	
	function resetModules() {
        var modulesNode = Y.one('#id_target_module');
		modulesNode.all('option').remove();
		modulesNode.append(createDefaultOption());
	}

    // return true if the modules can be requested to the Web Service
    function canLoadModules() {
        return getAcademicUnit() != getChooseDots()
		       && getCurricula() != getChooseDots();
    }

    // return true if the curricula and the academic units can be requested to the Web Service
    function canLoadCurriculaAndAcademicUnits() {
        return getAcademicYear() != getChooseDots();
    }

    // Validate an AJAX response
    // It returns false if the response contains the error field, true otherwise
    function isResponseValid(rawResponse) {
        var parsedResponse = JSON.parse(rawResponse);
        return (parsedResponse.error == null);
    }

    /*
     * Function used as callback for loading the curricula and the academic units
	 * for the chosen academic year.
     */
    var loadCurriculaAndAcademicUnits = function(e) {
        // UI components to update
        var academicUnitsNode = Y.one('#id_target_academic_unit');
        var curriculaNode = Y.one('#id_target_curricula');
  
        // check if the operation can be perfomed
        if (!canLoadCurriculaAndAcademicUnits()) {
			// remove the old academic units
 			resetAcademicUnits();

			// remove the old curricula
			resetCurricula();

			// remove the old modules
			resetModules();

            // remove the old students
            resetNumberOfStudents();

            // remove the old student IDs
            resetStudentsIDs();

            // disable the submit button
            disableSubmitButton();

            return false;
        }

        // AJAX request
        YUI().use("io-base", function(Y) {
            // create the request parameters
            var parameters = 'course_id=' + getCourseID();
            parameters += '&academic_year_id=' + getAcademicYear();

            // build the request
            var uri = '/enrol/supsifb/get_curricula_and_academic_units.php?' + parameters;

            // define a function to handle the response data
            function complete(id, output, args) {
                var response = output.responseText;

                // check if there are errors
                if (!isResponseValid(response)) {
                    showErrorMessage(M.util.get_string('ws_not_available', 'enrol_supsifb'));
                    return false;
                }

				// remove the old modules
				resetModules();

                // update the select boxes elements with the new values
                var responseObject = JSON.parse(response);
				var academicUnits = responseObject.academic_units;
				var curricula = responseObject.curricula;

				// add the new academic units
				resetAcademicUnits();
                for (var i = 0; i < academicUnits.length; i++) {
                    academicUnitsNode.append(createOption(academicUnits[i].name, academicUnits[i].id));
                }

				// add the new curricula
				resetCurricula();
                for (var i = 0; i < curricula.length; i++) {
                    curriculaNode.append(createOption(curricula[i].name, curricula[i].id));
                }

                // remove the old students
                resetNumberOfStudents();

                // remove the old student IDs
                resetStudentsIDs();

                // disable the submit button
                disableSubmitButton();

                // hide the loading box
                hideLoadingBox();
            };

            // Subscribe to event "io:complete", and pass an array
            // as an argument to the event handler "complete", since
            // "complete" is global.   At this point in the transaction
            // lifecycle, success or failure is not yet known.
            Y.on('io:complete', complete, Y, null);

            // do the GET request
            var request = Y.io(uri);

            // show the loading box
            showLoadingBox();
        });
    };

    /*
     * Function used as callback for loading the chosen modules.
     */
    var loadModules = function(e) {
        // UI components to update
        var modulesNode = Y.one('#id_target_module');
  
        // check if the operation can be perfomed
        if (!canLoadModules()) {
            // remove the module name
            resetModuleName();

            // remove the old modules
            modulesNode.all('option').remove();
            modulesNode.append(createDefaultOption());

            // remove the old students
            resetNumberOfStudents();

            // remove the old student IDs
            resetStudentsIDs();

            // disable the submit button
            disableSubmitButton();

            return false;
        }

        // AJAX request
        YUI().use("io-base", function(Y) {
            // create the request parameters
            var parameters = 'course_id=' + getCourseID();
            parameters += '&academic_unit_id=' + getAcademicUnit();
            parameters += '&curricula_id=' + getCurricula();

            // build the request
            var uri = '/enrol/supsifb/get_modules.php?' + parameters;

            // define a function to handle the response data
            function complete(id, output, args) {
                var response = output.responseText;

                // check if there are errors
                if (!isResponseValid(response)) {
                    showErrorMessage(M.util.get_string('ws_not_available', 'enrol_supsifb'));
                    return false;
                }

                // remove the old modules
                modulesNode.all('option').remove();

                // add the 'no module' option
                modulesNode.append(createDefaultOption());

                // update the modules select element with the new values
                var modules = JSON.parse(response);
                for (var i = 0; i < modules.length; i++) {
                    modulesNode.append(createOption(modules[i].name, modules[i].id));
                }

                // remove the old students
                resetNumberOfStudents();

                // remove the old student IDs
                resetStudentsIDs();

                // disable the submit button
                disableSubmitButton();

                // hide the loading box
                hideLoadingBox();
            };

            // Subscribe to event "io:complete", and pass an array
            // as an argument to the event handler "complete", since
            // "complete" is global.   At this point in the transaction
            // lifecycle, success or failure is not yet known.
            Y.on('io:complete', complete, Y, null);

            // do the GET request
            var request = Y.io(uri);

            // show the loading box
            showLoadingBox();
        });
    };

    /*
     * Function used as callback for loading the students of the chosen module.
     */
    var loadStudents = function(e) {
        if (!isModuleSelected()) {
            // remove the students
            resetNumberOfStudents();
            resetStudentsIDs();

            // remove the module name
            resetModuleName();

            // disable the submit button
            disableSubmitButton();

            return false;
        }

        // UI components to update
        var numberOfStudents = getNumberOfStudentsLabel()
        var studentsIDs = getStudentsIDs();

        YUI().use("io-base", function(Y) {
            // create the request parameters
            var parameters = 'course_id=' + getCourseID();
            parameters += '&module_id=' + getModule();

            // build the request
            var uri = '/enrol/supsifb/get_students.php?' + parameters;

            // define a function to handle the response data
            function complete(id, output, args) {
                var response = output.responseText;

                // check if there are errors
                if (!isResponseValid(response)) {
                    showErrorMessage(M.util.get_string('ws_not_available', 'enrol_supsifb'));
                    return false;
                }

                // update the modules element with the new values
                var students = JSON.parse(response);

                // update the number of students label
                setNumberOfStudents(students.length.toString());

                // update the students IDs hidden field
                setStudentsIDs(output.responseText);
            
                // update the module name hidden field
                setModuleName(getModuleText());

                // enable the submit button
                enableSubmitButton();

                // hide the loading box
                hideLoadingBox();
            };

            // Subscribe to event "io:complete", and pass an array
            // as an argument to the event handler "complete", since
            // "complete" is global.   At this point in the transaction
            // lifecycle, success or failure is not yet known.
            Y.on('io:complete', complete, Y, null);

            // do the GET request
            var request = Y.io(uri);

            // show the loading box
            showLoadingBox();
        });
    };  
},
'@VERSION@', {
  requires: ['node']
});

var dynamicFormURL = siteURL + 'api/index.php/dynamicForm';
var submitURL = siteURL + 'api/index.php/submitForm';
var captcha;

// create row for table showing form record
/***
* @detail: create a row for the form listing table
* @params: item:ARRAY, containing full information about the field, 
* index:NUMBER, loop index value, 
* return: HTML STRING  a table row
*/
function createRow(item, index) {

    return `
    <tr id="row_${index}">
      <th scope="row">${index}</th>
      <td  id="formRecipientEmail_${index}">${item.formRecipientEmail}</td>
      <td>${item.formName}</td>
      <td  id="formData_${index}">${item.formData}</td>
      <td> 
        <a href="javascript:void(0);" onclick= "createForm(${index}, ${item.formID})">
          Open Form
        </a>
        </td>
    </tr>`
}



/***
* @detail: create a form field while composing a form. 
* @params: field:ARRAY, containing full information about the field, 
* return: HTML STRING  a field row
*/
function createFormField(field) {
    var fieldHtml = '';
    switch (field.fieldType) {
        case 'input':
            fieldHtml = `
                    <div class="form-group">
                    <label for="${field.fieldName}">${field.fieldLabel}</label>
                    <input type="input" class="form-control rec_${field.fieldName}" id="rec_${field.fieldName}" placeholder="" name="${field.fieldName}" rule="${field.validationRule}">
                </div>`

            break;
        case 'textarea':
            fieldHtml = `
                    <div class="form-group">
                    <label for="${field.fieldName}">${field.fieldLabel}</label>
                    <textarea class="form-control rec_${field.fieldName}" id="rec_${field.fieldName}" rows="3" name="${field.fieldName}"  rule="${field.validationRule}"></textarea>
                </div>`

            break;

        case 'radio':
        case 'checkbox':
            fieldHtml = `
                    <div class="form-group">
                    <label for="${field.fieldName}">${field.fieldLabel}</label>
                    <br>
                    `
            for (const key in field.options) {
                fieldHtml += `
                        <div class="form-check form-check-inline">
                        <input class="form-check-input rec_${field.fieldName}" type="${field.fieldType}"  value = '${key}' name= "${field.fieldName}[]" rule="${field.validationRule}" />
                        <label class="form-check-label" for="gridCheck">
                         ${field.options[key]}
                        </label> </div>`;
            }
            fieldHtml += `
                     </div>`;

            break;
        case 'select':
            console.log(field.options)
            fieldHtml = `
                    <div class="form-group">
                    <label for="${field.fieldName}">${field.fieldLabel}</label>
                    <select id="rec_${field.fieldName}" class="form-select rec_${field.fieldName}" name= "${field.fieldName}"  rule="${field.validationRule}">`
            for (const key in field.options) {
                fieldHtml += `<option value = '${key}'>${field.options[key]}</option>`;
            }
            fieldHtml += `
                        </select>
                     </div>`

            break;
        default:
            fieldHtml = `
                    <div class="form-group">
                    <label for="${field.fieldName}">${field.fieldLabel}</label>
                    <input type="input" class="form-control rec_${field.fieldName}"  id="rec_${field.fieldName}" name="${field.fieldName}"  rule="${field.validationRule}">
                </div>`


    }
    return fieldHtml;

}


/***
* @detail: Create a form from database reocrd. 
* @params: index:NUMBER, loop index value, 
* formId:NUMBER, id of the form used for server side purposes
* return: Display a form on screen
*/


function createForm(index, formId) {
    // convert JSON into Object
    let html = '';
    let fieldsData = $('#formData_' + index).text();
    let data = JSON.parse(fieldsData);
    data.forEach((item, index) => {
        let newIndex = index + 1;
        html += createFormField(item, newIndex);
    })
    html += `
    <div class="form-group">
    <canvas id="canvas"></canvas>
   
    </div>
    <div class="form-group"> 
    <input name="code" placeholder="Enter captcha code here"/>
    </div>
    <input type="hidden" name="formId" value= "${formId}" /> 

    <button type="button" class="btn btn-primary mt-3" id="btnSubmitForm">
    <div class="spinner-border" style="width: 1.5rem; height: 1.5rem; display:none;" role="status" id="btnSubmitFormSpinner">
   
  </div>
    Submit</button>`;
    
    $('#dynamicFormContainer').html(html);
    $('#dynamicFormContainer,#form').show();
    $('html, body').animate({
        scrollTop: $("#dynamicFormContainer").offset().top
    }, 2000);
    captcha = new Captcha($('#canvas'));


}


/***
* @detail: validate the values against the validation rules.  
* @params: value:STRING, value to be validated, 
* validationRule:STRING, rules need to be validate
* fieldName:STRING, label of the field use to compose a error message  
* return: ARRAY status:STRING, (true or false),  message:STRING, n case of validation fail
*/

function validateValue(value, validationRule, fieldName) {
    split = validationRule.split(':');
    let status = { status: true, message: '' };
    let fieldLabel = $(`[for=${fieldName}]`).text();
    switch (split[0]) {
        case 'required':
            if (!value) {
                return { status: false, message: fieldLabel + ' is required.' }
            }
            break;
        case 'minLength':
            if (value.length < split[1]) {
                return { status: false, message: fieldLabel + ' should not be less than ' + split[1] + ' words.' }
            }
            break;
        case 'maxLength':
            if (value.length > split[1]) {
                return { status: false, message: fieldLabel + ' should not be greater than ' + split[1] + ' words.' }
            }
            break;
        case 'min':
            if (value < split[1]) {
                return { status: false, message: fieldLabel + '  value should not be greater than ' + split[1] }
            }
            break;
        case 'max':
            if (value > split[1]) {
                return { status: false, message: fieldLabel + ' value should not be less than ' + split[1] }
            }
            break;
        case 'email':
            let emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
            if (!value.match(emailRegex)) {
                return { status: false, message: fieldLabel + ' does not have a valid email address.' }
            }
            break;

    }
    return status;
}
 /***
     * @detail: extract validation from the rule attribute   
     * @params: value:STRING, the value of the input that need to be validated
     * fieldName:STRING, name of the field,     
     * return: ARRAY status:BOOLEAN,(true, false) name,  message:STRING, corresponding message
     */
function validation(value, fieldName) {

    let validationRule = $('.rec_' + fieldName).attr('rule');
    console.log({ value456: validationRule, fieldName: fieldName })
    if (!validationRule) {
        return { status: true, message: '' }
    }
    console.log({ value45: value, fieldName: fieldName })
    let validationStatus = { status: true, message: '' };
    let rules = validationRule.split('|');
    for (i = 0; i < rules.length; i++) {
        validationStatus = validateValue(value, rules[i], fieldName);
        console.log({ validationStatus: validationStatus, fieldName: fieldName })
        if (validationStatus['status'] == false) {
            break;
        }
    }
    return validationStatus;
}

$(document).ready(function () {

    $(window).on("load", function () {

        // executes when complete page is fully loaded, including all frames, objects and images
        $.ajax({
            url: dynamicFormURL,
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                if (result.status == true) {
                    var html = '';
                    result.data.forEach((item, index) => {
                        html += createRow(item, index + 1);
                    })
                    $("#tblBody").html(html);
                    $('#secForms').show();
                } else {

                    html = `
                    <div class="mt-4 p-5 bg-danger text-white rounded">
                        <div>There is no record found in database.</div>
                        <div>Please add some records via API call or click the below link for adding a test record:</div>
                        <div>
                            <a href = "http://localhost/solution/createFromRequestExample.php" target ="_blank">
                            Add data for a form in DB
                            </a>
                        </div>
                    </div>
                    `;
                    $("#secForms").html(html);
                    $('#secForms').show();
                }

                $('.loader').hide();
            }
        });
    });


    /************************ Submit form to server********************* */
    $(document).on("click", '#btnSubmitForm', function () {
        const ans = captcha.valid($('input[name="code"]').val());
        console.log({ ans: ans });
        if (ans == false) {
            $('html, body').animate({
                scrollTop: $("#dynamicFormContainer").offset().top
            }, 2000);
            $('.alert-danger').show();
            $('.alert-danger').text('Wrong Captcha Code');
            captcha.refresh();
            return false;
        }
        $('.alert').hide();
        let formArray = $('#dynamicFormContainer').serializeArray();
        $('#dynamicFormContainer input[type="checkbox"]:not(:checked)').each(function (i, e) {
            formArray.push({ name: e.getAttribute("name"), value: '', type: 'checkbox' });
        });
        $('#dynamicFormContainer input[type="radio"]:not(:checked)').each(function (i, e) {
            formArray.push({ name: e.getAttribute("name"), value: '', type: 'radio' });
        });
        console.log({ formArray: formArray })
        let validationStatus = '';
        for (j = 0; j < formArray.length; j++) {
            item = formArray[j];
            console.log({ iteml: item, formArrayformArray: formArray })
            if (item.fieldName == 'formId') {
                continue;
            }
            if (item.type == 'radio' || item.type == 'checkbox' || item.name.includes('[]')) {
                let name = item.name.replace('[]', '');
                let value = $('.rec_' + name + ':checked').val()
                console.log({ jjvalue: value })
                validationStatus = validation(value, name)
            } else {
                validationStatus = validation(item.value, item.name)
            }
            if (validationStatus.status == false) {
                break;
            }
        }
        if (validationStatus.status == false) {

            $('.alert-danger').show();
            $('.alert-danger').text(validationStatus.message);
            $('html, body').animate({
                scrollTop: $("#dynamicFormContainer").offset().top
            }, 2000);
            return false;
        }


        let formData = $('#dynamicFormContainer').serialize();
        $('#dynamicFormContainer input[type="checkbox"]:not(:checked)').each(function (i, e) {
            formData += '&' + e.getAttribute("name") + '=';
        });
        $('#dynamicFormContainer input[type="radio"]:not(:checked)').each(function (i, e) {
            formData += '&' + e.getAttribute("name") + '=';
        });

        // executes when complete page is fully loaded, including all frames, objects and images
        $.ajax({
            url: submitURL,
            type: 'POST',
            dataType: 'json',
            data: formArray,
            beforeSend: function (xhr) {
                $('#btnSubmitForm').attr('disabled', true);
                $('#btnSubmitFormSpinner').show()
            },
            complete: function (xhr) {
                $('#btnSubmitForm').attr('disabled', false);
                $('#btnSubmitFormSpinner').hide()
            },

            success: function (result) {
                if (result.status == true) {
                    $('.alert-success').show();
                    $('.alert-success').text(result.message);
                } else {
                    $('.alert-danger').show();
                    $('.alert-danger').text(result.message);
                    $('html, body').animate({
                        scrollTop: $("#dynamicFormContainer").offset().top
                    }, 1000);
                }

                $('.loader').hide();
            }
        });
    });
})
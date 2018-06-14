CRM.$(document).ready(function() {

    CRM.api3('Contributiontemplate', 'gethtml', {
        "sequential": 1,
        "templateId": templateId
    }).done(function(result) {
        var html = result.values.html;
        CKEDITOR.instances["html_template"].setData(html);
    });

});
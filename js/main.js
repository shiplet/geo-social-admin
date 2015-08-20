var addApiActive = false;


function addApiField() {
    if (!addApiActive) {
        jQuery('#geo_social_admin_entry').append('<div id="api_entry"><input type="hidden" name="admin_api_valid" value="true"/><label for="input_api_name" class="first-label">A Unique Name</label><input id="input_api_name" name="admin_api[api_name]" size="40" type="text" value=""/><label for="input_api_key">API Key</label><input id="input_api_key" name="admin_api[api_key]" size="40" type="text" value=""/><label for="input_api_secret">API Secret</label><input id="input_api_secret" name="admin_api[api_secret]" size="40" type="text" value=""/></div>');
        addApiActive = true;
    }
};

function listenForEsc() {
    jQuery(window).keyup(function(e){
            if(e.which === 27) {
                removeOverlay();
            };
        });
}

function removeOverlay() {
    jQuery('.apiEditBox').remove();
    jQuery('#geo-social-overlay').remove();
}


function watchFormFields(type) {
    var formFields = {};
    var formStrings = '';
    if (type === 'social') {
        formFields = {
            socialSource: jQuery('#input_social_source'),
            socialTitle: jQuery('#input_social_title'),
            socialUrl: jQuery('#input_social_url'),
            socialApi: jQuery('#add-api-select')
        };
        formStrings = '#input_social_source, #input_social_title, #input_social_url, #add-api-select';
    } else if (type === 'api') {
        formFields = {
            apiName: jQuery('#input_api_name'),
            apiKey: jQuery('#input_api_key'),
            apiSecret: jQuery('#input_api_secret')
        }
        formStrings = '#input_api_name, #input_api_key, #input_api_secret';
    }

    for (var key in formFields) {
        if (!formFields[key].val() || formFields[key].val() === 'init' || formFields[key].val() === 'null') {
            formFields[key].css({border: '1px solid red'});
        }
    };

    jQuery(formStrings).on('focus', function(){
        jQuery(this).css({border: '1px solid #dfdfdf'});
    });

    jQuery(formStrings).on('blur', function(){
        if (!jQuery(this).val() || jQuery(this).val() === 'init') {
            jQuery(this).css({border: '1px solid red'});
        }
    })
}


jQuery('#add-api-select').on('change', function(){
    var check = jQuery('#add-api-select').val();
    if (check === 'add_an_api') {
        addApiField();
        formStrings = '#input_social_source, #input_social_title, #input_social_url, #add-api-select';
        jQuery(formStrings).css({border: '1px solid #dfdfdf'});
    } else if (check !== "add_an_api" && addApiActive) {
        jQuery('#api_entry').remove();
        addApiActive = false;
    } else if (check === 'null') {
        jQuery('#add-api-select').find('option').removeAttr('selected');
        jQuery('#add-api-select option').find('option[value="init"]').attr('selected',true);
    }
});


jQuery('#geo-social-admin-submit').on('click', function(e){
    var socialSource = jQuery('#input_social_source').val();
    var socialTitle = jQuery('#input_social_title').val();
    var socialUrl = jQuery('#input_social_url').val();
    var socialApi = jQuery('#add-api-select').val();
    var apiName = jQuery('#input_api_name').val();
    var apiKey = jQuery('#input_api_key').val();
    var apiSecret = jQuery('#input_api_secret').val();

    if ((!socialSource || !socialTitle || !socialUrl || socialApi === 'init' || socialApi === 'null') && socialApi !== 'add_an_api') {
        e.preventDefault();
        watchFormFields('social');
        alert('Please fill in the indicated fields.');
    } else if (((!socialSource || !socialTitle || !socialUrl) && (!apiName || !apiKey || !apiSecret)) && socialApi === 'add_an_api') {
        e.preventDefault();
        watchFormFields('api');
        alert('Please fill in at least the indicated fields.');
    }
    else {
        return true;
    }
})

jQuery('.apiEdit').on('click', function(e){
    e.preventDefault();
    listenForEsc();
    var id = jQuery(this).parent().children('input').attr('data-index');
    var model = jQuery(this).parent().children('input').attr('data-model');
    if (model === 'api') {
       var apiName = jQuery(this).parent().children('p:nth-child(1)').text().split(':')[1].toString().trim();
       jQuery('body').append('<div id="geo-social-overlay" onclick="removeOverlay()"></div><div class="apiEditBox"><form method="post"><input type="hidden" name="admin_api[edit_api]" value="true"/><input type="hidden" name="admin_api[api_name_orig]" value="'+apiName+'"/><label for="input_api_name_update">API Name </label><input id="input_api_update" name="admin_api[api_name]" size="40" type="text" value="'+apiName+'"/><label for="input_api_key_update">API Key</label><input id="input_api_key_update" name="admin_api[api_key]" size="40" type="text" value=""/><label for="input_api_secret_update">API Secret</label><input id="input_api_secret_update" name="admin_api[api_secret]" size="40" type="text" value=""/><input type="hidden" name="admin_api[api_id]" value="'+id+'"/><input type="submit" action="" class="save"  value="Save Changes"/></form><div class="api-item-close update" onclick="removeOverlay()">&times;</div></div>');
   }
   if (model === 'social') {
       var assocApi = jQuery(this).parent().children('p:nth-child(4)').text().split(':')[1].toString().trim();
       var name = jQuery(this).parent().children('p:nth-child(3)').text().split(':')[1].toString().trim();
       var socialSource = jQuery(this).parent().children('p:nth-child(1)').text().split(':')[1].toString().trim();
       var url = jQuery(this).parent().children('p:nth-child(2)').text().split(' ')[1].toString().trim();
       console.log(jQuery('#api-list').children().children('.api-names').text());
       jQuery('body').append('<div id="geo-social-overlay" onclick="removeOverlay()"></div><div class="apiEditBox"><form method="post"><input type="hidden" name="admin_social[social_id]" value="'+id+'"/><input type="hidden" name="admin_social[edit_social]" value="true"/><label for="input_social_title_update">Name</label><input id="input_social_title_update" name="admin_social[social_title]" size="40" type="text" value="'+name+'"/><label for="input_social_url_update">URL</label><input id="input_social_url_update" name="admin_social[social_url]" size="40" type="text" value="'+url+'"/><label for="input_social_source_update">Source</label><input id="input_api_update" name="admin_social[social_source]" size="40" type="text" value="'+socialSource+'"/><label for="input_assoc_api_update">Associated API</label><select class="styled-select"></select><input type="submit" class="save" action=""  value="Save Changes"/></form><div class="api-item-close update" onclick="removeOverlay()">&times;</div></div>');
   }
});


jQuery('.apiDelete').on('click', function(e){
    e.preventDefault();
    listenForEsc();
    var id = jQuery(this).parent().children('input').attr('data-index');
    var model = jQuery(this).parent().children('input').attr('data-model');
    if (model === 'api') {
        jQuery('body').append('<div id="geo-social-overlay" onclick="removeOverlay()"></div><div class="apiEditBox delete"><h3>Are you sure?</h3><p>This can\'t be undone.</p><form name="deleteRow" method="post"><input type="hidden" name="admin_api[delete_item]" value="true"/><input type="hidden" name="admin_api[delete_this_item]" value="'+id+'"><input type="submit" action="" value="Yes, I want to Delete"/><div class="cancelButton" onclick="removeOverlay()">Cancel</div></form><div class="api-item-close update" onclick="removeOverlay()">&times;</div></div>');
    }
    if (model === 'social') {
        jQuery('body').append('<div id="geo-social-overlay" onclick="removeOverlay()"></div><div class="apiEditBox delete"><h3>Are you sure?</h3><p>This can\'t be undone.</p><form name="deleteRow" method="post"><input type="hidden" name="admin_social[delete_item]" value="true"/><input type="hidden" name="admin_social[delete_this_item]" value="'+id+'"><input type="submit" action="" value="Yes, I want to Delete"/><div class="cancelButton" onclick="removeOverlay()">Cancel</div></form><div class="api-item-close update" onclick="removeOverlay()">&times;</div></div>');
    }
});


// function removeApiField(input) {
//     if (apiFieldCounter === 1) {
//  apisAreActive = false;
//  jQuery('#admin_api_valid').remove();
//     }
//     apiFieldCounter = 0;
//     jQuery(input).bind('dragstart', function(e){
//  e.preventDefault();
//     }).parent().remove();
//     jQuery('.api-item-close').each(function(i){
//  jQuery(this).attr('data-index', apiFieldCounter++);
//     });
// }

// function addSocialField() {
//     if (!socialsAreActive) {
//  socialsAreActive = true;
//  jQuery('.social-box').append('<input id="admin_social_valid" type="hidden" name="admin_social_valid" value="true"/>');
//     }
//  jQuery('.social-box').append('<div class="api-item"><label for="input_social_source'+socialFieldCounter+'">Social Source</label><input id="input_social_source'+socialFieldCounter+'" name="admin_social['+socialFieldCounter+'][social_source]" size="40" type="text" value=""/><label for="input_social_url'+socialFieldCounter+'">URL</label><input id="input_social_url'+socialFieldCounter+'" name="admin_social['+socialFieldCounter+'][social_url]" size="40" type="text" value=""/><label for="input_social_title'+socialFieldCounter+'">Name</label><input id="input_social_title'+socialFieldCounter+'" name="admin_social['+socialFieldCounter+'][social_title]" size="40" type="text" value=""/><div class="api-item-close" onclick="removeSocialField(this)" data-index="'+socialFieldCounter+'">&times;</div></div>');
//     ++socialFieldCounter;
// }

// function removeSocialField(input) {
//     if (socialFieldCounter === 1) {
//  socialsAreActive = false;
//  jQuery('#admin_social_valid').remove();
//     }
//     socialFieldCounter = 0;
//     jQuery(input).bind('dragstart', function(e){
//  e.preventDefault();
//     }).parent().remove();
//     jQuery('.api-item-close').each(function(i){
//  jQuery(this).attr('data-index', socialFieldCounter++);
//     });
// }

/*
<input
 id=""
 name=""
 size=""
 type=""
 value=""
/>
*/

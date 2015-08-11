// use jQuery instead of $

var apiFieldCounter = 0,
    apisAreActive = false,
    socialFieldCounter = 0,
    socialsAreActive = false;


function addApiField() {
    if (!apisAreActive) {
	apisAreActive = true;
	jQuery('.api-box').append('<input id="admin_api_valid" type="hidden" name="admin_api_valid" value="true"/>');
    }
    
    jQuery('.api-box').append('<div class="api-item"><label for="input_api_source'+apiFieldCounter+'">API Source</label><input id="input_api_source'+apiFieldCounter+'" name="admin_api['+apiFieldCounter+'][api_source]" size="40" type="text" value=""/><label for="input_api_key'+apiFieldCounter+'">API Key</label><input id="input_api_key'+apiFieldCounter+'" name="admin_api['+apiFieldCounter+'][api_key]" size="40" type="text" value=""/><label for="input_api_secret'+apiFieldCounter+'">API Secret</label><input id="input_api_secret'+apiFieldCounter+'" name="admin_api['+apiFieldCounter+'][api_secret]" size="40" type="text" value=""/><div class="api-item-close" onclick="removeApiField(this)" data-index="'+apiFieldCounter+'">&times;</div></div>');
    ++apiFieldCounter;
};

function removeApiField(input) {
    if (apiFieldCounter === 1) {
	apisAreActive = false;
	jQuery('#admin_api_valid').remove();
    }
    apiFieldCounter = 0;
    jQuery(input).bind('dragstart', function(e){
	e.preventDefault();
    }).parent().remove();
    jQuery('.api-item-close').each(function(i){
	jQuery(this).attr('data-index', apiFieldCounter++);
    });
}

function addSocialField() {
    if (!socialsAreActive) {
	socialsAreActive = true;
	jQuery('.social-box').append('<input id="admin_social_valid" type="hidden" name="admin_social_valid" value="true"/>');
    }
	jQuery('.social-box').append('<div class="api-item"><label for="input_social_source'+socialFieldCounter+'">Social Source</label><input id="input_social_source'+socialFieldCounter+'" name="admin_social['+socialFieldCounter+'][social_source]" size="40" type="text" value=""/><label for="input_social_url'+socialFieldCounter+'">URL</label><input id="input_social_url'+socialFieldCounter+'" name="admin_social['+socialFieldCounter+'][social_url]" size="40" type="text" value=""/><label for="input_social_title'+socialFieldCounter+'">Title to Appear on Site</label><input id="input_social_title'+socialFieldCounter+'" name="admin_social['+socialFieldCounter+'][social_title]" size="40" type="text" value=""/><div class="api-item-close" onclick="removeSocialField(this)" data-index="'+socialFieldCounter+'">&times;</div></div>');
    ++socialFieldCounter;
}

function removeSocialField(input) {
    if (socialFieldCounter === 1) {
	socialsAreActive = false;
	jQuery('#admin_social_valid').remove();
    }
    socialFieldCounter = 0;
    jQuery(input).bind('dragstart', function(e){
	e.preventDefault();
    }).parent().remove();
    jQuery('.api-item-close').each(function(i){
	jQuery(this).attr('data-index', socialFieldCounter++);
    });
}

/*
<input
 id=""
 name=""
 size=""
 type=""
 value=""
/>
*/

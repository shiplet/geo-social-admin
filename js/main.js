// use jQuery instead of $

var apiFieldCounter = 0;
var apisAreActive = false;

function addApiField() {
    if (!apisAreActive) {
	apisAreActive = true;
	jQuery('.api-box').append('<input id="geo_social_valid" type="hidden" name="geo_social_valid" value="true"/>');
    }
    
    jQuery('.api-box').append('<div class="api-item"><label for="input_api_source'+apiFieldCounter+'">API Source</label><input id="input_api_source'+apiFieldCounter+'" name="geo_social['+apiFieldCounter+'][api_source]" size="40" type="text" value=""/><label for="input_api_key'+apiFieldCounter+'">API Key</label><input id="input_api_key'+apiFieldCounter+'" name="geo_social['+apiFieldCounter+'][api_key]" size="40" type="text" value=""/><label for="input_api_secret'+apiFieldCounter+'">API Secret</label><input id="input_api_secret'+apiFieldCounter+'" name="geo_social['+apiFieldCounter+'][api_secret]" size="40" type="text" value=""/><div class="api-item-close" onclick="removeApiField(this)" data-index="'+apiFieldCounter+'">&times;</div></div>');
    ++apiFieldCounter;
};

function removeApiField(input) {
    if (apiFieldCounter === 1) {
	apisAreActive = false;
	jQuery('#geo_social_valid').remove();
    }
    apiFieldCounter = 0;
    jQuery(input).bind('dragstart', function(e){
	e.preventDefault();
    }).parent().remove();
    jQuery('.api-item-close').each(function(i){
	jQuery(this).attr('data-index', apiFieldCounter++);
    });
}


// use jQuery instead of $

var apiFieldCounter = 0;

function addApiField() {
    jQuery('.api-box').append('<div class="api-item"><label for="input_api_source'+apiFieldCounter+'">API Source</label><input id="input_api_source'+apiFieldCounter+'" name="api_source['+apiFieldCounter+']" size="40" type="text" value=""/><label for="input_api_key'+apiFieldCounter+'">API Key</label><input id="input_api_key'+apiFieldCounter+'" name="api_source['+apiFieldCounter+']" size="40" type="text" value=""/><label for="input_api_secret'+apiFieldCounter+'">API Secret</label><input id="input_api_secret'+apiFieldCounter+'" name="input_api_secret['+apiFieldCounter+']" size="40" type="text" value=""/><div class="api-item-close" onclick="removeApiField()">&times;</div></div>');
    ++apiFieldCounter;
};

function removeApiField() {
    jQuery();
};
